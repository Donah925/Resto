<?php

namespace App\Policies;

use App\Models\Utilisateur;
use App\Models\Produit;

class ProduitPolicy
{
    /**
     * Déterminer si l'utilisateur peut voir la liste des produits.
     */
    public function viewAny(Utilisateur $user): bool
    {
        return true; // Public
    }

    /**
     * Déterminer si l'utilisateur peut voir un produit spécifique.
     */
    public function view(Utilisateur $user, Produit $produit): bool
    {
        return $produit->visible || $this->update($user, $produit);
    }

    /**
     * Déterminer si l'utilisateur peut créer un produit.
     */
    public function create(Utilisateur $user): bool
    {
        return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT']);
    }

    /**
     * Déterminer si l'utilisateur peut modifier un produit.
     */
    public function update(Utilisateur $user, Produit $produit): bool
    {
        if ($user->hasRole('SUPERADMIN')) {
            return true;
        }

        $restaurant = $produit->restaurant;

        if ($user->hasRole('ADMIN')) {
            return $user->adminRestaurants()->where('restaurant_id', $restaurant->id)->exists();
        }

        if ($user->hasRole('GERANT') && $user->gerantDe) {
            return $user->gerantDe->id === $restaurant->id;
        }

        return false;
    }

    /**
     * Déterminer si l'utilisateur peut supprimer un produit.
     */
    public function delete(Utilisateur $user, Produit $produit): bool
    {
        return $this->update($user, $produit);
    }

    /**
     * Déterminer si l'utilisateur peut gérer les stocks.
     */
    public function manageStock(Utilisateur $user, Produit $produit): bool
    {
        return $this->update($user, $produit);
    }
}
