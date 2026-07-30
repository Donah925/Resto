<?php

namespace App\Services;

use App\Models\Commande;
use App\Models\Panier;
use App\Models\ProfilClient;
use App\Models\CodePromo;
use App\Models\Livraison;
use App\Enums\StatutCommande;
use App\Enums\TypeCommande;
use App\Exceptions\StockInsuffisantException;
use App\Exceptions\PanierVideException;
use App\Exceptions\CodePromoInvalideException;
use App\Exceptions\RestaurantFermeeException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    public function __construct(
        private PaymentService $paymentService,
        private LoyaltyService $loyaltyService,
        private NotificationService $notificationService,
        private PricingService $pricingService,
        private DeliveryService $deliveryService
    ) {}

    /**
     * Crée une commande complète depuis un panier
     */
    public function creerDepuisPanier(ProfilClient $client, Panier $panier, array $donnees): Commande
    {
        return DB::transaction(function () use ($client, $panier, $donnees) {

            // 1. VALIDATIONS
            $this->validerPanier($panier);
            $this->verifierDisponibiliteRestaurant($panier->restaurant_id);
            $this->verifierStockProduits($panier);
            $this->verifierMontantMinimum($panier);
            $this->verifierAdresseLivraison($donnees);

            // 2. CALCUL DES TOTAUX
            $totaux = $this->pricingService->calculerTotauxCommande($panier, $donnees);

            // 3. CRÉATION DE LA COMMANDE
            $commande = Commande::create([
                'client_id' => $client->id,
                'restaurant_id' => $panier->restaurant_id,
                'type_commande' => $donnees['type_commande'],
                'adresse_livraison_id' => $donnees['adresse_livraison_id'] ?? null,
                'table_id' => $donnees['table_id'] ?? null,
                'statut' => StatutCommande::EN_ATTENTE,
                'sous_total' => $totaux['sous_total'],
                'montant_tva' => $totaux['tva'],
                'frais_livraison' => $totaux['frais_livraison'],
                'montant_reduction' => $totaux['reduction'],
                'montant_total' => $totaux['total'],
                'notes_client' => $donnees['notes_client'] ?? null,
                'code_promo_id' => $totaux['code_promo_id'] ?? null,
                'temps_preparation_estime' => $this->estimerTempsPreparation($panier),
            ]);

            // 4. TRANSFERT DES LIGNES
            $this->transfererLignesPanier($panier, $commande);

            // 5. GESTION DU PAIEMENT
            $paiement = $this->paymentService->initierPaiement($commande, $donnees);

            // 6. CRÉER LIVRAISON SI NÉCESSAIRE
            if ($commande->type_commande === TypeCommande::LIVRAISON) {
                $this->creerLivraison($commande, $totaux['frais_livraison']);
            }

            // 7. DÉCRÉMENTER LE STOCK
            $this->decrementerStock($commande);

            // 8. ENREGISTRER UTILISATION CODE PROMO
            if ($totaux['code_promo_id']) {
                $this->enregistrerUtilisationCodePromo($commande, $totaux['reduction']);
            }

            // 9. VIDER LE PANIER
            $panier->lignes()->delete();
            $panier->delete();

            // 10. NOTIFICATIONS
            $this->notifierCreationCommande($commande);

            Log::info("Commande créée: {$commande->numero}", [
                'client_id' => $client->id,
                'montant' => $commande->montant_total,
            ]);

            return $commande->load(['lignes.produit', 'paiement', 'livraison']);
        });
    }

    /**
     * Change le statut d'une commande
     */
    public function changerStatut(Commande $commande, StatutCommande $nouveauStatut, ?string $commentaire = null, $modifiePar = null): void
    {
        DB::transaction(function () use ($commande, $nouveauStatut, $commentaire, $modifiePar) {

            // Valider la transition
            $this->validerTransitionStatut($commande, $nouveauStatut);

            $ancienStatut = $commande->statut;
            $commande->update(['statut' => $nouveauStatut]);

            // Historique
            $commande->historiqueStatuts()->create([
                'statut' => $nouveauStatut->value,
                'commentaire' => $commentaire,
                'modifie_par_id' => $modifiePar?->id,
            ]);

            // Actions spécifiques selon le statut
            $this->executerActionsPostStatut($commande, $ancienStatut, $nouveauStatut);

            // Notifications
            $this->notificationService->notifierChangementStatutCommande($commande, $ancienStatut, $nouveauStatut);
        });
    }

    /**
     * Annule une commande
     */
    public function annuler(Commande $commande, string $raison, $annulePar = null): void
    {
        if (!$commande->estAnnulable()) {
            throw new \Exception('Cette commande ne peut plus être annulée');
        }

        DB::transaction(function () use ($commande, $raison, $annulePar) {

            // Rembourser si payé
            if ($commande->paiement && $commande->paiement->estReussi()) {
                $this->paymentService->rembourser($commande->paiement, $raison);
            }

            // Remettre le stock
            $this->remettreStock($commande);

            // Annuler la livraison si existe
            if ($commande->livraison) {
                $commande->livraison->update(['statut' => 'echouee']);
            }

            // Changer le statut
            $commande->changerStatut(StatutCommande::ANNULEE, $raison, $annulePar);

            // Notifier
            $this->notificationService->notifierAnnulationCommande($commande, $raison);

            Log::info("Commande annulée: {$commande->numero}", ['raison' => $raison]);
        });
    }

    /**
     * Termine une commande (après livraison)
     */
    public function terminer(Commande $commande): void
    {
        DB::transaction(function () use ($commande) {
            $commande->changerStatut(StatutCommande::TERMINEE);

            // Attribuer les points de fidélité
            $pointsGagnes = (int) floor($commande->montant_total / 100);
            $this->loyaltyService->attribuerPoints(
                $commande->client,
                $pointsGagnes,
                "Points gagnés sur la commande #{$commande->numero}",
                $commande
            );

            // Mettre à jour les stats client
            $commande->client->increment('nombre_commandes');
            $commande->client->increment('total_depense', $commande->montant_total);
            $commande->client->update(['derniere_commande_le' => now()]);

            // Demander un avis
            $this->notificationService->demanderAvisClient($commande);
        });
    }

    // ===== MÉTHODES PRIVÉES =====

    private function validerPanier(Panier $panier): void
    {
        if ($panier->lignes->isEmpty()) {
            throw new PanierVideException('Votre panier est vide');
        }
    }

    private function verifierDisponibiliteRestaurant(string $restaurantId): void
    {
        $restaurant = \App\Models\Restaurant::find($restaurantId);

        if (!$restaurant) {
            throw new \Exception('Restaurant introuvable');
        }

        if (!$restaurant->estOuvertMaintenant()) {
            throw new RestaurantFermeeException('Ce restaurant est actuellement fermé');
        }

        if ($restaurant->statut !== 'actif') {
            throw new \Exception('Ce restaurant n\'est pas disponible');
        }
    }

    private function verifierStockProduits(Panier $panier): void
    {
        foreach ($panier->lignes as $ligne) {
            $produit = $ligne->produit;

            if (!$produit->est_disponible) {
                throw new \Exception("Le produit '{$produit->getTranslation('nom')}' n'est plus disponible");
            }

            if ($produit->quantite_stock !== null && $produit->quantite_stock < $ligne->quantite) {
                throw new StockInsuffisantException(
                    "Stock insuffisant pour '{$produit->getTranslation('nom')}'. Disponible : {$produit->quantite_stock}"
                );
            }
        }
    }

    private function verifierMontantMinimum(Panier $panier): void
    {
        $restaurant = $panier->restaurant;

        if ($panier->sous_total < $restaurant->montant_minimum_commande) {
            throw new \Exception(
                "Le montant minimum de commande est de {$restaurant->montant_minimum_commande} FCFA"
            );
        }
    }

    private function verifierAdresseLivraison(array $donnees): void
    {
        if ($donnees['type_commande'] === 'livraison' && empty($donnees['adresse_livraison_id'])) {
            throw new \Exception('Adresse de livraison requise');
        }

        if (!empty($donnees['adresse_livraison_id'])) {
            $adresse = \App\Models\Adresse::find($donnees['adresse_livraison_id']);
            if (!$adresse) {
                throw new \Exception('Adresse de livraison introuvable');
            }
        }
    }

    private function transfererLignesPanier(Panier $panier, Commande $commande): void
    {
        foreach ($panier->lignes as $ligne) {
            $commandeLigne = $commande->lignes()->create([
                'produit_id' => $ligne->produit_id,
                'variante_id' => $ligne->variante_id,
                'nom_produit' => $ligne->produit->getTranslations('nom'),
                'quantite' => $ligne->quantite,
                'prix_unitaire' => $ligne->prix_unitaire,
                'modificateur_prix' => $ligne->variante?->modificateur_prix ?? 0,
                'notes' => $ligne->notes,
            ]);

            // Transférer les options (suppléments)
            if ($ligne->options) {
                foreach ($ligne->options as $option) {
                    $commandeLigne->options()->create([
                        'supplement_id' => $option['id'] ?? null,
                        'nom_supplement' => json_encode(['fr' => $option['nom'] ?? '']),
                        'prix' => $option['prix'] ?? 0,
                    ]);
                }
            }
        }
    }

    private function creerLivraison(Commande $commande, float $fraisLivraison): void
    {
        Livraison::create([
            'commande_id' => $commande->id,
            'statut' => 'en_attente_livreur',
            'commission_livreur' => $fraisLivraison * 0.7, // 70% pour le livreur
        ]);
    }

    private function decrementerStock(Commande $commande): void
    {
        foreach ($commande->lignes as $ligne) {
            $produit = $ligne->produit;
            if ($produit->quantite_stock !== null) {
                $produit->decrement('quantite_stock', $ligne->quantite);
            }
        }
    }

    private function remettreStock(Commande $commande): void
    {
        foreach ($commande->lignes as $ligne) {
            $produit = $ligne->produit;
            if ($produit->quantite_stock !== null) {
                $produit->increment('quantite_stock', $ligne->quantite);
            }
        }
    }

    private function enregistrerUtilisationCodePromo(Commande $commande, float $reduction): void
    {
        \App\Models\UtilisationCodePromo::create([
            'code_promo_id' => $commande->code_promo_id,
            'commande_id' => $commande->id,
            'client_id' => $commande->client_id,
            'montant_reduction' => $reduction,
        ]);
    }

    private function estimerTempsPreparation(Panier $panier): int
    {
        $tempsMax = $panier->lignes->max(fn($l) => $l->produit->temps_preparation ?? 15);
        return $tempsMax + 10; // 10 min de marge
    }

    private function notifierCreationCommande(Commande $commande): void
    {
        // Notifier le restaurant
        $this->notificationService->notifierRestaurantNouvelleCommande($commande);

        // Notifier le client
        $this->notificationService->confirmerCommandeClient($commande);
    }

    private function executerActionsPostStatut(Commande $commande, $ancienStatut, $nouveauStatut): void
    {
        match($nouveauStatut) {
            StatutCommande::CONFIRMEE => $this->onCommandeConfirmee($commande),
            StatutCommande::PRETE => $this->onCommandePrete($commande),
            StatutCommande::LIVREE => $this->onCommandeLivree($commande),
            StatutCommande::TERMINEE => $this->terminer($commande),
            default => null,
        };
    }

    private function onCommandeConfirmee(Commande $commande): void
    {
        // Si livraison, déclencher assignation livreur
        if ($commande->type_commande === TypeCommande::LIVRAISON && $commande->livraison) {
            $this->deliveryService->declencherAssignationLivreur($commande->livraison);
        }
    }

    private function onCommandePrete(Commande $commande): void
    {
        // Notifier le client que sa commande est prête
        $this->notificationService->notifierCommandePrete($commande);
    }

    private function onCommandeLivree(Commande $commande): void
    {
        $commande->update(['heure_livraison_effective' => now()]);
    }

    public function validerTransitionStatut(Commande $commande, StatutCommande $nouveauStatut): void
    {
        $transitionsAutorisees = [
            StatutCommande::EN_ATTENTE->value => ['confirmee', 'annulee'],
            StatutCommande::CONFIRMEE->value => ['en_preparation', 'annulee'],
            StatutCommande::EN_PREPARATION->value => ['prete'],
            StatutCommande::PRETE->value => ['en_livraison'],
            StatutCommande::EN_LIVRAISON->value => ['livree'],
            StatutCommande::LIVREE->value => ['terminee'],
        ];

        $statutActuel = $commande->statut->value;
        $transitionsPossibles = $transitionsAutorisees[$statutActuel] ?? [];

        if (!in_array($nouveauStatut->value, $transitionsPossibles)) {
            throw new \Exception(
                "Transition impossible de '{$statutActuel}' vers '{$nouveauStatut->value}'"
            );
        }
    }
}
