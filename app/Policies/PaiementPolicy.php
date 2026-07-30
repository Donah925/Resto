<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Paiement;

class PaiementPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des paiements.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut voir un paiement spécifique.
     */
    public function view(Utilisateur $user, Paiement $paiement): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le client peut voir ses propres paiements
        if ($user->hasRole('CLIENT')) {
            return $paiement->commande && 
                   $paiement->commande->client_id === $user->profilClient->user_id;
        }

        // Le gérant peut voir les paiements des commandes de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $paiement->commande && 
                   $paiement->commande->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer un paiement.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole('CLIENT');
    }

    /**
     * Déterminer si l'utilisateur peut rembourser un paiement.
     */
    public function refund(Utilisateur $user, Paiement $paiement): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut valider un paiement manuel.
     */
    public function validate(Utilisateur $user, Paiement $paiement): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Le gérant peut valider les paiements de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $paiement->commande && 
                   $paiement->commande->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut annuler un paiement.
     */
    public function cancel(Utilisateur $user, Paiement $paiement): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }
}
