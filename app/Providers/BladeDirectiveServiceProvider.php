<?php

namespace App\Providers;

use App\Services\FeatureLockService;
use App\Services\ModuleService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    public function register(): void {}

    public function boot(): void
    {
        // --- Feature Locks ---

        Blade::if('featureAccessible', function ($featureKey) {
            return app(FeatureLockService::class)
                ->isFeatureAccessible(auth()->user()?->company, $featureKey);
        });

        Blade::if('featureLocked', function ($featureKey) {
            return app(FeatureLockService::class)
                ->isFeatureLocked(auth()->user()?->company, $featureKey);
        });

        Blade::directive('featureLockMessage', function ($featureKey) {
            return "<?php echo app('".FeatureLockService::class."')->getFeatureLockMessage({$featureKey}); ?>";
        });

        // --- Modules ---

        Blade::if('moduleEnabled', function ($moduleKey) {
            $company = auth()->user()?->company;

            return $company && app(ModuleService::class)->isEnabled($company, $moduleKey);
        });

        Blade::if('moduleDisabled', function ($moduleKey) {
            $company = auth()->user()?->company;

            return ! $company || ! app(ModuleService::class)->isEnabled($company, $moduleKey);
        });
    }
}
