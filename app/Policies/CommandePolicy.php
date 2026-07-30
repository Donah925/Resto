<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Commande;

class CommandePolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des commandes.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT', 'LIVREUR']);
    }

    /**
     * Déterminer si l'utilisateur peut voir une commande spécifique.
     */
    public function view(Utilisateur $user, Commande $commande): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le gérant peut voir les commandes de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $commande->restaurant_id === $user->gerantDe->id;
        }

        // Le livreur peut voir ses commandes assignées
        if ($user->hasRole('LIVREUR')) {
            return $commande->livreur_id === $user->id;
        }

        // Le client peut voir ses propres commandes
        if ($user->hasRole('CLIENT')) {
            return $commande->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer une commande.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole('CLIENT');
    }

    /**
     * Déterminer si l'utilisateur peut modifier une commande.
     */
    public function update(Utilisateur $user, Commande $commande): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Le gérant peut modifier les commandes de son restaurant (avant préparation)
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            if ($commande->restaurant_id !== $user->gerantDe->id) {
                return false;
            }
            return in_array($commande->statut, ['en_attente', 'confirmee']);
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut annuler une commande.
     */
    public function cancel(Utilisateur $user, Commande $commande): bool
    {
        // Le client peut annuler sa commande si elle n'est pas en préparation
        if ($user->hasRole('CLIENT')) {
            if ($commande->client_id !== $user->profilClient->user_id) {
                return false;
            }
            return in_array($commande->statut, ['en_attente', 'confirmee']);
        }

        // Le gérant peut annuler les commandes de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $commande->restaurant_id === $user->gerantDe->id;
        }

        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut changer le statut.
     */
    public function updateStatus(Utilisateur $user, Commande $commande): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Le gérant peut changer le statut des commandes de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $commande->restaurant_id === $user->gerantDe->id;
        }

        // Le livreur peut marquer comme livrée
        if ($user->hasRole('LIVREUR') && $commande->livreur_id === $user->id) {
            return in_array($commande->statut, ['en_livraison']);
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut assigner un livreur.
     */
    public function assignLivreur(Utilisateur $user, Commande $commande): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT']);
    }
}
