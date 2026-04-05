<?php

namespace App\Providers;

use App\Services\FeatureLockService;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

/**
 * Service Provider pour les directives Blade personnalisées.
 *
 * Ajoute des directives pour vérifier les verrouillages de fonctionnalités
 * directement dans les templates Blade.
 */
class BladeServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->registerFeatureDirectives();
    }

    /**
     * Enregistre les directives Blade pour les fonctionnalités.
     */
    private function registerFeatureDirectives(): void
    {
        // @canFeature('products.manage')
        Blade::directive('canFeature', function ($expression) {
            return "<?php if(app('".FeatureLockService::class."')->isFeatureAccessible(auth()->user()->company ?? null, {$expression})): ?>";
        });

        // @endcanFeature
        Blade::directive('endcanFeature', function () {
            return '<?php endif; ?>';
        });

        // @cannotFeature('products.manage')
        Blade::directive('cannotFeature', function ($expression) {
            return "<?php if(app('".FeatureLockService::class."')->isFeatureLocked(auth()->user()->company ?? null, {$expression})): ?>";
        });

        // @endcannotFeature
        Blade::directive('endcannotFeature', function () {
            return '<?php endif; ?>';
        });

        // @featureUnless('products.manage')
        Blade::directive('featureUnless', function ($expression) {
            return "<?php unless(app('".FeatureLockService::class."')->isFeatureAccessible(auth()->user()->company ?? null, {$expression})): ?>";
        });

        // @endfeatureUnless
        Blade::directive('endfeatureUnless', function () {
            return '<?php endunless; ?>';
        });

        // @hasFeatures(['products.manage', 'sales.create'])
        Blade::directive('hasFeatures', function ($expression) {
            return "<?php 
                \$featuresArray = {$expression};
                \$featureLockService = app('".FeatureLockService::class."');
                \$company = auth()->user()->company ?? null;
                \$hasAllFeatures = \$company && collect(\$featuresArray)->every(function(\$feature) use (\$featureLockService, \$company) {
                    return \$featureLockService->isFeatureAccessible(\$company, \$feature);
                });
                if(\$hasAllFeatures): 
            ?>";
        });

        // @endhasFeatures
        Blade::directive('endhasFeatures', function () {
            return '<?php endif; ?>';
        });

        // @hasAnyFeature(['products.manage', 'sales.create'])
        Blade::directive('hasAnyFeature', function ($expression) {
            return "<?php 
                \$featuresArray = {$expression};
                \$featureLockService = app('".FeatureLockService::class."');
                \$company = auth()->user()->company ?? null;
                \$hasAnyFeature = \$company && collect(\$featuresArray)->some(function(\$feature) use (\$featureLockService, \$company) {
                    return \$featureLockService->isFeatureAccessible(\$company, \$feature);
                });
                if(\$hasAnyFeature): 
            ?>";
        });

        // @endhasAnyFeature
        Blade::directive('endhasAnyFeature', function () {
            return '<?php endif; ?>';
        });

        // @featureStatus('products.manage')
        Blade::directive('featureStatus', function ($expression) {
            return "<?php 
                \$featureKey = {$expression};
                \$featureLockService = app('".FeatureLockService::class."');
                \$company = auth()->user()->company ?? null;
                if (\$company) {
                    \$isLocked = \$featureLockService->isFeatureLocked(\$company, \$featureKey);
                    echo \$isLocked ? 'locked' : 'unlocked';
                } else {
                    echo 'unavailable';
                }
            ?>";
        });

        // @isGlobalAdmin
        Blade::directive('isGlobalAdmin', function () {
            return "<?php if(auth()->check() && (auth()->user()->hasRole('global_admin') || auth()->user()->is_global_admin ?? false)): ?>";
        });

        // @endisGlobalAdmin
        Blade::directive('endisGlobalAdmin', function () {
            return '<?php endif; ?>';
        });

        // @featureLockInfo('products.manage')
        Blade::directive('featureLockInfo', function ($expression) {
            return "<?php 
                \$featureKey = {$expression};
                \$company = auth()->user()->company ?? null;
                if (\$company) {
                    \$lock = \$company->featureLocks()->where('feature_key', \$featureKey)->where('is_locked', true)->first();
                    if (\$lock) {
                        echo json_encode([
                            'reason' => \$lock->reason,
                            'locked_at' => \$lock->locked_at?->toISOString(),
                            'expires_at' => \$lock->expires_at?->toISOString(),
                            'locked_by' => \$lock->lockedBy?->name,
                        ]);
                    } else {
                        echo 'null';
                    }
                } else {
                    echo 'null';
                }
            ?>";
        });

        // @featureGuard('products.manage', 'div')
        Blade::directive('featureGuard', function ($expression) {
            $parts = explode(',', $expression);
            $featureKey = trim($parts[0]);
            $element = isset($parts[1]) ? trim($parts[1], " '\"") : 'div';

            return "<?php 
                \$featureKey = {$featureKey};
                \$featureLockService = app('".FeatureLockService::class."');
                \$company = auth()->user()->company ?? null;
                \$isLocked = \$company ? \$featureLockService->isFeatureLocked(\$company, \$featureKey) : true;
                
                if (\$isLocked): 
                    \$featureEnum = \\App\\Enums\\FeatureEnum::tryFrom(\$featureKey);
                    \$description = \$featureEnum ? \$featureEnum->getDescription() : \$featureKey;
            ?>
                <{$element} class=\"feature-locked-overlay relative\">
                    <div class=\"absolute inset-0 bg-gray-100 bg-opacity-75 flex items-center justify-center z-10 rounded\">
                        <div class=\"text-center p-4\">
                            <svg class=\"h-8 w-8 text-gray-400 mx-auto mb-2\" fill=\"none\" viewBox=\"0 0 24 24\" stroke=\"currentColor\">
                                <path stroke-linecap=\"round\" stroke-linejoin=\"round\" stroke-width=\"2\" d=\"M12 15v2m0 0v2m0-2h2m-2 0h-2m9-6V9a3 3 0 00-3-3H6a3 3 0 00-3 3v6a3 3 0 003 3h12a3 3 0 003-3z\" />
                            </svg>
                            <p class=\"text-sm text-gray-600 font-medium\">Fonctionnalité verrouillée</p>
                            <p class=\"text-xs text-gray-500 mt-1\">{{ \$description }}</p>
                        </div>
                    </div>
                    <div class=\"pointer-events-none opacity-30\">
            <?php else: ?>
                <{$element}>
            <?php endif; ?>";
        });

        // @endfeatureGuard
        Blade::directive('endfeatureGuard', function () {
            return '<?php 
                if ($isLocked): 
            ?>
                    </div>
                </div>
            <?php else: ?>
                </div>
            <?php endif; ?>';
        });
    }
}
