<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class LivreurDisponibleMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $profil = $request->user()->profilLivreur;

        if (!$profil || !$profil->est_disponible) {
            return response()->json([
                'message' => 'Vous devez être disponible pour effectuer cette action.',
            ], 403);
        }

        return $next($request);
    }
}
