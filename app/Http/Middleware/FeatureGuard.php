<?php

namespace App\Http\Middleware;

use App\Enums\FeatureEnum;
use App\Services\FeatureLockService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware pour vérifier les verrouillages de fonctionnalités.
 *
 * Ce middleware protège les routes en vérifiant si la fonctionnalité
 * correspondante est verrouillée pour l'entreprise de l'utilisateur.
 */
class FeatureGuard
{
    public function __construct(
        private FeatureLockService $featureLockService
    ) {
        //
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        // Vérifier si l'utilisateur est authentifié
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Bypass pour les admins globaux
        if ($user->is_global_admin || $user->hasRole('Global-Admin')) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a une entreprise
        if (! $user->company) {
            abort(403, 'Aucune entreprise associée à votre compte.');
        }

        // Vérifier si la fonctionnalité est verrouillée
        $isLocked = $this->featureLockService->isFeatureLocked($user->company, $featureKey);

        if ($isLocked) {
            // Pour les requêtes AJAX/Livewire, retourner une réponse JSON
            if ($request->expectsJson() || $request->header('X-Livewire')) {
                return response()->json([
                    'error' => 'Fonctionnalité verrouillée',
                    'message' => 'Cette fonctionnalité est temporairement indisponible pour votre entreprise.',
                    'feature_key' => $featureKey,
                ], 403);
            }

            // Pour les requêtes normales, rediriger vers une page d'erreur
            return $this->handleLockedFeature($request, $featureKey, $user->company);
        }

        return $next($request);
    }

    /**
     * Gère l'affichage d'une fonctionnalité verrouillée.
     */
    private function handleLockedFeature($request, string $featureKey, $company): Response
    {
        // Récupérer les détails du verrouillage
        $lock = $company->featureLocks()
            ->where('feature_key', $featureKey)
            ->where('is_locked', true)
            ->first();

        $featureEnum = FeatureEnum::tryFrom($featureKey);
        $featureDescription = $featureEnum ? $featureEnum->getDescription() : $featureKey;

        // Si on a les informations du verrouillage, les passer à la vue
        $lockInfo = $lock ? [
            'reason' => $lock->reason,
            'locked_at' => $lock->locked_at,
            'expires_at' => $lock->expires_at,
        ] : null;

        return response()->view('errors.feature-locked', [
            'feature_key' => $featureKey,
            'feature_description' => $featureDescription,
            'company' => $company,
            'lock_info' => $lockInfo,
        ], 403);
    }
}
