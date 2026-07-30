<?php

namespace App\Policies;

use App\Models\Utilisateur;

class UtilisateurPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des utilisateurs.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut voir un utilisateur spécifique.
     */
    public function view(Utilisateur $user, Utilisateur $targetUser): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Un utilisateur peut voir son propre profil
        return $user->id === $targetUser->id;
    }

    /**
     * Déterminer si l'utilisateur peut créer un utilisateur.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut modifier un utilisateur.
     */
    public function update(Utilisateur $user, Utilisateur $targetUser): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // L'admin peut modifier les utilisateurs de niveau inférieur
        if ($user->hasRole('ADMIN')) {
            return !$targetUser->hasRole(['SUPERADMIN', 'ADMIN']);
        }

        // Un utilisateur peut modifier son propre profil
        return $user->id === $targetUser->id;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un utilisateur.
     */
    public function delete(Utilisateur $user, Utilisateur $targetUser): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // L'admin ne peut pas supprimer les superadmins ou autres admins
        if ($user->hasRole('ADMIN')) {
            return !$targetUser->hasRole(['SUPERADMIN', 'ADMIN']);
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut changer le rôle d'un utilisateur.
     */
    public function changeRole(Utilisateur $user, Utilisateur $targetUser): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // L'admin peut changer les rôles des utilisateurs de niveau inférieur
        if ($user->hasRole('ADMIN')) {
            return !$targetUser->hasRole(['SUPERADMIN', 'ADMIN']);
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut bannir/débannir un utilisateur.
     */
    public function ban(Utilisateur $user, Utilisateur $targetUser): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        // L'admin peut bannir les utilisateurs de niveau inférieur
        if ($user->hasRole('ADMIN')) {
            return !$targetUser->hasRole(['SUPERADMIN', 'ADMIN']);
        }

        return false;
    }
}
