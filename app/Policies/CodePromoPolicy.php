<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\CodePromo;

class CodePromoPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des codes promo.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT']);
    }

    /**
     * Déterminer si l'utilisateur peut voir un code promo spécifique.
     */
    public function view(Utilisateur $user, CodePromo $codePromo): bool
    {
        if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
            return true;
        }

        // Le gérant peut voir les codes de son restaurant (ou globaux)
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $codePromo->restaurant_id === null || 
                   $codePromo->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut créer un code promo.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut modifier un code promo.
     */
    public function update(Utilisateur $user, CodePromo $codePromo): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        if ($user->hasRole('ADMIN')) {
            return true;
        }

        // Le gérant peut modifier les codes de son restaurant
        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $codePromo->restaurant_id === $user->gerantDe->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un code promo.
     */
    public function delete(Utilisateur $user, CodePromo $codePromo): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut activer/désactiver un code promo.
     */
    public function toggle(Utilisateur $user, CodePromo $codePromo): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut utiliser un code promo.
     */
    public function use(Utilisateur $user, CodePromo $codePromo): bool
    {
        if (!$codePromo->actif) {
            return false;
        }

        // Vérifier si le code est limité à certains utilisateurs
        if ($codePromo->usage_limite_par_utilisateur) {
            // Logique de vérification du nombre d'utilisations par utilisateur
            // sera faite dans le service
        }

        return $user->hasRole('CLIENT');
    }
}
