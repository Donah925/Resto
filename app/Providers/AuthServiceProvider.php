<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

use App\Models\Restaurant;
use App\Models\Produit;
use App\Models\Commande;
use App\Models\Reservation;
use App\Models\Avis;
use App\Models\Panier;
use App\Models\Paiement;
use App\Models\Livraison;
use App\Models\Ticket;
use App\Models\Portefeuille;
use App\Models\CodePromo;

use App\Policies\RestaurantPolicy;
use App\Policies\ProduitPolicy;
use App\Policies\CommandePolicy;
use App\Policies\ReservationPolicy;
use App\Policies\AvisPolicy;
use App\Policies\PanierPolicy;
use App\Policies\PaiementPolicy;
use App\Policies\UtilisateurPolicy;
use App\Policies\LivraisonPolicy;
use App\Policies\TicketPolicy;
use App\Policies\PortefeuillePolicy;
use App\Policies\CodePromoPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Les policies pour l'application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Restaurant::class => RestaurantPolicy::class,
        Produit::class => ProduitPolicy::class,
        Commande::class => CommandePolicy::class,
        Reservation::class => ReservationPolicy::class,
        Avis::class => AvisPolicy::class,
        Panier::class => PanierPolicy::class,
        Paiement::class => PaiementPolicy::class,
        Livraison::class => LivraisonPolicy::class,
        Ticket::class => TicketPolicy::class,
        Portefeuille::class => PortefeuillePolicy::class,
        CodePromo::class => CodePromoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Gates globaux pour les rôles
        Gate::define('superadmin', function ($user) {
            return $user->hasRole('SUPERADMIN');
        });

        Gate::define('admin', function ($user) {
            return $user->hasRole(['SUPERADMIN', 'ADMIN']);
        });

        Gate::define('gerant', function ($user) {
            return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT']);
        });

        Gate::define('livreur', function ($user) {
            return $user->hasRole(['SUPERADMIN', 'ADMIN', 'GERANT', 'LIVREUR']);
        });

        Gate::define('client', function ($user) {
            return $user->hasRole('CLIENT');
        });

        // Gate pour la gestion multi-restaurant
        Gate::define('manage-restaurant', function ($user, $restaurantId) {
            if ($user->hasRole('SUPERADMIN')) {
                return true;
            }

            if ($user->hasRole('ADMIN')) {
                return $user->adminRestaurants()->where('restaurant_id', $restaurantId)->exists();
            }

            if ($user->hasRole('GERANT') && $user->gerantDe) {
                return $user->gerantDe->id === $restaurantId;
            }

            return false;
        });

        // Gate pour voir les statistiques
        Gate::define('view-stats', function ($user, $restaurantId = null) {
            if ($user->hasRole(['SUPERADMIN', 'ADMIN'])) {
                return true;
            }

            if ($restaurantId && $user->hasRole('GERANT') && $user->gerantDe) {
                return $user->gerantDe->id === $restaurantId;
            }

            return false;
        });
    }
}
