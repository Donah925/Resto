<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Avis;

class AvisPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des avis.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return true; // Public
    }

    /**
     * Déterminer si l'utilisateur peut voir un avis spécifique.
     */
    public function view(Utilisateur $user, Avis $avis): bool
    {
        return true; // Public
    }

    /**
     * Déterminer si l'utilisateur peut créer un avis.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole('CLIENT');
    }

    /**
     * Déterminer si l'utilisateur peut modifier un avis.
     */
    public function update(Utilisateur $user, Avis $avis): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Seul l'auteur peut modifier son avis
        if ($user->hasRole('CLIENT')) {
            return $avis->client_id === $user->profilClient->user_id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un avis.
     */
    public function delete(Utilisateur $user, Avis $avis): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // L'auteur peut supprimer son propre avis
        if ($user->hasRole('CLIENT')) {
            return $avis->client_id === $user->profilClient->user_id;
        }

        // Le gérant du restaurant concerné peut supprimer les avis de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $avis->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut signaler un avis.
     */
    public function report(Utilisateur $user, Avis $avis): bool
    {
        return $user->hasRole('CLIENT');
    }

    /**
     * Déterminer si l'utilisateur peut répondre à un avis.
     */
    public function reply(Utilisateur $user, Avis $avis): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // Le gérant du restaurant peut répondre aux avis
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $avis->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }
}
