<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Usage dans les routes : ->middleware('role:admin,pharmacien')
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user || ! $user->isActive()) {
            abort(403, 'Compte inactif ou non authentifié.');
        }

        if (! $user->hasRole(...$roles)) {
            abort(403, "Vous n'avez pas accès à cette page.");
        }

        return $next($request);
    }
}
