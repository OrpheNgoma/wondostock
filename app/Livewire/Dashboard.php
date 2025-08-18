<?php

namespace App\Livewire;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Customer;
use App\Models\Product;
use App\Traits\SecureCompanyAccess;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
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
            return view('livewire.saas.dashboard', $this->getEmptyData());
        }

        $user = $userCompany['user'];
        $company = $userCompany['company'];

        // Vérifier les permissions d'accès au dashboard
        if (! Gate::allows('view_dashboard_stats')) {
            $this->dispatchSecureError('Vous n\'avez pas les permissions pour accéder au tableau de bord.');

            return view('livewire.saas.dashboard', $this->getEmptyData());
        }

        // Déterminer le scope des données selon les permissions
        $canViewGlobalReports = Gate::allows('view_global_reports');
        $canViewStoreReports = Gate::allows('view_store_reports');

        // Si l'utilisateur ne peut voir que son magasin et a un store_id, limiter les données
        $storeId = null;
        if (! $canViewGlobalReports && $canViewStoreReports && $user->store_id) {
            $storeId = $user->store_id;
        }

        // Récupérer les KPIs avec le contexte approprié
        $kpis = $this->getSecureDashboardData($company->id, (int) $this->period, $storeId, $user);

        // Ajouter les flags de permissions pour la vue
        $kpis['canViewFinancials'] = $canViewGlobalReports || $canViewStoreReports;
        $kpis['canViewInventory'] = Gate::allows('manage_inventory') || Gate::allows('view_products');
        $kpis['canViewReports'] = $canViewGlobalReports || $canViewStoreReports;

        // Dispatch des données pour ApexCharts (seulement si utilisateur peut voir les stats)
        if ($canViewGlobalReports || $canViewStoreReports) {
            $this->dispatch('update-charts', [
                'lineLabels' => $kpis['lineChartLabels'],
                'lineValues' => $kpis['lineChartValues'],
                'donutLabels' => $kpis['donutChartLabels'],
                'donutValues' => $kpis['donutChartValues'],
                'canViewFinancials' => $canViewGlobalReports || $canViewStoreReports,
            ]);
        }

        return view('livewire.saas.dashboard', $kpis);
    }

    private function getEmptyData(): array
    {
        return [
            'totalRevenue' => 0,
            'totalSales' => 0,
            'estimatedProfit' => 0,
            'newCustomersCount' => 0,
            'lineChartLabels' => [],
            'lineChartValues' => [],
            'donutChartLabels' => [],
            'donutChartValues' => [],
            'topProducts' => [],
            'lowStockProducts' => collect(),
            'canViewFinancials' => false,
            'canViewInventory' => false,
            'canViewReports' => false,
        ];
    }

    private function getSecureDashboardData(int $companyId, int $period, ?int $storeId, $user): array
    {
        // Permissions par rôle
        $canViewFinancials = Gate::allows('view_global_reports') || Gate::allows('view_store_reports');
        $canViewInventory = Gate::allows('manage_inventory') || Gate::allows('view_products');
        $canViewReports = Gate::allows('view_global_reports') || Gate::allows('view_store_reports');

        $startDate = Carbon::now()->subDays($period);

        // Initialiser les données
        $data = [
            'totalRevenue' => 0,
            'totalSales' => 0,
            'estimatedProfit' => 0,
            'newCustomersCount' => 0,
            'lineChartLabels' => [],
            'lineChartValues' => [],
            'donutChartLabels' => [],
            'donutChartValues' => [],
            'topProducts' => [],
            'lowStockProducts' => collect(),
            'canViewFinancials' => $canViewFinancials,
            'canViewInventory' => $canViewInventory,
            'canViewReports' => $canViewReports,
        ];

        try {
            // Si l'utilisateur peut voir les données financières
            if ($canViewFinancials) {
                $salesData = $this->getSecureSalesData($companyId, $startDate, $storeId);
                // Préserver les flags de permissions lors du merge
                $data = array_merge($salesData, [
                    'canViewFinancials' => $canViewFinancials,
                    'canViewInventory' => $canViewInventory,
                    'canViewReports' => $canViewReports,
                    'newCustomersCount' => $data['newCustomersCount'],
                    'lowStockProducts' => $data['lowStockProducts'],
                ]);
            }

            // Nouveaux clients (visible pour tous avec permission dashboard)
            $data['newCustomersCount'] = Customer::where('company_id', $companyId)
                ->where('created_at', '>=', $startDate)
                ->count();

            // Si l'utilisateur peut voir l'inventaire
            if ($canViewInventory) {
                $data['lowStockProducts'] = $this->getSecureLowStockProducts($companyId, $storeId);
            }

        } catch (\Exception $e) {
            \Log::error('Dashboard data error for company '.$companyId, [
                'error' => $e->getMessage(),
                'user_id' => $user->id,
                'store_id' => $storeId,
            ]);

            return $this->getEmptyData();
        }

        return $data;
    }

    private function getSecureSalesData(int $companyId, Carbon $startDate, ?int $storeId): array
    {
        $baseQuery = DB::table('documents as d')
            ->join('document_items as di', 'd.id', '=', 'di.document_id')
            ->join('products as p', 'di.product_id', '=', 'p.id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->where('d.company_id', $companyId)
            ->where('d.type', DocumentType::Invoice->value)
            ->where('d.document_date', '>=', $startDate)
            ->whereIn('d.status', [
                DocumentStatus::Validated->value,
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
            ]);

        if ($storeId) {
            $baseQuery->where('d.store_id', $storeId);
        }

        $rawData = $baseQuery->select([
            'd.id as document_id',
            DB::raw('DATE(d.document_date) as date'),
            'd.total_amount as document_total',
            'di.quantity',
            'di.unit_price',
            'di.total_amount as item_total',
            'p.id as product_id',
            'p.name as product_name',
            'p.sku as product_sku',
            'p.purchase_price',
            'c.name as category_name',
            DB::raw('(di.quantity * (di.unit_price - COALESCE(p.purchase_price, 0))) as item_profit'),
        ])->get();

        $uniqueDocuments = $rawData->groupBy('document_id');
        $totalRevenue = $uniqueDocuments->sum(fn ($items) => $items->first()->document_total);
        $totalSales = $uniqueDocuments->count();
        $estimatedProfit = $rawData->sum('item_profit');

        $chartData = $this->prepareChartData($uniqueDocuments, $startDate);
        $categoryData = $this->getCategoryData($rawData);
        $topProducts = $this->getTopProducts($rawData);

        return [
            'totalRevenue' => $totalRevenue,
            'totalSales' => $totalSales,
            'estimatedProfit' => $estimatedProfit,
            'lineChartLabels' => $chartData['labels'],
            'lineChartValues' => $chartData['values'],
            'donutChartLabels' => $categoryData['labels'],
            'donutChartValues' => $categoryData['values'],
            'topProducts' => $topProducts,
        ];
    }

    private function getSecureLowStockProducts(int $companyId, ?int $storeId)
    {
        $query = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->with(['stores' => function ($query) use ($storeId) {
                $query->whereRaw('quantity <= low_stock_threshold');
                if ($storeId) {
                    $query->where('stores.id', $storeId);
                }
            }])
            ->limit(5);

        return $query->get()->filter(function ($product) {
            return $product->stores->isNotEmpty();
        });
    }

    private function prepareChartData($uniqueDocuments, Carbon $startDate): array
    {
        $chartData = [];
        $date = clone $startDate;

        $salesByDate = $uniqueDocuments->mapToGroups(function ($items, $documentId) {
            return [$items->first()->date => $items->first()->document_total];
        })->map->sum();

        while ($date <= now()) {
            $formattedDate = $date->format('Y-m-d');
            $chartData[$formattedDate] = $salesByDate->get($formattedDate, 0);
            $date->addDay();
        }

        return [
            'labels' => array_keys($chartData),
            'values' => array_values($chartData),
        ];
    }

    private function getCategoryData($rawData): array
    {
        $salesByCategory = $rawData
            ->groupBy('category_name')
            ->map(fn ($items) => $items->sum('item_total'))
            ->sortDesc()
            ->take(5);

        return [
            'labels' => $salesByCategory->keys()->toArray(),
            'values' => $salesByCategory->values()->toArray(),
        ];
    }

    private function getTopProducts($rawData): array
    {
        return $rawData
            ->groupBy('product_id')
            ->map(function ($items) {
                $firstItem = $items->first();

                return [
                    'name' => $firstItem->product_name,
                    'sku' => $firstItem->product_sku,
                    'total_quantity' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(5)
            ->values()
            ->toArray();
    }
}
