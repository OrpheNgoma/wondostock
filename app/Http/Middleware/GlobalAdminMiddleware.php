<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class GlobalAdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check() || ! Auth::user()->is_global_admin) {
            abort(403, 'Accès non autorisé. Seuls les administrateurs globaux peuvent accéder à cette section.');
        }

        return $next($request);
    }
}
