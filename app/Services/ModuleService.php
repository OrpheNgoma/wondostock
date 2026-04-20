<?php

namespace App\Services;

use App\Enums\ModuleKey;
use App\Models\Company;
use App\Models\TenantModule;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ModuleService
{
    private const CACHE_TTL = 300; // 5 minutes

    private function cacheKey(int $companyId): string
    {
        return "company_{$companyId}_modules";
    }

    /**
     * @return Collection<string, bool>
     */
    private function getModuleMap(Company $company): Collection
    {
        return Cache::remember($this->cacheKey($company->id), self::CACHE_TTL, function () use ($company) {
            return TenantModule::where('company_id', $company->id)
                ->get()
                ->keyBy(fn ($m) => $m->module_key instanceof ModuleKey ? $m->module_key->value : $m->module_key)
                ->map(fn ($m) => $m->is_enabled);
        });
    }

    public function isEnabled(Company $company, ModuleKey|string $moduleKey): bool
    {
        $key = $moduleKey instanceof ModuleKey ? $moduleKey->value : $moduleKey;

        return (bool) $this->getModuleMap($company)->get($key, false);
    }

    public function enable(Company $company, ModuleKey|string $moduleKey, ?int $userId = null): TenantModule
    {
        $key = $moduleKey instanceof ModuleKey ? $moduleKey->value : $moduleKey;

        $module = TenantModule::updateOrCreate(
            ['company_id' => $company->id, 'module_key' => $key],
            [
                'is_enabled' => true,
                'enabled_at' => now(),
                'enabled_by' => $userId ?? Auth::id(),
            ]
        );

        $this->clearCache($company);

        return $module;
    }

    public function disable(Company $company, ModuleKey|string $moduleKey): void
    {
        $key = $moduleKey instanceof ModuleKey ? $moduleKey->value : $moduleKey;

        TenantModule::where('company_id', $company->id)
            ->where('module_key', $key)
            ->update(['is_enabled' => false, 'enabled_at' => null, 'enabled_by' => null]);

        $this->clearCache($company);
    }

    public function toggle(Company $company, ModuleKey|string $moduleKey): bool
    {
        if ($this->isEnabled($company, $moduleKey)) {
            $this->disable($company, $moduleKey);

            return false;
        }

        $this->enable($company, $moduleKey);

        return true;
    }

    public function updateConfig(Company $company, ModuleKey|string $moduleKey, array $config): void
    {
        $key = $moduleKey instanceof ModuleKey ? $moduleKey->value : $moduleKey;

        TenantModule::where('company_id', $company->id)
            ->where('module_key', $key)
            ->update(['config' => $config]);

        $this->clearCache($company);
    }

    /** @return Collection<int, TenantModule> */
    public function getEnabledModules(Company $company): Collection
    {
        return TenantModule::where('company_id', $company->id)
            ->where('is_enabled', true)
            ->get();
    }

    /**
     * Initialise tous les modules (désactivés) pour une nouvelle company.
     */
    public function syncDefaults(Company $company): void
    {
        foreach (ModuleKey::cases() as $module) {
            TenantModule::firstOrCreate(
                ['company_id' => $company->id, 'module_key' => $module->value],
                ['is_enabled' => false]
            );
        }

        $this->clearCache($company);
    }

    public function clearCache(Company $company): void
    {
        Cache::forget($this->cacheKey($company->id));
    }
}
