<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RestaurantAccessMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $restaurantId = $request->route('restaurant') ?? $request->route('restaurant_id');

        if (!$restaurantId) {
            return $next($request);
        }

        // SuperAdmin : accès à tout
        if ($user->estSuperAdmin()) {
            return $next($request);
        }

        // Admin : accès aux restaurants assignés
        if ($user->role->value === 'admin') {
            $aAcces = $user->profilAdmin->restaurants()
                ->where('restaurants.id', $restaurantId)
                ->exists();

            if (!$aAcces) {
                abort(403, 'Vous n\'avez pas accès à ce restaurant.');
            }

            return $next($request);
        }

        // Gérant : accès à son restaurant uniquement
        if ($user->role->value === 'gerant') {
            if ($user->profilGerant->restaurant_id !== $restaurantId) {
                abort(403, 'Vous n\'êtes pas le gérant de ce restaurant.');
            }

            return $next($request);
        }

        abort(403);
    }
}
