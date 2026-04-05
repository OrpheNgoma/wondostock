<?php

namespace App\Http\Middleware;

use App\Exceptions\FeatureBlockedException;
use App\Services\FeatureLockService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckFeatureLock
{
    protected FeatureLockService $featureLockService;

    public function __construct(FeatureLockService $featureLockService)
    {
        $this->featureLockService = $featureLockService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $featureKey  La clé de la fonctionnalité à vérifier
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $company = Auth::user()?->company;

        // Vérifier si la fonctionnalité est verrouillée
        if ($this->featureLockService->isFeatureLocked($company, $featureKey)) {
            $message = $this->featureLockService->getFeatureLockMessage($featureKey, $company);

            // Si c'est une requête AJAX/API, retourner JSON
            if ($request->expectsJson()) {
                return response()->json([
                    'error' => 'Fonctionnalité verrouillée',
                    'message' => $message,
                    'feature' => $featureKey,
                ], 403);
            }

            // Sinon, lancer l'exception pour redirection vers une page d'erreur
            throw new FeatureBlockedException($featureKey, $message);
        }

        return $next($request);
    }
}
