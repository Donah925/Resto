<?php

namespace App\Services;

use App\Models\User;
use App\Models\Commande;
use App\Models\Restaurant;
use Illuminate\Support\Facades\DB;

class LoyaltyService
{
    /**
     * Points par défaut par euro dépensé
     */
    const POINTS_PAR_EURO = 10;

    /**
     * Seuil pour un bon de réduction
     */
    const SEUIL_BON_REDUCTION = 1000;

    /**
     * Valeur d'un bon de réduction en points
     */
    const VALEUR_BON_REDUCTION = 500;

    /**
     * Ajouter des points de fidélité à un client
     */
    public function ajouterPoints(User $client, int $points, string $source, ?string $description = null): void
    {
        DB::transaction(function () use ($client, $points, $source, $description) {
            $client->points_fidelite = ($client->points_fidelite ?? 0) + $points;
            $client->save();

            // Historique des points
            $client->historiqueFidelite()->create([
                'points' => $points,
                'source' => $source,
                'description' => $description,
                'solde_apres' => $client->points_fidelite,
            ]);
        });
    }

    /**
     * Déduire des points de fidélité
     */
    public function deduirePoints(User $client, int $points, string $raison, ?string $description = null): void
    {
        if (($client->points_fidelite ?? 0) < $points) {
            throw new \InvalidArgumentException('Solde de points insuffisant.');
        }

        DB::transaction(function () use ($client, $points, $raison, $description) {
            $client->points_fidelite -= $points;
            $client->save();

            $client->historiqueFidelite()->create([
                'points' => -$points,
                'source' => $raison,
                'description' => $description,
                'solde_apres' => $client->points_fidelite,
            ]);
        });
    }

    /**
     * Calculer les points gagnés pour une commande
     */
    public function calculerPointsCommande(Commande $commande): int
    {
        $montantEligible = $commande->total - ($commande->frais_livraison ?? 0);
        return (int) ($montantEligible * self::POINTS_PAR_EURO);
    }

    /**
     * Appliquer les points d'une commande validée
     */
    public function appliquerPointsCommande(Commande $commande): void
    {
        if (!$commande->client) {
            return;
        }

        $points = $this->calculerPointsCommande($commande);
        
        $bonus = $this->calculerBonusFidelite($commande->client);
        $pointsAvecBonus = $points + $bonus;

        $this->ajouterPoints(
            $commande->client,
            $pointsAvecBonus,
            'commande',
            "Commande #{$commande->reference}" . ($bonus > 0 ? " (+{$bonus} points bonus)" : '')
        );
    }

    /**
     * Calculer un bonus de fidélité basé sur le niveau du client
     */
    private function calculerBonusFidelite(User $client): int
    {
        $niveau = $this->getNiveauFidelite($client);
        
        switch ($niveau) {
            case 'gold':
                return (int) ($this->getPointsTotalGagnes($client) * 0.05); // 5% bonus
            case 'platinum':
                return (int) ($this->getPointsTotalGagnes($client) * 0.10); // 10% bonus
            default:
                return 0;
        }
    }

    /**
     * Obtenir le niveau de fidélité d'un client
     */
    public function getNiveauFidelite(User $client): string
    {
        $pointsTotal = $this->getPointsTotalGagnes($client);

        if ($pointsTotal >= 50000) {
            return 'platinum';
        } elseif ($pointsTotal >= 20000) {
            return 'gold';
        } elseif ($pointsTotal >= 5000) {
            return 'silver';
        }

        return 'bronze';
    }

    /**
     * Obtenir le total des points gagnés (hors dépenses)
     */
    private function getPointsTotalGagnes(User $client): int
    {
        return $client->historiqueFidelite()
            ->where('points', '>', 0)
            ->sum('points');
    }

    /**
     * Vérifier si un client peut utiliser des points
     */
    public function peutUtiliserPoints(User $client, int $pointsRequis): bool
    {
        return ($client->points_fidelite ?? 0) >= $pointsRequis;
    }

    /**
     * Utiliser des points pour obtenir une réduction
     */
    public function utiliserPoints(User $client, int $points, string $raison): void
    {
        $this->deduirePoints($client, $points, 'utilisation', $raison);
    }

