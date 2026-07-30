<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Panier;

class PanierPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir son panier.
     */
    public function view(Utilisateur $user, Panier $panier): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le client ne peut voir que son propre panier
        if ($user->hasRole('CLIENT')) {
            return $panier->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut ajouter des articles au panier.
     */
    public function addItem(Utilisateur $user, Panier $panier): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $panier->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut modifier le panier.
     */
    public function update(Utilisateur $user, Panier $panier): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $panier->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut vider le panier.
     */
    public function clear(Utilisateur $user, Panier $panier): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $panier->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut appliquer un code promo.
     */
    public function applyPromo(Utilisateur $user, Panier $panier): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $panier->client_id === $user->profilClient->user_id;
        }

        return false;
    }
}
