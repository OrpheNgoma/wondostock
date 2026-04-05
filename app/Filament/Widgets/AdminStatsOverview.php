<?php

namespace App\Filament\Widgets;

use App\Models\Company;
use App\Models\Document;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

/**
 * Widget de vue d'ensemble des statistiques admin
 *
 * Affiche les métriques clés de la plateforme SaaS :
 * - Nombres d'entreprises et utilisateurs
 * - Revenus et abonnements actifs
 * - Activité récente et croissance
 */
class AdminStatsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            // Statistiques des entreprises
            Stat::make('Entreprises Actives', $this->getActiveCompaniesCount())
                ->description($this->getCompaniesGrowth())
                ->descriptionIcon($this->getGrowthIcon($this->getCompaniesGrowthValue()))
                ->color($this->getGrowthColor($this->getCompaniesGrowthValue()))
                ->chart($this->getCompaniesChart()),

            // Statistiques des utilisateurs
            Stat::make('Utilisateurs Totaux', $this->getTotalUsersCount())
                ->description($this->getUsersGrowth())
                ->descriptionIcon($this->getGrowthIcon($this->getUsersGrowthValue()))
                ->color($this->getGrowthColor($this->getUsersGrowthValue()))
                ->chart($this->getUsersChart()),

            // Revenus mensuels
            Stat::make('Revenus Mensuels', $this->getMonthlyRevenue())
                ->description($this->getRevenueGrowth())
                ->descriptionIcon($this->getGrowthIcon($this->getRevenueGrowthValue()))
                ->color($this->getGrowthColor($this->getRevenueGrowthValue()))
                ->chart($this->getRevenueChart()),

            // Documents générés ce mois
            Stat::make('Documents Ce Mois', $this->getDocumentsThisMonth())
                ->description('Total documents générés')
                ->descriptionIcon('heroicon-m-document-text')
                ->color('info')
                ->chart($this->getDocumentsChart()),
        ];
    }

    /**
     * Compte des entreprises actives
     */
    private function getActiveCompaniesCount(): int
    {
        return Company::where('is_active', true)->count();
    }

    /**
     * Croissance des entreprises
     */
    private function getCompaniesGrowth(): string
    {
        $growth = $this->getCompaniesGrowthValue();

        return abs($growth).'% ce mois';
    }

    private function getCompaniesGrowthValue(): float
    {
        $currentMonth = Company::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = Company::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth === 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Compte total des utilisateurs
     */
    private function getTotalUsersCount(): int
    {
        return User::count();
    }

    /**
     * Croissance des utilisateurs
     */
    private function getUsersGrowth(): string
    {
        $growth = $this->getUsersGrowthValue();

        return abs($growth).'% ce mois';
    }

    private function getUsersGrowthValue(): float
    {
        $currentMonth = User::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();

        $lastMonth = User::whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year)
            ->count();

        if ($lastMonth === 0) {
            return $currentMonth > 0 ? 100 : 0;
        }

        return round((($currentMonth - $lastMonth) / $lastMonth) * 100, 1);
    }

    /**
     * Revenus mensuels estimés
     */
    private function getMonthlyRevenue(): string
    {
        $revenue = DB::table('subscriptions')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->where('subscriptions.status', 'active')
            ->sum('plans.price');

        return number_format($revenue, 0, ',', ' ').' FCFA';
    }

    private function getRevenueGrowth(): string
    {
        $growth = $this->getRevenueGrowthValue();

        return abs($growth).'% vs dernier mois';
    }

    private function getRevenueGrowthValue(): float
    {
        // Simulation de croissance du revenu
        // En réalité, il faudrait calculer avec l'historique des abonnements
        return rand(-10, 25);
    }

    /**
     * Documents générés ce mois
     */
    private function getDocumentsThisMonth(): int
    {
        return Document::whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year)
            ->count();
    }

    /**
     * Graphiques de tendance
     */
    private function getCompaniesChart(): array
    {
        return $this->getMonthlyData(Company::class);
    }

    private function getUsersChart(): array
    {
        return $this->getMonthlyData(User::class);
    }

    private function getRevenueChart(): array
    {
        // Simulation de données de revenu sur 7 jours
        return [
            rand(80, 120),
            rand(80, 120),
            rand(80, 120),
            rand(80, 120),
            rand(80, 120),
            rand(80, 120),
            rand(80, 120),
        ];
    }

    private function getDocumentsChart(): array
    {
        return $this->getMonthlyData(Document::class);
    }

    /**
     * Données mensuelles pour les graphiques
     */
    private function getMonthlyData(string $model): array
    {
        $data = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $count = $model::whereDate('created_at', $date)->count();
            $data[] = $count;
        }

        return $data;
    }

    /**
     * Utilitaires pour les indicateurs de croissance
     */
    private function getGrowthIcon(float $growth): string
    {
        return $growth >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down';
    }

    private function getGrowthColor(float $growth): string
    {
        if ($growth > 10) {
            return 'success';
        }
        if ($growth > 0) {
            return 'warning';
        }

        return 'danger';
    }
}
