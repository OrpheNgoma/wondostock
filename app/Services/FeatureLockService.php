<?php

namespace App\Services;

use App\Enums\FeatureEnum;
use App\Models\Company;
use App\Models\FeatureLock;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

/**
 * Service de gestion des verrouillages de fonctionnalités.
 *
 * Permet de vérifier, gérer et appliquer les restrictions d'accès
 * aux fonctionnalités basées sur les FeatureLocks.
 */
class FeatureLockService
{
    /**
     * Durée de cache en secondes pour les verrouillages.
     */
    const CACHE_TTL = 60;

    /**
     * Vérifier si une fonctionnalité est verrouillée pour une entreprise.
     *
     * @param  Company|null  $company  Entreprise à vérifier (null = company de l'utilisateur connecté)
     * @param  string|FeatureEnum  $featureKey  Clé ou enum de la fonctionnalité
     */
    public function isFeatureLocked(?Company $company, string|FeatureEnum $featureKey): bool
    {
        $company = $company ?? $this->getCurrentCompany();

        if (! $company) {
            return false;
        }

        $featureKey = $featureKey instanceof FeatureEnum ? $featureKey->value : $featureKey;
        $cacheKey = "feature_lock:{$company->id}:{$featureKey}";

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($featureKey, $company) {
            return FeatureLock::forCompany($company)
                ->forFeature($featureKey)
                ->active()
                ->exists();
        });
    }

    /**
     * Vérifier si une fonctionnalité est accessible (non verrouillée) pour une entreprise.
     */
    public function isFeatureAccessible(?Company $company, string|FeatureEnum $featureKey): bool
    {
        return ! $this->isFeatureLocked($company, $featureKey);
    }

    /**
     * Alias de isFeatureAccessible — utilisé par les directives Blade.
     */
    public function isFeatureUnlocked(?Company $company, string|FeatureEnum $featureKey): bool
    {
        return $this->isFeatureAccessible($company, $featureKey);
    }

    /**
     * Obtenir tous les FeatureLock actifs (modèles complets) pour une entreprise.
     */
    public function getLockedFeatures(?Company $company = null): Collection
    {
        $company = $company ?? $this->getCurrentCompany();

        if (! $company) {
            return collect();
        }

        return Cache::remember("locked_features:{$company->id}", self::CACHE_TTL, function () use ($company) {
            return FeatureLock::forCompany($company)->active()->get();
        });
    }

    /**
     * Alias de getLockedFeatures — retourne les FeatureLock actifs complets.
     */
    public function getCompanyLockedFeatures(Company $company): Collection
    {
        return $this->getLockedFeatures($company);
    }

    /**
     * Verrouiller une fonctionnalité pour une entreprise.
     */
    public function lockFeature(
        Company $company,
        string|FeatureEnum $featureKey,
        ?string $reason = null,
        ?User $admin = null,
        ?\Carbon\Carbon $expiresAt = null
    ): FeatureLock {
        $admin = $admin ?? Auth::user();
        $featureKey = $featureKey instanceof FeatureEnum ? $featureKey->value : $featureKey;

        $lock = FeatureLock::firstOrCreate(
            [
                'company_id' => $company->id,
                'feature_key' => $featureKey,
            ],
            [
                'is_locked' => false,
                'reason' => null,
                'locked_by' => null,
                'locked_at' => null,
                'expires_at' => null,
            ]
        );

        $lock->lock($reason, $admin, $expiresAt);
        $this->clearFeatureCache($company);

        return $lock;
    }

    /**
     * Déverrouiller une fonctionnalité pour une entreprise.
     */
    public function unlockFeature(
        Company $company,
        string|FeatureEnum $featureKey,
        ?User $admin = null
    ): bool {
        $featureKey = $featureKey instanceof FeatureEnum ? $featureKey->value : $featureKey;

        $lock = FeatureLock::forCompany($company)->forFeature($featureKey)->first();

        if (! $lock) {
            return false;
        }

        $lock->unlock();
        $this->clearFeatureCache($company);

        return true;
    }

    /**
     * Verrouiller plusieurs fonctionnalités en lot pour une entreprise.
     *
     * @param  array<string|FeatureEnum>  $features
     * @return Collection<FeatureLock>
     */
    public function bulkLockFeatures(
        Company $company,
        array $features,
        ?string $reason = null,
        ?User $admin = null,
        ?\Carbon\Carbon $expiresAt = null
    ): Collection {
        $admin = $admin ?? Auth::user();
        $locks = collect();

        foreach ($features as $featureKey) {
            $featureValue = $featureKey instanceof FeatureEnum ? $featureKey->value : $featureKey;

            $lock = FeatureLock::updateOrCreate(
                [
                    'company_id' => $company->id,
                    'feature_key' => $featureValue,
                ],
                [
                    'is_locked' => true,
                    'reason' => $reason,
                    'locked_by' => $admin?->id,
                    'locked_at' => now(),
                    'expires_at' => $expiresAt,
                ]
            );

            $locks->push($lock);
        }

        $this->clearFeatureCache($company);

        return $locks;
    }

    /**
     * Déverrouiller plusieurs fonctionnalités en lot pour une entreprise.
     *
     * @param  array<string|FeatureEnum>  $features
     * @return int Nombre de fonctionnalités déverrouillées
     */
    public function bulkUnlockFeatures(
        Company $company,
        array $features,
        ?User $admin = null
    ): int {
        $featureValues = array_map(
            fn ($f) => $f instanceof FeatureEnum ? $f->value : $f,
            $features
        );

        $count = FeatureLock::forCompany($company)
            ->whereIn('feature_key', $featureValues)
            ->where('is_locked', true)
            ->update([
                'is_locked' => false,
                'reason' => null,
                'locked_at' => null,
                'expires_at' => null,
            ]);

        $this->clearFeatureCache($company);

        return $count;
    }

    /**
     * Nettoyer les verrouillages expirés (appelé par la commande planifiée).
     *
     * @return int Nombre de verrouillages nettoyés
     */
    public function cleanupExpiredLocks(): int
    {
        $count = FeatureLock::expired()->update([
            'is_locked' => false,
            'reason' => null,
            'locked_at' => null,
            'expires_at' => null,
        ]);

        // Invalider tout le cache pour les companies affectées
        Cache::flush();

        return $count;
    }

    /**
     * Obtenir les statistiques globales des verrouillages.
     *
     * @return array{total_companies: int, total_active_locks: int, companies_with_locks: int, average_locks_per_company: float}
     */
    public function getGlobalLockStats(): array
    {
        $totalCompanies = Company::count();
        $totalActiveLocks = FeatureLock::active()->count();
        $companiesWithLocks = FeatureLock::active()->distinct('company_id')->count('company_id');
        $averageLocksPerCompany = $companiesWithLocks > 0
            ? round($totalActiveLocks / $companiesWithLocks, 2)
            : 0.0;

        return [
            'total_companies' => $totalCompanies,
            'total_active_locks' => $totalActiveLocks,
            'companies_with_locks' => $companiesWithLocks,
            'average_locks_per_company' => $averageLocksPerCompany,
        ];
    }

    /**
     * Générer un rapport des verrouillages par catégorie pour une entreprise.
     *
     * @return array<int, array{category: string, features: array, locked_count: int, total_count: int}>
     */
    public function getCompanyLockReport(Company $company): array
    {
        $allFeatures = FeatureEnum::getGroupedFeatures();
        $activeLockKeys = FeatureLock::forCompany($company)
            ->active()
            ->pluck('feature_key')
            ->toArray();

        $report = [];

        foreach ($allFeatures as $category => $features) {
            $lockedFeatures = array_filter(
                $features,
                fn ($f) => in_array($f['key'], $activeLockKeys)
            );

            $report[] = [
                'category' => $category,
                'features' => $features,
                'locked_count' => count($lockedFeatures),
                'total_count' => count($features),
            ];
        }

        return $report;
    }

    /**
     * Obtenir les détails d'un verrouillage de fonctionnalité.
     */
    public function getFeatureLockDetails(string|FeatureEnum $featureKey, ?Company $company = null): ?FeatureLock
    {
        $company = $company ?? $this->getCurrentCompany();
        $featureKey = $featureKey instanceof FeatureEnum ? $featureKey->value : $featureKey;

        if (! $company) {
            return null;
        }

        return FeatureLock::forCompany($company)
            ->forFeature($featureKey)
            ->with(['lockedBy'])
            ->first();
    }

    /**
     * Obtenir un message d'explication pour une fonctionnalité verrouillée.
     */
    public function getFeatureLockMessage(string|FeatureEnum $featureKey, ?Company $company = null): ?string
    {
        $lock = $this->getFeatureLockDetails($featureKey, $company);

        if (! $lock || ! $lock->isActive()) {
            return null;
        }

        $message = 'Cette fonctionnalité est actuellement indisponible';

        if ($lock->reason) {
            $message .= ' : '.$lock->reason;
        }

        if ($lock->expires_at) {
            $message .= " (jusqu'au ".$lock->expires_at->format('d/m/Y H:i').')';
        }

        return $message;
    }

    /**
     * Vider le cache des fonctionnalités pour une entreprise spécifique.
     */
    public function clearFeatureCache(Company $company): void
    {
        Cache::forget("locked_features:{$company->id}");

        foreach (FeatureEnum::cases() as $feature) {
            Cache::forget("feature_lock:{$company->id}:{$feature->value}");
        }
    }

    /**
     * Vider tout le cache des fonctionnalités (toutes les entreprises).
     */
    public function clearAllCache(): void
    {
        Company::query()->each(function (Company $company) {
            $this->clearFeatureCache($company);
        });
    }

    /**
     * Obtenir l'entreprise courante depuis le contexte d'authentification.
     */
    protected function getCurrentCompany(): ?Company
    {
        $user = Auth::user();

        if (! $user) {
            return null;
        }

        if ($user->company_id) {
            return $user->company;
        }

        // Valider que la company en session appartient bien à l'utilisateur connecté
        if (session()->has('current_company_id')) {
            $sessionCompanyId = session('current_company_id');

            if ((int) $sessionCompanyId === (int) $user->company_id) {
                return Company::find($sessionCompanyId);
            }
        }

        return null;
    }
}
