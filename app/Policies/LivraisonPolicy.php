<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Livraison;

class LivraisonPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des livraisons.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT', 'LIVREUR']);
    }

    /**
     * Déterminer si l'utilisateur peut voir une livraison spécifique.
     */
    public function view(Utilisateur $user, Livraison $livraison): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le gérant peut voir les livraisons de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $livraison->commande && 
                   $livraison->commande->restaurant_id === $user->gerantDe->id;
        }

        // Le livreur peut voir ses propres livraisons
        if ($user->hasRole('LIVREUR')) {
            return $livraison->livreur_id === $user->id;
        }

        // Le client peut voir ses propres livraisons
        if ($user->hasRole('CLIENT')) {
            return $livraison->commande && 
                   $livraison->commande->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut assigner un livreur.
     */
    public function assignLivreur(Utilisateur $user, Livraison $livraison): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT']);
    }

    /**
     * Déterminer si l'utilisateur peut mettre à jour le statut.
     */
    public function updateStatus(Utilisateur $user, Livraison $livraison): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Le livreur peut mettre à jour le statut de ses livraisons
        if ($user->hasRole('LIVREUR')) {
            return $livraison->livreur_id === $user->id;
        }

        // Le gérant peut mettre à jour les livraisons de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $livraison->commande && 
                   $livraison->commande->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut accepter une livraison.
     */
    public function accept(Utilisateur $user, Livraison $livraison): bool
    {
        if ($user->hasRole('LIVREUR')) {
            return $livraison->statut === 'en_attente' && 
                   $livraison->livreur_id === null;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut marquer comme livrée.
     */
    public function markAsDelivered(Utilisateur $user, Livraison $livraison): bool
    {
        if ($user->hasRole('LIVREUR')) {
            return $livraison->livreur_id === $user->id && 
                   in_array($livraison->statut, ['en_cours', 'en_livraison']);
        }

        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut suivre en temps réel.
     */
    public function trackRealtime(Utilisateur $user, Livraison $livraison): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le livreur peut suivre sa propre livraison
        if ($user->hasRole('LIVREUR')) {
            return $livraison->livreur_id === $user->id;
        }

        // Le client peut suivre sa livraison
        if ($user->hasRole('CLIENT')) {
            return $livraison->commande && 
                   $livraison->commande->client_id === $user->profilClient->user_id;
        }

        return false;
    }
}
