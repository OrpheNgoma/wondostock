<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Subscription;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SaasOverviewWidget extends StatsOverviewWidget
{
    protected ?string $pollingInterval = '30s';

    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        // Calcul des métriques
        $totalCompanies = Company::count();
        $activeCompanies = Company::where('is_active', true)->count();
        $activeSubscriptions = Subscription::where('status', 'active')->count();

        // MRR (Monthly Recurring Revenue)
        $mrr = Subscription::where('subscriptions.status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price') / 100; // Conversion de centimes en FCFA

        // Évolution par rapport au mois précédent
        $lastMonthCompanies = Company::where('created_at', '<', now()->startOfMonth())->count();
        $companiesGrowth = $lastMonthCompanies > 0
            ? (($totalCompanies - $lastMonthCompanies) / $lastMonthCompanies) * 100
            : 0;

        $lastMonthSubscriptions = Subscription::where('subscriptions.status', 'active')
            ->where('subscriptions.created_at', '<', now()->startOfMonth())
            ->count();
        $subscriptionsGrowth = $lastMonthSubscriptions > 0
            ? (($activeSubscriptions - $lastMonthSubscriptions) / $lastMonthSubscriptions) * 100
            : 0;

        // Factures en attente
        $pendingInvoices = Invoice::whereIn('status', ['sent', 'overdue'])->count();
        $overdueInvoices = Invoice::where('status', 'overdue')->count();

        return [
            Stat::make('Entreprises Totales', $totalCompanies)
                ->description($companiesGrowth >= 0
                    ? '+'.number_format($companiesGrowth, 1).'% ce mois'
                    : number_format($companiesGrowth, 1).'% ce mois')
                ->descriptionIcon($companiesGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($companiesGrowth >= 0 ? 'success' : 'danger')
                ->chart([
                    Company::whereMonth('created_at', now()->subMonths(6)->month)->count(),
                    Company::whereMonth('created_at', now()->subMonths(5)->month)->count(),
                    Company::whereMonth('created_at', now()->subMonths(4)->month)->count(),
                    Company::whereMonth('created_at', now()->subMonths(3)->month)->count(),
                    Company::whereMonth('created_at', now()->subMonths(2)->month)->count(),
                    Company::whereMonth('created_at', now()->subMonths(1)->month)->count(),
                    Company::whereMonth('created_at', now()->month)->count(),
                ]),

            Stat::make('Entreprises Actives', $activeCompanies)
                ->description($totalCompanies > 0
                    ? number_format(($activeCompanies / $totalCompanies) * 100, 1).'% du total'
                    : 'Aucune entreprise')
                ->descriptionIcon('heroicon-m-building-office-2')
                ->color('info'),

            Stat::make('Abonnements Actifs', $activeSubscriptions)
                ->description($subscriptionsGrowth >= 0
                    ? '+'.number_format($subscriptionsGrowth, 1).'% ce mois'
                    : number_format($subscriptionsGrowth, 1).'% ce mois')
                ->descriptionIcon($subscriptionsGrowth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($subscriptionsGrowth >= 0 ? 'success' : 'warning')
                ->chart([
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->subMonths(6)->month)->count(),
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->subMonths(5)->month)->count(),
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->subMonths(4)->month)->count(),
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->subMonths(3)->month)->count(),
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->subMonths(2)->month)->count(),
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->subMonths(1)->month)->count(),
                    Subscription::where('status', 'active')->whereMonth('created_at', now()->month)->count(),
                ]),

            Stat::make('Revenus Mensuels (MRR)', number_format($mrr, 0, ',', ' ').' FCFA')
                ->description('Revenus récurrents mensuels')
                ->descriptionIcon('heroicon-m-banknotes')
                ->color('success'),

            Stat::make('Factures en Attente', $pendingInvoices)
                ->description($overdueInvoices > 0
                    ? $overdueInvoices.' en retard'
                    : 'Aucune en retard')
                ->descriptionIcon($overdueInvoices > 0 ? 'heroicon-m-exclamation-triangle' : 'heroicon-m-check-circle')
                ->color($overdueInvoices > 0 ? 'danger' : 'success'),

            Stat::make('Taux d\'Activation', $totalCompanies > 0
                ? number_format(($activeSubscriptions / $totalCompanies) * 100, 1).'%'
                : '0%')
                ->description('Entreprises avec abonnement actif')
                ->descriptionIcon('heroicon-m-chart-bar')
                ->color($totalCompanies > 0 && ($activeSubscriptions / $totalCompanies) > 0.7 ? 'success' : 'warning'),
        ];
    }
}
