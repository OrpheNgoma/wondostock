<?php

namespace App\Providers;

use Illuminate\Support\Facades\URL;
use App\Models\User;
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

        // Ajoutez ici d'autres "Gates" pour les futures fonctionnalités payantes...
    }
}