    /**
     * Convertir des points en bon de réduction
     */
    public function convertirPointsEnBon(User $client): ?array
    {
        $pointsRequis = self::VALEUR_BON_REDUCTION;
        
        if (!$this->peutUtiliserPoints($client, $pointsRequis)) {
            return null;
        }

        DB::transaction(function () use ($client, $pointsRequis, &$bon) {
            $this->deduirePoints(
                $client,
                $pointsRequis,
                'conversion_bon',
                'Conversion en bon de réduction'
            );

            $bon = $client->bonsReduction()->create([
                'code' => $this->genererCodeBon(),
                'type' => 'reduction_fixe',
                'valeur' => 10, // 10€ de réduction
                'points_utilises' => $pointsRequis,
                'date_expiration' => now()->addMonths(3),
                'statut' => 'actif',
            ]);
        });

        return $bon;
    }

    /**
     * Générer un code de bon de réduction unique
     */
    private function genererCodeBon(): string
    {
        return 'BON-' . strtoupper(substr(uniqid(), -8));
    }

    /**
     * Obtenir les avantages du niveau de fidélité
     */
    public function getAvantagesNiveau(string $niveau): array
    {
        $avantages = [
            'bronze' => [
                'description' => 'Niveau de base',
                'bonus_percentage' => 0,
                'livraison_gratuite_seuil' => 50,
            ],
            'silver' => [
                'description' => 'Premier niveau de fidélité',
                'bonus_percentage' => 2,
                'livraison_gratuite_seuil' => 40,
            ],
            'gold' => [
                'description' => 'Niveau intermédiaire',
                'bonus_percentage' => 5,
                'livraison_gratuite_seuil' => 30,
            ],
            'platinum' => [
                'description' => 'Niveau最高',
                'bonus_percentage' => 10,
                'livraison_gratuite_seuil' => 0,
            ],
        ];

        return $avantages[$niveau] ?? $avantages['bronze'];
    }

    /**
     * Obtenir le progrès vers le niveau supérieur
     */
    public function getProgresNiveauSuperieur(User $client): array
    {
        $pointsTotal = $this->getPointsTotalGagnes($client);
        $niveauActuel = $this->getNiveauFidelite($client);

        $seuils = [
            'bronze' => 5000,
            'silver' => 20000,
            'gold' => 50000,
            'platinum' => PHP_INT_MAX,
        ];

        $niveauSuivant = [
            'bronze' => 'silver',
            'silver' => 'gold',
            'gold' => 'platinum',
            'platinum' => null,
        ][$niveauActuel];

        if (!$niveauSuivant) {
            return [
                'niveau_actuel' => $niveauActuel,
                'niveau_suivant' => null,
                'points_manquants' => 0,
                'pourcentage' => 100,
            ];
        }

        $seuilSuivant = $seuils[$niveauActuel];
        $pointsManquants = max(0, $seuilSuivant - $pointsTotal);
        $pourcentage = min(100, ($pointsTotal / $seuilSuivant) * 100);

        return [
            'niveau_actuel' => $niveauActuel,
            'niveau_suivant' => $niveauSuivant,
            'points_manquants' => $pointsManquants,
            'pourcentage' => round($pourcentage, 2),
        ];
    }

    /**
     * Annuler les points d'une commande annulée
     */
    public function annulerPointsCommande(Commande $commande): void
    {
        if (!$commande->client) {
            return;
        }

        // Trouver l'entrée d'historique correspondante
        $historique = $commande->client->historiqueFidelite()
            ->where('source', 'commande')
            ->where('description', 'like', "%Commande #{$commande->reference}%")
            ->orderBy('created_at', 'desc')
            ->first();

        if ($historique && $historique->points > 0) {
            $this->deduirePoints(
                $commande->client,
                $historique->points,
                'annulation_commande',
                "Annulation commande #{$commande->reference}"
            );
        }
    }

    /**
     * Offrir des points de parrainage
     */
    public function offrirPointsParrainage(User $parrain, User $filleul): void
    {
        $pointsParrain = 500;
        $pointsFilleul = 200;

        $this->ajouterPoints($parrain, $pointsParrain, 'parrainage', "Parrainage de {$filleul->email}");
        $this->ajouterPoints($filleul, $pointsFilleul, 'bienvenue_parrainage', "Parrainé par {$parrain->email}");
    }
}
