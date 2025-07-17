<?php

namespace App\Providers;

use App\Helpers\CurrencyHelper;
use App\Listeners\InvalidateDashboardCacheListener;
use App\Listeners\InvalidateSecurityCacheListener;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
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
        // Cette règle s'exécute avant toutes les autres.
        // Si l'utilisateur a le rôle "Global Admin" ou "Super-Administrateur", il a tous les droits.
        Gate::before(function (User $user, string $ability) {
            return $user->hasRole('Global-Admin|Super-Administrateur') ? true : null;
        });

        // Feature: Multi-Magasins (disponible pour le plan PRO)
        Gate::define('feature-multi-store', function (User $user) {
            return $user->company->hasFeature('multi_store');
        });

        // Feature: Rôles & Permissions (disponible pour le plan PRO)
        Gate::define('feature-roles-permissions', function (User $user) {
            return $user->company->hasFeature('roles_permissions');
        });

        // Feature: Reporting Avancé (disponible pour le plan PRO)
        Gate::define('feature-advanced-reporting', function (User $user) {
            return $user->company->hasFeature('advanced_reporting');
        });

        // Invalidation automatique du cache Dashboard lors des modifications
        $this->registerDashboardCacheListeners();

        // Invalidation automatique du cache de sécurité
        $this->registerSecurityCacheListeners();

        // Register custom Blade directives
        $this->registerBladeDirectives();

        // Ajoutez ici d'autres "Gates" pour les futures fonctionnalités payantes...
    }

    private function registerDashboardCacheListeners(): void
    {
        $listener = InvalidateDashboardCacheListener::class;
        $events = [
            'eloquent.created: App\Models\Document',
            'eloquent.updated: App\Models\Document',
            'eloquent.deleted: App\Models\Document',
            'eloquent.created: App\Models\DocumentItem',
            'eloquent.updated: App\Models\DocumentItem',
            'eloquent.deleted: App\Models\DocumentItem',
            'eloquent.created: App\Models\Product',
            'eloquent.updated: App\Models\Product',
            'eloquent.created: App\Models\Customer',
            'eloquent.created: App\Models\StockMovement',
        ];

        foreach ($events as $event) {
            Event::listen($event, $listener);
        }
    }

    private function registerSecurityCacheListeners(): void
    {
        $listener = InvalidateSecurityCacheListener::class;
        $events = [
            'eloquent.created: App\Models\User',
            'eloquent.updated: App\Models\User',
            'eloquent.deleted: App\Models\User',
            'eloquent.created: App\Models\Company',
            'eloquent.updated: App\Models\Company',
            'eloquent.deleted: App\Models\Company',
            'eloquent.created: Spatie\Permission\Models\Role',
            'eloquent.updated: Spatie\Permission\Models\Role',
            'eloquent.deleted: Spatie\Permission\Models\Role',
            'eloquent.created: Spatie\Permission\Models\Permission',
            'eloquent.updated: Spatie\Permission\Models\Permission',
            'eloquent.deleted: Spatie\Permission\Models\Permission',
        ];

        foreach ($events as $event) {
            Event::listen($event, $listener);
        }
    }

    private function registerBladeDirectives(): void
    {
        // Currency formatting directive
        Blade::directive('fcfa', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::format($expression); ?>";
        });

        // Currency formatting with custom decimals
        Blade::directive('fcfaDecimals', function ($expression) {
            return "<?php echo \App\Helpers\CurrencyHelper::format(...array_values(compact($expression))); ?>";
        });

        // Just the currency symbol
        Blade::directive('currency', function () {
            return "<?php echo \App\Helpers\CurrencyHelper::symbol(); ?>";
        });
    }
}
