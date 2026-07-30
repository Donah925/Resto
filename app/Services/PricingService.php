<?php

namespace App\Services;

use App\Models\Produit;
use App\Models\Panier;
use App\Models\CodePromo;
use App\Models\Commande;
use App\Models\Restaurant;
use App\Exceptions\CodePromoInvalideException;
use Illuminate\Support\Facades\DB;

class PricingService
{
    /**
     * Calculer le prix total d'un produit avec options
     */
    public function calculerPrixProduit(Produit $produit, array $options = []): float
    {
        $prixBase = $produit->prix;

        // Ajouter le prix des options
        if (!empty($options)) {
            foreach ($options as $optionId => $quantite) {
                $option = $produit->options()->find($optionId);
                if ($option) {
                    $prixBase += $option->prix_supplementaire * $quantite;
                }
            }
        }

        // Appliquer les réductions spécifiques au produit
        if ($produit->promotion_en_cours) {
            $prixBase *= (1 - ($produit->pourcentage_reduction ?? 0) / 100);
        }

        return round($prixBase, 2);
    }

    /**
     * Calculer le total d'un panier
     */
    public function calculerTotalPanier(Panier $panier): array
    {
        $sousTotal = 0;
        $detailsProduits = [];

        foreach ($panier->items as $item) {
            $produit = $item->produit;
            $prixUnitaire = $this->calculerPrixProduit($produit, $item->options ?? []);
            $totalLigne = $prixUnitaire * $item->quantite;

            $sousTotal += $totalLigne;

            $detailsProduits[] = [
                'produit_id' => $produit->id,
                'nom' => $produit->nom,
                'prix_unitaire' => $prixUnitaire,
                'quantite' => $item->quantite,
                'total_ligne' => $totalLigne,
                'options' => $item->options ?? [],
            ];
        }

        // Calculer la TVA
        $tva = $this->calculerTVA($sousTotal, $panier->restaurant);

        // Frais de livraison (estimés)
        $fraisLivraison = $this->estimerFraisLivraison($panier);

        // Total avant code promo
        $totalAvantPromo = $sousTotal + $tva + $fraisLivraison;

        // Application du code promo
        $reductionPromo = 0;
        $codePromoApplique = null;

        if ($panier->code_promo) {
            $resultatPromo = $this->appliquerCodePromo($panier->code_promo, $panier);
            if ($resultatPromo['valide']) {
                $reductionPromo = $resultatPromo['reduction'];
                $codePromoApplique = $panier->code_promo;
            }
        }

        $total = $totalAvantPromo - $reductionPromo;

        return [
            'sous_total' => round($sousTotal, 2),
            'tva' => round($tva, 2),
            'frais_livraison' => round($fraisLivraison, 2),
            'reduction_promo' => round($reductionPromo, 2),
            'code_promo' => $codePromoApplique,
            'total' => round(max(0, $total), 2),
            'details_produits' => $detailsProduits,
        ];
    }

    /**
     * Calculer la TVA
     */
    public function calculerTVA(float $montantHT, ?Restaurant $restaurant = null): float
    {
        // Taux de TVA par défaut pour la restauration (peut varier selon les pays)
        $tauxTVA = $restaurant?->taux_tva ?? 10.0;

        return round($montantHT * ($tauxTVA / 100), 2);
    }

    /**
     * Estimer les frais de livraison
     */
    public function estimerFraisLivraison(Panier $panier): float
    {
        if (!$panier->adresse_livraison) {
            return 0;
        }

        $restaurant = $panier->restaurant;
        $adresse = $panier->adresse_livraison;

        // Calculer la distance (nécessite un service de géolocalisation)
        $distance = $this->calculerDistanceLivraison($restaurant, $adresse);

        // Base de calcul: frais fixes + frais par km
        $fraisBase = $restaurant->frais_livraison_base ?? 3.0;
        $fraisParKm = $restaurant->frais_livraison_km ?? 1.5;

        $fraisTotaux = $fraisBase + ($distance * $fraisParKm);

        // Gratuit si montant minimum atteint
        if ($panier->getSousTotalAttribute() >= ($restaurant->seuil_livraison_gratuite ?? 50)) {
            return 0;
        }

        return round($fraisTotaux, 2);
    }

    /**
     * Calculer la distance entre restaurant et adresse
     */
    private function calculerDistanceLivraison(Restaurant $restaurant, $adresse): float
    {
        if (!$restaurant->adresse || !$adresse) {
            return 5.0; // Distance par défaut
        }

        $coordResto = $restaurant->adresse->coordonnees ?? null;
        $coordAdresse = $adresse->coordonnees ?? null;

        if (!$coordResto || !$coordAdresse) {
            return 5.0;
        }

        // Formule de Haversine
        $lat1 = deg2rad($coordResto['lat']);
        $lon1 = deg2rad($coordResto['lng']);
        $lat2 = deg2rad($coordAdresse['lat']);
        $lon2 = deg2rad($coordAdresse['lng']);

        $dLat = $lat2 - $lat1;
        $dLon = $lon2 - $lon1;

        $a = sin($dLat / 2) ** 2 + cos($lat1) * cos($lat2) * sin($dLon / 2) ** 2;
        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return 6371 * $c; // Rayon terrestre en km
    }

