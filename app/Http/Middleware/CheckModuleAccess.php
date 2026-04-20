<?php

namespace App\Http\Middleware;

use App\Services\ModuleService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckModuleAccess
{
    public function __construct(private ModuleService $moduleService) {}

    public function handle(Request $request, Closure $next, string $moduleKey): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->company) {
            abort(403, 'Accès refusé.');
        }

        if (! $this->moduleService->isEnabled($user->company, $moduleKey)) {
            abort(403, "Le module « {$moduleKey} » n'est pas activé pour votre entreprise.");
        }

        return $next($request);
    }
}
