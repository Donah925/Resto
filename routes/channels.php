<?php

use App\Models\Utilisateur;
use App\Models\Commande;
use App\Models\Livraison;
use Illuminate\Support\Facades\Broadcast;

/*
|--------------------------------------------------------------------------
| Broadcast Channels
|--------------------------------------------------------------------------
*/

// 1. Canal du Restaurant (pour le gérant/admin)
Broadcast::channel('restaurant.{restaurantId}', function (Utilisateur $user, string $restaurantId) {
    if ($user->role->value === 'superadmin') {
        return true;
    }
    
    if ($user->role->value === 'admin') {
        // Vérifier si l'admin a accès à ce restaurant
        if ($user->profilAdmin) {
            return $user->profilAdmin->restaurants()->where('restaurants.id', $restaurantId)->exists();
        }
        return false;
    }
    
    if ($user->role->value === 'gerant') {
        return $user->profilGerant?->restaurant_id === $restaurantId;
    }
    
    return false;
});

// 2. Canal du Client (pour suivre SES commandes)
Broadcast::channel('client.{clientId}', function (Utilisateur $user, string $clientId) {
    return $user->profilClient?->id === $clientId;
});

// 3. Canal de la Livraison (pour le suivi GPS en temps réel)
Broadcast::channel('livraison.{livraisonId}', function (Utilisateur $user, string $livraisonId) {
    $livraison = Livraison::find($livraisonId);
    if (!$livraison) {
        return false;
    }

    // Le client de la commande OU le livreur assigné peuvent écouter
    $estClient = $user->profilClient?->id === $livraison->commande?->client_id;
    $estLivreur = $user->profilLivreur?->id === $livraison->livreur_id;
    $estStaff = in_array($user->role->value, ['superadmin', 'admin', 'gerant']);

    return $estClient || $estLivreur || $estStaff;
});
