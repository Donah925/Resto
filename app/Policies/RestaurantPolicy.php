<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Restaurant;

class RestaurantPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des restaurants.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return true; // Tous les utilisateurs peuvent voir la liste
    }

    /**
     * Déterminer si l'utilisateur peut voir un restaurant spécifique.
     */
    public function view(Utilisateur $user, Restaurant $restaurant): bool
    {
        return true; // Public
    }

    /**
     * Déterminer si l'utilisateur peut créer un restaurant.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut modifier un restaurant.
     */
    public function update(Utilisateur $user, Restaurant $restaurant): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        if ($user->hasRole('ADMIN')) {
            return $user->adminRestaurants()->where('restaurant_id', $restaurant->id)->exists();
        }

        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $user->gerantDe->id === $restaurant->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un restaurant.
     */
    public function delete(Utilisateur $user, Restaurant $restaurant): bool
    {
        return $user->hasRole(['SUPERADMIN']);
    }

    /**
     * Déterminer si l'utilisateur peut gérer le menu.
     */
    public function manageMenu(Utilisateur $user, Restaurant $restaurant): bool
    {
        return $this->update($user, $restaurant);
    }

    /**
     * Déterminer si l'utilisateur peut voir les statistiques.
     */
    public function viewStats(Utilisateur $user, Restaurant $restaurant): bool
    {
        return $this->update($user, $restaurant) || $user->hasRole(['SUPERADMIN', 'ADMIN']);
    }
}