    /**
     * Valider et appliquer un code promo
     */
    public function appliquerCodePromo(string $code, $context): array
    {
        $codePromo = CodePromo::where('code', strtoupper($code))->first();

        if (!$codePromo) {
            throw new CodePromoInvalideException('Code promo invalide.');
        }

        // Vérifier la validité temporelle
        $now = now();
        if ($codePromo->date_debut && $codePromo->date_debut > $now) {
            throw new CodePromoInvalideException('Ce code promo n\'est pas encore actif.');
        }
        if ($codePromo->date_fin && $codePromo->date_fin < $now) {
            throw new CodePromoInvalideException('Ce code promo a expiré.');
        }

        // Vérifier le nombre d'utilisations
        if ($codePromo->nombre_utilisations_max && $codePromo->nombre_utilisations >= $codePromo->nombre_utilisations_max) {
            throw new CodePromoInvalideException('Ce code promo a atteint sa limite d\'utilisations.');
        }

        // Vérifier si l'utilisateur a déjà utilisé ce code
        if ($codePromo->usage_unique_par_client && $context instanceof Panier && $context->client) {
            $aDejaUtilise = Commande::where('client_id', $context->client->id)
                ->where('code_promo', $codePromo->code)
                ->exists();

            if ($aDejaUtilise) {
                throw new CodePromoInvalideException('Vous avez déjà utilisé ce code promo.');
            }
        }

        // Vérifier le montant minimum
        $montantPanier = $context instanceof Panier ? $context->getSousTotalAttribute() : 0;
        if ($codePromo->montant_minimum && $montantPanier < $codePromo->montant_minimum) {
            throw new CodePromoInvalideException("Montant minimum requis: {$codePromo->montant_minimum}€");
        }

        // Calculer la réduction
        $reduction = 0;
        if ($codePromo->type === 'pourcentage') {
            $reduction = $montantPanier * ($codePromo->valeur / 100);
            
            // Plafond de réduction
            if ($codePromo->reduction_max) {
                $reduction = min($reduction, $codePromo->reduction_max);
            }
        } elseif ($codePromo->type === 'fixe') {
            $reduction = $codePromo->valeur;
        }

        // La réduction ne peut pas dépasser le montant du panier
        $reduction = min($reduction, $montantPanier);

        return [
            'valide' => true,
            'reduction' => round($reduction, 2),
            'code_promo' => $codePromo,
        ];
    }

    /**
     * Calculer le prix total d'une commande
     */
    public function calculerTotalCommande(Commande $commande): array
    {
        $sousTotal = 0;

        foreach ($commande->produits as $produit) {
            $sousTotal += $produit->pivot->prix_unitaire * $produit->pivot->quantite;
        }

        $tva = $this->calculerTVA($sousTotal, $commande->restaurant);
        $fraisLivraison = $commande->frais_livraison ?? 0;
        $reductionPromo = $commande->reduction_promo ?? 0;

        $total = $sousTotal + $tva + $fraisLivraison - $reductionPromo;

        return [
            'sous_total' => round($sousTotal, 2),
            'tva' => round($tva, 2),
            'frais_livraison' => round($fraisLivraison, 2),
            'reduction_promo' => round($reductionPromo, 2),
            'total' => round(max(0, $total), 2),
        ];
    }

    /**
     * Obtenir les frais de service de la plateforme
     */
    public function calculerFraisService(float $montantTotal): float
    {
        $tauxService = 2.5; // 2.5% de frais de service
        return round($montantTotal * ($tauxService / 100), 2);
    }

    /**
     * Calculer la commission du restaurant
     */
    public function calculerCommissionRestaurant(float $montantTotal, ?Restaurant $restaurant = null): float
    {
        $tauxCommission = $restaurant?->taux_commission ?? 15.0; // 15% par défaut
        return round($montantTotal * ($tauxCommission / 100), 2);
    }

    /**
     * Vérifier si un prix est dans une fourchette acceptable
     */
    public function verifierFourchettePrix(float $prix, float $min, float $max): bool
    {
        return $prix >= $min && $prix <= $max;
    }

    /**
     * Formater un prix pour affichage
     */
    public function formaterPrix(float $prix, string $devise = 'EUR'): string
    {
        return number_format($prix, 2, ',', ' ') . ' ' . $devise;
    }
}
