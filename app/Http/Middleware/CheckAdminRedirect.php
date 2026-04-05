<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAdminRedirect
{
    /**
     * Handle an incoming request.
     *
     * Ce middleware peut être utilisé pour des vérifications futures
     * liées aux administrateurs globaux sans redirection automatique.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Middleware désactivé - pas de redirection automatique
        // Les administrateurs globaux peuvent naviguer librement

        return $next($request);
    }
}
