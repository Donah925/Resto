<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Portefeuille;

class PortefeuillePolicy
{
    /**
     * Déterminer si l'utilisateur peut voir son portefeuille.
     */
    public function view(Utilisateur $user, Portefeuille $portefeuille): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le client ne peut voir que son propre portefeuille
        if ($user->hasRole('CLIENT')) {
            return $portefeuille->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut recharger le portefeuille.
     */
    public function recharge(Utilisateur $user, Portefeuille $portefeuille): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $portefeuille->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut utiliser le solde.
     */
    public function useBalance(Utilisateur $user, Portefeuille $portefeuille): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $portefeuille->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut voir l'historique des transactions.
     */
    public function viewTransactions(Utilisateur $user, Portefeuille $portefeuille): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        if ($user->hasRole('CLIENT')) {
            return $portefeuille->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'admin peut ajuster manuellement le solde.
     */
    public function adjustBalance(Utilisateur $user, Portefeuille $portefeuille): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut transférer de l'argent.
     */
    public function transfer(Utilisateur $user, Portefeuille $portefeuille): bool
    {
        if ($user->hasRole('CLIENT')) {
            return $portefeuille->client_id === $user->profilClient->user_id;
        }

        return false;
    }
}
