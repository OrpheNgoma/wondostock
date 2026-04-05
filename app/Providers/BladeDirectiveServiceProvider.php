<?php

namespace App\Providers;

use App\Services\FeatureLockService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class BladeDirectiveServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Directive pour vérifier si une fonctionnalité est accessible
        Blade::if('featureAccessible', function ($featureKey) {
            $service = app(FeatureLockService::class);

            return $service->isFeatureAccessible(auth()->user()?->company, $featureKey);
        });

        // Directive pour vérifier si une fonctionnalité est verrouillée
        Blade::if('featureLocked', function ($featureKey) {
            $service = app(FeatureLockService::class);

            return $service->isFeatureLocked(auth()->user()?->company, $featureKey);
        });

        // Directive pour afficher un message de verrouillage
        Blade::directive('featureLockMessage', function ($featureKey) {
            return "<?php echo app('".FeatureLockService::class."')->getFeatureLockMessage({$featureKey}); ?>";
        });
    }
}
