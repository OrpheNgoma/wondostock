<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Initialise le contexte "team" de Spatie Laravel Permission pour chaque requête
 * (page HTTP classique ou requête AJAX Livewire). Doit s'exécuter après que
 * TenantIsolation ait validé l'existence et l'activité de l'entreprise.
 */
class InitializePermissionsTeam
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && Auth::user()->company_id) {
            setPermissionsTeamId(Auth::user()->company_id);
        }

        return $next($request);
    }
}
