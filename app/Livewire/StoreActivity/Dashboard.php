<?php

namespace App\Livewire\StoreActivity;

use App\Models\Document;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use App\Models\Store;
use App\Services\InventoryService;
use App\Services\ReportingService;
use App\Traits\SecureCompanyAccess;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Activité Magasin - WondoStock')]
class Dashboard extends Component
{
    use SecureCompanyAccess;

    public ?int $selectedStoreId = null;

    public string $selectedPeriod = '7'; // 7 jours par défaut

    public string $selectedTab = 'overview'; // overview, stock, sales, transfers, analytics

    // Real-time refresh
    public bool $autoRefresh = false;

    public int $refreshInterval = 30; // secondes

    protected $listeners = [
        'storeSelected' => 'selectStore',
        'refreshData' => 'refreshData',
        'echo:stock-movement,StockMovementCreated' => 'refreshData',
        'echo:document,DocumentCreated' => 'refreshData',
    ];

    public function mount(?int $storeId = null)
    {
        $userCompany = $this->getSecureUserAndCompany();
        if (! $userCompany['valid']) {
            abort(403);
        }

        $user = $userCompany['user'];

        // Auto-sélectionner un magasin
        if ($storeId) {
            $this->selectedStoreId = $storeId;
        } elseif ($user->store_id) {
            $this->selectedStoreId = $user->store_id;
        } else {
            $firstStore = Store::where('company_id', $user->company_id)->first();
            $this->selectedStoreId = $firstStore?->id;
        }
    }

    public function selectStore(int $storeId)
    {
        $this->selectedStoreId = $storeId;
        $this->dispatch('store-changed', $storeId);
    }

    public function selectPeriod(string $period)
    {
        $this->selectedPeriod = $period;
    }

    public function selectTab(string $tab)
    {
        $this->selectedTab = $tab;
    }

    public function toggleAutoRefresh()
    {
        $this->autoRefresh = ! $this->autoRefresh;

        if ($this->autoRefresh) {
            $this->dispatch('start-auto-refresh', $this->refreshInterval);
        } else {
            $this->dispatch('stop-auto-refresh');
        }
    }

    public function refreshData()
    {
        // Méthode appelée pour rafraîchir les données
        $this->dispatch('data-refreshed');
    }

    public function render()
    {
        $userCompany = $this->getSecureUserAndCompany();
        if (! $userCompany['valid']) {
            return view('livewire.saas.store-activity.dashboard', []);
        }

        $user = $userCompany['user'];
        $company = $userCompany['company'];

        // Si aucun magasin sélectionné, afficher la liste
        if (! $this->selectedStoreId) {
            return view('livewire.saas.store-activity.dashboard', [
                'stores' => Store::where('company_id', $company->id)->get(),
                'selectedStore' => null,
                'noStoreSelected' => true,
            ]);
        }

        // Récupérer le magasin sélectionné
        $selectedStore = Store::where('company_id', $company->id)
            ->where('id', $this->selectedStoreId)
            ->first();

        if (! $selectedStore) {
            $this->selectedStoreId = null;

            return $this->render();
        }

        // Calculer les dates
        $endDate = Carbon::now();
        $startDate = Carbon::now()->subDays((int) $this->selectedPeriod);

        // Données selon l'onglet sélectionné
        $data = match ($this->selectedTab) {
            'overview' => $this->getOverviewData($company->id, $selectedStore->id, $startDate, $endDate),
            'stock' => $this->getStockData($company->id, $selectedStore->id),
            'sales' => $this->getSalesData($company->id, $selectedStore->id, $startDate, $endDate),
            'transfers' => $this->getTransfersData($company->id, $selectedStore->id, $startDate, $endDate),
            'analytics' => $this->getAnalyticsData($company->id, $selectedStore->id, $startDate, $endDate),
            default => []
        };

        return view('livewire.saas.store-activity.dashboard', [
            'stores' => Store::where('company_id', $company->id)->get(),
            'selectedStore' => $selectedStore,
            'data' => $data,
            'periods' => $this->getPeriodOptions(),
            'tabs' => $this->getTabOptions(),
            'noStoreSelected' => false,
        ]);
    }

