<?php

namespace App\Livewire;

use App\Services\DashboardCacheService;
use App\Traits\SecureCompanyAccess;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Tableau de Bord - KaziFlow')]
class Dashboard extends Component
{
    use SecureCompanyAccess;

    public string $period = '30';

    public function setPeriod(string $period)
    {
        $this->period = $period;
        $this->dispatch('periodChanged');
    }

    public function render()
    {
        $userCompany = $this->getSecureUserAndCompany();

        // Vérification de sécurité
        if (! $userCompany['valid']) {
            return view('livewire.dashboard', [
                'totalRevenue' => 0, 'totalSales' => 0, 'estimatedProfit' => 0,
                'newCustomersCount' => 0, 'lineChartLabels' => [], 'lineChartValues' => [],
                'donutChartLabels' => [], 'donutChartValues' => [], 'topProducts' => collect(),
                'lowStockProducts' => collect(),
            ]);
        }

        $user = $userCompany['user'];
        $company = $userCompany['company'];
        $isGlobalView = Gate::allows('view_global_reports');
        $storeId = !$isGlobalView && $user->store_id ? $user->store_id : null;

        // Utilisation du service de cache pour récupérer les KPIs
        $dashboardService = app(DashboardCacheService::class);
        $kpis = $dashboardService->getKPIs($company->id, (int) $this->period, $storeId);

        // Dispatch des données pour les graphiques
        $this->dispatch('update-charts', [
            'lineLabels' => $kpis['lineChartLabels'],
            'lineValues' => $kpis['lineChartValues'],
            'donutLabels' => $kpis['donutChartLabels'],
            'donutValues' => $kpis['donutChartValues'],
        ]);

        return view('livewire.dashboard', $kpis);
    }
}
