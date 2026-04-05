<?php

namespace App\Traits;

use App\Services\FeatureLockService;
use Illuminate\Support\Facades\Auth;

/**
 * Trait pour vérifier les verrouillages de fonctionnalités dans les composants Livewire
 */
trait ChecksFeatureLocks
{
    protected FeatureLockService $featureLockService;

    /**
     * Initialiser le service de verrouillages
     */
    public function bootChecksFeatureLocks(): void
    {
        $this->featureLockService = app(FeatureLockService::class);
    }

    /**
     * Vérifier si une fonctionnalité est accessible
     */
    protected function isFeatureAccessible(string $featureKey): bool
    {
        if (! isset($this->featureLockService)) {
            $this->featureLockService = app(FeatureLockService::class);
        }

        return $this->featureLockService->isFeatureAccessible(Auth::user()?->company, $featureKey);
    }

    /**
     * Vérifier si une fonctionnalité est verrouillée
     */
    protected function isFeatureLocked(string $featureKey): bool
    {
        if (! isset($this->featureLockService)) {
            $this->featureLockService = app(FeatureLockService::class);
        }

        return $this->featureLockService->isFeatureLocked(Auth::user()?->company, $featureKey);
    }

    /**
     * Obtenir le message de verrouillage d'une fonctionnalité
     */
    protected function getFeatureLockMessage(string $featureKey): ?string
    {
        if (! isset($this->featureLockService)) {
            $this->featureLockService = app(FeatureLockService::class);
        }

        return $this->featureLockService->getFeatureLockMessage($featureKey, Auth::user()?->company);
    }

    /**
     * Exiger l'accès à une fonctionnalité ou bloquer l'action
     */
    protected function requireFeatureAccess(string $featureKey): void
    {
        if (! $this->isFeatureAccessible($featureKey)) {
            $message = $this->getFeatureLockMessage($featureKey) ?? 'Cette fonctionnalité est temporairement indisponible.';

            // Pour Livewire, on peut utiliser une notification ou redirection
            session()->flash('error', $message);
            $this->redirectRoute('dashboard');

            return;
        }
    }

    /**
     * Vérifier l'accès avant d'exécuter une action
     */
    protected function checkFeatureAccess(string $featureKey, callable $callback, ?callable $onBlocked = null)
    {
        if ($this->isFeatureAccessible($featureKey)) {
            return $callback();
        }

        if ($onBlocked) {
            return $onBlocked();
        }

        $message = $this->getFeatureLockMessage($featureKey) ?? 'Cette fonctionnalité est temporairement indisponible.';
        session()->flash('error', $message);
    }

    /**
     * Filtrer une collection en fonction des verrouillages de fonctionnalités
     */
    protected function filterByFeatureAccess(array $items, string $featureKeyProperty = 'feature'): array
    {
        return array_filter($items, function ($item) use ($featureKeyProperty) {
            $featureKey = is_array($item) ? ($item[$featureKeyProperty] ?? null) : $item->{$featureKeyProperty} ?? null;

            if (! $featureKey) {
                return true; // Si pas de clé de fonctionnalité, on autorise
            }

            return $this->isFeatureAccessible($featureKey);
        });
    }
}
