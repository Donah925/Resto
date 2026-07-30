<?php

namespace App\Services;

use App\Models\Reservation;
use App\Models\Restaurant;
use App\Exceptions\ReservationIndisponibleException;
use App\Exceptions\RestaurantFermeeException;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class ReservationService
{
    /**
     * Vérifier la disponibilité d'une table
     */
    public function verifierDisponibilite(
        Restaurant $restaurant,
        int $nombrePersonnes,
        Carbon $dateHeure,
        ?int $duree = null,
        ?string $typeTable = null
    ): bool {
        // Vérifier si le restaurant est ouvert à cette heure
        if (!$this->restaurantEstOuvert($restaurant, $dateHeure)) {
            throw new RestaurantFermeeException('Le restaurant est fermé à cette heure.');
        }

        $duree = $duree ?? 120; // Durée par défaut: 2 heures
        $dateFin = $dateHeure->copy()->addMinutes($duree);

        // Vérifier les conflits de réservation
        $conflits = Reservation::where('restaurant_id', $restaurant->id)
            ->where('statut', '!=', 'annulee')
            ->where(function ($query) use ($dateHeure, $dateFin) {
                $query->where(function ($q) use ($dateHeure, $dateFin) {
                    // Réservations qui commencent avant et finissent après le début
                    $q->where('date_heure', '<=', $dateHeure)
                      ->where('date_heure_fin', '>=', $dateHeure);
                })->orWhere(function ($q) use ($dateHeure, $dateFin) {
                    // Réservations qui commencent entre le début et la fin
                    $q->where('date_heure', '>=', $dateHeure)
                      ->where('date_heure', '<', $dateFin);
                });
            })
            ->count();

        if ($conflits > 0) {
            return false;
        }

        // Vérifier la capacité du restaurant
        $capaciteTotale = $this->getCapaciteRestaurants($restaurant, $typeTable);
        $capaciteReservee = $this->getCapaciteReservee($restaurant, $dateHeure, $dateFin, $typeTable);

        return ($capaciteTotale - $capaciteReservee) >= $nombrePersonnes;
    }

    /**
     * Créer une réservation
     */
    public function creerReservation(
        Restaurant $restaurant,
        $client,
        int $nombrePersonnes,
        Carbon $dateHeure,
        ?string $typeTable = null,
        ?string $occasionSpeciale = null,
        ?string $commentaires = null,
        ?int $duree = null
    ): Reservation {
        DB::transaction(function () use (
            $restaurant,
            $client,
            $nombrePersonnes,
            $dateHeure,
            $typeTable,
            $occasionSpeciale,
            $commentaires,
            $duree,
            &$reservation
        ) {
            if (!$this->verifierDisponibilite($restaurant, $nombrePersonnes, $dateHeure, $duree, $typeTable)) {
                throw new ReservationIndisponibleException('Aucune table disponible pour ce créneau.');
            }

            $duree = $duree ?? 120;
            $dateFin = $dateHeure->copy()->addMinutes($duree);

            $reservation = Reservation::create([
                'restaurant_id' => $restaurant->id,
                'client_id' => $client->id ?? null,
                'nom_client' => $client->nom ?? null,
                'email_client' => $client->email ?? null,
                'telephone_client' => $client->telephone ?? null,
                'nombre_personnes' => $nombrePersonnes,
                'date_heure' => $dateHeure,
                'date_heure_fin' => $dateFin,
                'type_table' => $typeTable,
                'occasion_speciale' => $occasionSpeciale,
                'commentaires' => $commentaires,
                'statut' => 'confirmee',
                'code_confirmation' => $this->genererCodeConfirmation(),
            ]);
        });

        return $reservation;
    }

    /**
     * Annuler une réservation
     */
    public function annulerReservation(Reservation $reservation, ?string $motif = null): void
    {
        $reservation->update([
            'statut' => 'annulee',
            'motif_annulation' => $motif,
            'date_annulation' => Carbon::now(),
        ]);
    }

    /**
     * Modifier une réservation
     */
    public function modifierReservation(
        Reservation $reservation,
        ?int $nombrePersonnes = null,
        ?Carbon $dateHeure = null,
        ?string $typeTable = null,
        ?string $commentaires = null
    ): Reservation {
        DB::transaction(function () use (
            $reservation,
            $nombrePersonnes,
            $dateHeure,
            $typeTable,
            $commentaires,
            &$updatedReservation
        ) {
            $restaurant = $reservation->restaurant;
            $newDateHeure = $dateHeure ?? $reservation->date_heure;
            $newNombrePersonnes = $nombrePersonnes ?? $reservation->nombre_personnes;
            $newTypeTable = $typeTable ?? $reservation->type_table;

            // Vérifier la nouvelle disponibilité si la date change
            if ($dateHeure || $nombrePersonnes || $typeTable) {
                if (!$this->verifierDisponibilite(
                    $restaurant,
                    $newNombrePersonnes,
                    $newDateHeure,
                    null,
                    $newTypeTable
                )) {
                    throw new ReservationIndisponibleException('Nouveau créneau non disponible.');
                }
            }

            $updatedData = [
                'nombre_personnes' => $newNombrePersonnes,
                'date_heure' => $newDateHeure,
                'type_table' => $newTypeTable,
                'commentaires' => $commentaires ?? $reservation->commentaires,
            ];

            // Recalculer la date de fin
            $duree = $reservation->date_heure_fin->diffInMinutes($reservation->date_heure);
            $updatedData['date_heure_fin'] = $newDateHeure->copy()->addMinutes($duree);

            $reservation->update($updatedData);
            $updatedReservation = $reservation->fresh();
        });

        return $updatedReservation;
    }

    /**
     * Confirmer une réservation en attente
     */
    public function confirmerReservation(Reservation $reservation): void
    {
        $reservation->update([
            'statut' => 'confirmee',
            'date_confirmation' => Carbon::now(),
        ]);
    }

    /**
     * Marquer une réservation comme honorée
     */
    public function honorerReservation(Reservation $reservation): void
    {
        $reservation->update([
            'statut' => 'honoree',
            'date_arrivee' => Carbon::now(),
        ]);
    }

    /**
     * Marquer une réservation comme non honorée (no-show)
     */
    public function noShowReservation(Reservation $reservation, ?string $motif = null): void
    {
        $reservation->update([
            'statut' => 'no_show',
            'motif_annulation' => $motif,
        ]);
    }

    /**
     * Vérifier si un restaurant est ouvert à une date donnée
     */
    private function restaurantEstOuvert(Restaurant $restaurant, Carbon $dateHeure): bool
    {
        $jourSemaine = strtolower($dateHeure->format('l')); // e.g., 'monday'
        $heure = $dateHeure->format('H:i');

        $horaires = $restaurant->horaires_ouverture ?? [];

        if (!isset($horaires[$jourSemaine])) {
            return false;
        }

        $horaireJour = $horaires[$jourSemaine];

        if (isset($horaireJour['ferme']) && $horaireJour['ferme']) {
            return false;
        }

        $ouverture = $horaireJour['ouverture'] ?? '00:00';
        $fermeture = $horaireJour['fermeture'] ?? '23:59';

        return $heure >= $ouverture && $heure <= $fermeture;
    }

    /**
     * Obtenir la capacité totale du restaurant
     */
    private function getCapaciteRestaurants(Restaurant $restaurant, ?string $typeTable = null): int
    {
        if ($typeTable) {
            $tables = $restaurant->tables()->where('type', $typeTable)->get();
            return $tables->sum('capacite');
        }

        return $restaurant->tables()->sum('capacite');
    }

    /**
     * Obtenir la capacité déjà réservée sur un créneau
     */
    private function getCapaciteReservee(
        Restaurant $restaurant,
        Carbon $debut,
        Carbon $fin,
        ?string $typeTable = null
    ): int {
        $query = Reservation::where('restaurant_id', $restaurant->id)
            ->where('statut', '!=', 'annulee')
            ->where(function ($q) use ($debut, $fin) {
                $q->where(function ($sub) use ($debut, $fin) {
                    $sub->where('date_heure', '<=', $debut)
                        ->where('date_heure_fin', '>', $debut);
                })->orWhere(function ($sub) use ($debut, $fin) {
                    $sub->where('date_heure', '>=', $debut)
                        ->where('date_heure', '<', $fin);
                });
            });

        if ($typeTable) {
            $query->where('type_table', $typeTable);
        }

        return $query->sum('nombre_personnes');
    }

    /**
     * Générer un code de confirmation unique
     */
    private function genererCodeConfirmation(): string
    {
        return strtoupper(substr(uniqid(), -6));
    }

    /**
     * Obtenir les réservations à venir d'un client
     */
    public function getReservationsClient($client, ?int $limit = 10)
    {
        return Reservation::where('client_id', $client->id ?? null)
            ->orWhere('email_client', $client->email ?? null)
            ->whereIn('statut', ['confirmee', 'en_attente'])
            ->where('date_heure', '>=', Carbon::now())
            ->orderBy('date_heure', 'asc')
            ->limit($limit)
            ->with('restaurant')
            ->get();
    }

    /**
     * Obtenir les réservations d'un restaurant
     */
    public function getReservationsRestaurant(
        Restaurant $restaurant,
        ?Carbon $debut = null,
        ?Carbon $fin = null,
        ?string $statut = null
    ) {
        $query = Reservation::where('restaurant_id', $restaurant->id);

        if ($debut) {
            $query->where('date_heure', '>=', $debut);
        }

        if ($fin) {
            $query->where('date_heure', '<=', $fin);
        }

        if ($statut) {
            $query->where('statut', $statut);
        }

        return $query->orderBy('date_heure', 'asc')
            ->with('client')
            ->get();
    }
}
