<?php

namespace App\Providers\Filament;

use App\Http\Middleware\GlobalAdminMiddleware;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

/**
 * Configuration du panel d'administration global Filament
 *
 * Panel dédié aux super admins pour la gestion de la plateforme SaaS
 * Accès restreint aux utilisateurs avec permissions globales
 */
class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('WondoStock Admin')
            ->brandLogo(asset('images/LOGO WONDO STOCK-01.jpg'))
            ->brandLogoHeight('2rem')
            ->favicon(asset('favicon.ico'))
            ->login()
            ->colors([
                'primary' => Color::Indigo,      // Rouge WondoStock
                'gray' => Color::Slate,       // Gris moderne
                'danger' => Color::Rose,      // Erreurs
                'warning' => Color::Amber,    // Avertissements
                'success' => Color::Emerald,  // Succès
                'info' => Color::Blue,        // Informations
            ])
            ->font('Inter')
            ->darkMode(false) // Admin en mode clair pour professionnalisme
            ->maxContentWidth('full') // Interface large pour les tableaux
            ->sidebarCollapsibleOnDesktop()
            ->resources([
                \App\Filament\Resources\Companies\CompanyResource::class,
                \App\Filament\Resources\FeatureLocks\FeatureLockResource::class,
                \App\Filament\Resources\PlanResource::class,
                \App\Filament\Resources\SubscriptionResource::class,
                \App\Filament\Resources\InvoiceResource::class,
                \App\Filament\Resources\PaymentResource::class,
            ])
            ->pages([
                Dashboard::class,
            ])
            ->widgets([
                AccountWidget::class,
                \App\Filament\Widgets\SaasOverviewWidget::class,
                \App\Filament\Widgets\RevenueChartWidget::class,
                \App\Filament\Widgets\PlansDistributionWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                Authenticate::class,
                GlobalAdminMiddleware::class, // Vérification des permissions admin global
            ])
            ->authGuard('web')
            ->databaseNotifications()
            ->databaseNotificationsPolling('30s')
            ->spa(); // Single Page Application pour performance
    }
}
