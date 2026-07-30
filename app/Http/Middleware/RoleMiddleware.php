<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!$request->user()) {
            return redirect()->route('login');
        }

        if (!in_array($request->user()->role->value, $roles)) {
            abort(403, 'Accès non autorisé pour votre rôle.');
        }

        return $next($request);
    }
}
