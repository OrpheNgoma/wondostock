<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\TenantIsolation::class,
            \App\Http\Middleware\InitializePermissionsTeam::class,
        ]);

        $middleware->alias([
            'global_admin' => \App\Http\Middleware\GlobalAdminMiddleware::class,
            'check_admin_redirect' => \App\Http\Middleware\CheckAdminRedirect::class,
            'feature' => \App\Http\Middleware\FeatureGuard::class,
            'feature_lock' => \App\Http\Middleware\CheckFeatureLock::class,
            'module' => \App\Http\Middleware\CheckModuleAccess::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
