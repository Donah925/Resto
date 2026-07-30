<?php

namespace App\Services;

use App\Models\Restaurant;
use App\Models\Livreur;
use App\Models\Commande;
use App\Models\Adresse;
use App\Exceptions\LivraisonNonAssigneeException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class DeliveryService
{
    /**
     * Assigner un livreur à une commande
     */
    public function assignLivreur(Commande $commande): ?Livreur
    {
        if ($commande->statut !== 'confirmee') {
            throw new LivraisonNonAssigneeException('La commande doit être confirmée avant assignation.');
        }

        $livreur = $this->trouverLivreurDisponible($commande->adresse_livraison);
        
        if (!$livreur) {
            throw new LivraisonNonAssigneeException('Aucun livreur disponible dans cette zone.');
        }

        DB::transaction(function () use ($commande, $livreur) {
            $commande->livreur_id = $livreur->id;
            $commande->statut = 'en_livraison';
            $commande->save();

            $livreur->statut = 'occupe';
            $livreur->save();
        });

        return $livreur;
    }

    /**
     * Trouver un livreur disponible près de l'adresse
     */
    public function trouverLivreurDisponible(Adresse $adresse): ?Livreur
    {
        return Livreur::where('statut', 'disponible')
            ->whereHas('vehicule', function ($query) {
                $query->where('statut', 'actif');
            })
            ->with('vehicule')
            ->get()
            ->sortBy(function ($livreur) use ($adresse) {
                return $this->calculerDistance($livreur, $adresse);
            })
            ->first();
    }

    /**
     * Calculer la distance entre un livreur et une adresse
     */
    private function calculerDistance(Livreur $livreur, Adresse $adresse): float
    {
        if (!$livreur->localisation_actuelle || !$adresse->coordonnees) {
            return PHP_FLOAT_MAX;
        }

        $lat1 = $livreur->localisation_actuelle['lat'];
        $lon1 = $livreur->localisation_actuelle['lng'];
        $lat2 = $adresse->coordonnees['lat'];
        $lon2 = $adresse->coordonnees['lng'];

        return $this->haversine($lat1, $lon1, $lat2, $lon2);
    }

    /**
     * Formule de Haversine pour calculer la distance
     */
    private function haversine($lat1, $lon1, $lat2, $lon2): float
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat / 2) * sin($dLat / 2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon / 2) * sin($dLon / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Mettre à jour la position d'un livreur
     */
    public function updatePosition(Livreur $livreur, array $coordinates): void
    {
        $livreur->update([
            'localisation_actuelle' => $coordinates,
            'derniere_position' => Carbon::now()
        ]);
    }

    /**
     * Marquer une livraison comme terminée
     */
    public function terminerLivraison(Commande $commande): void
    {
        DB::transaction(function () use ($commande) {
            $commande->statut = 'livree';
            $commande->date_livraison = Carbon::now();
            $commande->save();

            if ($commande->livreur) {
                $commande->livreur->statut = 'disponible';
                $commande->livreur->save();
            }
        });
    }

    /**
     * Annuler une livraison
     */
    public function annulerLivraison(Commande $commande): void
    {
        DB::transaction(function () use ($commande) {
            if ($commande->livreur) {
                $commande->livreur->statut = 'disponible';
                $commande->livreur->save();
            }

            $commande->statut = 'annulee';
            $commande->livreur_id = null;
            $commande->save();
        });
    }

    /**
     * Estimer le temps de livraison
     */
    public function estimerTempsLivraison(Adresse $depart, Adresse $arrivee): int
    {
        $distance = $this->calculerDistanceFromCoords(
            $depart->coordonnees,
            $arrivee->coordonnees
        );

        // Vitesse moyenne estimée: 30 km/h en ville
        $tempsHeures = $distance / 30;
        
        // Ajouter 10 minutes pour préparation
        return (int) ($tempsHeures * 60) + 10;
    }

    /**
     * Calculer la distance entre deux coordonnées
     */
    private function calculerDistanceFromCoords(?array $coord1, ?array $coord2): float
    {
        if (!$coord1 || !$coord2) {
            return 5.0; // Distance par défaut
        }

        return $this->haversine(
            $coord1['lat'],
            $coord1['lng'],
            $coord2['lat'],
            $coord2['lng']
        );
    }

    /**
     * Obtenir l'historique des livraisons d'un livreur
     */
    public function getHistoriqueLivraisons(Livreur $livreur, ?int $limit = 50)
    {
        return Commande::where('livreur_id', $livreur->id)
            ->whereIn('statut', ['livree', 'annulee'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->with(['restaurant', 'client'])
            ->get();
    }

    /**
     * Calculer les statistiques d'un livreur
     */
    public function getStatistiquesLivreur(Livreur $livreur, ?Carbon $debut = null, ?Carbon $fin = null): array
    {
        $query = Commande::where('livreur_id', $livreur->id)
            ->where('statut', 'livree');

        if ($debut) {
            $query->where('created_at', '>=', $debut);
        }

        if ($fin) {
            $query->where('created_at', '<=', $fin);
        }

        $commandes = $query->get();

        return [
            'total_livraisons' => $commandes->count(),
            'revenu_total' => $commandes->sum('frais_livraison'),
            'pourboires_total' => $commandes->sum('pourboire'),
            'duree_moyenne' => $commandes->avg(
                fn($c) => $c->date_livraison ? $c->date_livraison->diffInMinutes($c->created_at) : 0
            ),
        ];
    }
}