    private function getOverviewData(int $companyId, int $storeId, Carbon $startDate, Carbon $endDate): array
    {
        // KPIs principaux
        $todaySales = Document::where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->where('type', 'invoice')
            ->whereDate('document_date', today())
            ->whereIn('status', ['validated', 'paid', 'partially_paid'])
            ->sum('total_amount');

        $stockValue = app(InventoryService::class)->calculateStockValue($companyId, $storeId);

        $lowStockCount = app(InventoryService::class)
            ->getLowStockProducts($companyId, $storeId)
            ->count();

        // Activités récentes
        $recentMovements = StockMovement::where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->with(['product'])
            ->latest()
            ->limit(10)
            ->get();

        $recentSales = Document::where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->where('type', 'invoice')
            ->with(['customer'])
            ->latest()
            ->limit(5)
            ->get();

        // Alertes
        $alerts = $this->generateAlerts($companyId, $storeId);

        return [
            'kpis' => [
                'today_sales' => $todaySales,
                'stock_value' => $stockValue['total_sale_value'],
                'low_stock_count' => $lowStockCount,
                'total_products' => $stockValue['unique_products'],
            ],
            'recent_movements' => $recentMovements,
            'recent_sales' => $recentSales,
            'alerts' => $alerts,
        ];
    }

    private function getStockData(int $companyId, int $storeId): array
    {
        $inventoryService = app(InventoryService::class);

        $stockValue = $inventoryService->calculateStockValue($companyId, $storeId);
        $lowStockProducts = $inventoryService->getLowStockProducts($companyId, $storeId);

        // Top produits par valeur
        $topValueProducts = DB::table('products as p')
            ->join('product_store as ps', 'p.id', '=', 'ps.product_id')
            ->where('p.company_id', $companyId)
            ->where('ps.store_id', $storeId)
            ->where('p.is_active', true)
            ->select([
                'p.name', 'p.sku', 'ps.quantity',
                'p.selling_price',
                DB::raw('ps.quantity * p.selling_price as total_value'),
            ])
            ->orderByDesc('total_value')
            ->limit(10)
            ->get();

        // Mouvements de stock récents
        $stockMovements = StockMovement::where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->with(['product'])
            ->latest()
            ->limit(20)
            ->get();

        return [
            'stock_summary' => $stockValue,
            'low_stock_products' => $lowStockProducts,
            'top_value_products' => $topValueProducts,
            'recent_movements' => $stockMovements,
        ];
    }

    private function getSalesData(int $companyId, int $storeId, Carbon $startDate, Carbon $endDate): array
    {
        $reportingService = app(ReportingService::class);

        // Rapport de ventes pour la période
        $salesReport = $reportingService->generateSalesReport($companyId, $startDate, $endDate, $storeId);

        // Ventes par heure aujourd'hui
        $hourlyToday = DB::table('documents')
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->where('type', 'invoice')
            ->whereDate('document_date', today())
            ->whereIn('status', ['validated', 'paid', 'partially_paid'])
            ->select([
                DB::raw('HOUR(created_at) as hour'),
                DB::raw('COUNT(*) as sales_count'),
                DB::raw('SUM(total_amount) as revenue'),
            ])
            ->groupBy('hour')
            ->orderBy('hour')
            ->get();

        return [
            'sales_report' => $salesReport,
            'hourly_today' => $hourlyToday,
        ];
    }

    private function getTransfersData(int $companyId, int $storeId, Carbon $startDate, Carbon $endDate): array
    {
        // Transferts entrants
        $incomingTransfers = StockTransfer::where('company_id', $companyId)
            ->where('to_store_id', $storeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['fromStore', 'items.product'])
            ->latest()
            ->get();

        // Transferts sortants
        $outgoingTransfers = StockTransfer::where('company_id', $companyId)
            ->where('from_store_id', $storeId)
            ->whereBetween('created_at', [$startDate, $endDate])
            ->with(['toStore', 'items.product'])
            ->latest()
            ->get();

        // Statistiques des transferts
        $transferStats = [
            'incoming_count' => $incomingTransfers->count(),
            'outgoing_count' => $outgoingTransfers->count(),
            'pending_incoming' => $incomingTransfers->where('status', 'pending')->count(),
            'pending_outgoing' => $outgoingTransfers->where('status', 'pending')->count(),
        ];

        return [
            'incoming_transfers' => $incomingTransfers,
            'outgoing_transfers' => $outgoingTransfers,
            'transfer_stats' => $transferStats,
        ];
    }

    private function getAnalyticsData(int $companyId, int $storeId, Carbon $startDate, Carbon $endDate): array
    {
        // Analyse de performance
        $previousPeriod = $startDate->copy()->subDays($startDate->diffInDays($endDate));

        $currentPeriodSales = Document::where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereBetween('document_date', [$startDate, $endDate])
            ->sum('total_amount');

        $previousPeriodSales = Document::where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereBetween('document_date', [$previousPeriod, $startDate])
            ->sum('total_amount');

        $growthRate = $previousPeriodSales > 0
            ? (($currentPeriodSales - $previousPeriodSales) / $previousPeriodSales) * 100
            : 0;

        // Rotation des stocks
        $stockTurnover = $this->calculateStockTurnover($companyId, $storeId, $startDate, $endDate);

        return [
            'current_period_sales' => $currentPeriodSales,
            'previous_period_sales' => $previousPeriodSales,
            'growth_rate' => $growthRate,
            'stock_turnover' => $stockTurnover,
        ];
    }

    private function generateAlerts(int $companyId, int $storeId): array
    {
        $alerts = [];

        // Stock critique
        $criticalStock = app(InventoryService::class)
            ->getLowStockProducts($companyId, $storeId);

        if ($criticalStock->count() > 0) {
            $alerts[] = [
                'type' => 'warning',
                'title' => 'Stock critique',
                'message' => "{$criticalStock->count()} produit(s) en rupture ou stock faible",
                'action' => 'Voir les produits',
                'url' => route('products.index'),
            ];
        }

        // Transferts en attente
        $pendingTransfers = StockTransfer::where('company_id', $companyId)
            ->where(function ($q) use ($storeId) {
                $q->where('from_store_id', $storeId)
                    ->orWhere('to_store_id', $storeId);
            })
            ->where('status', 'pending')
            ->count();

        if ($pendingTransfers > 0) {
            $alerts[] = [
                'type' => 'info',
                'title' => 'Transferts en attente',
                'message' => "{$pendingTransfers} transfert(s) en attente de validation",
                'action' => 'Voir les transferts',
                'url' => route('stock.movements.index'),
            ];
        }

        return $alerts;
    }

    private function calculateStockTurnover(int $companyId, int $storeId, Carbon $startDate, Carbon $endDate): float
    {
        // Calcul simplifié de la rotation des stocks
        $avgStock = app(InventoryService::class)->calculateStockValue($companyId, $storeId);

        $cogs = DB::table('documents as d')
            ->join('document_items as di', 'd.id', '=', 'di.document_id')
            ->join('products as p', 'di.product_id', '=', 'p.id')
            ->where('d.company_id', $companyId)
            ->where('d.store_id', $storeId)
            ->whereBetween('d.document_date', [$startDate, $endDate])
            ->sum(DB::raw('di.quantity * COALESCE(p.purchase_price, 0)'));

        return $avgStock['total_cost_value'] > 0 ? $cogs / $avgStock['total_cost_value'] : 0;
    }

    private function getPeriodOptions(): array
    {
        return [
            '1' => 'Aujourd\'hui',
            '7' => '7 derniers jours',
            '30' => '30 derniers jours',
            '90' => '3 derniers mois',
        ];
    }

    private function getTabOptions(): array
    {
        return [
            'overview' => ['label' => 'Vue d\'ensemble', 'icon' => 'home'],
            'stock' => ['label' => 'Stock', 'icon' => 'cube'],
            'sales' => ['label' => 'Ventes', 'icon' => 'chart-bar'],
            'transfers' => ['label' => 'Transferts', 'icon' => 'arrow-path'],
            'analytics' => ['label' => 'Analytics', 'icon' => 'chart-pie'],
        ];
    }
}
