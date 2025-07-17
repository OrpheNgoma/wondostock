<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Customer;
use App\Models\Product;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardCacheService
{
    private const CACHE_TTL = 900; // 15 minutes
    private const CACHE_PREFIX = 'dashboard_kpis';

    public function getKPIs(int $companyId, int $period, ?int $storeId = null): array
    {
        $cacheKey = $this->buildCacheKey($companyId, $period, $storeId);
        
        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($companyId, $period, $storeId) {
            return $this->calculateKPIs($companyId, $period, $storeId);
        });
    }

    public function invalidateKPIs(int $companyId): void
    {
        $patterns = [
            self::CACHE_PREFIX . ".company_{$companyId}_*",
        ];

        foreach ($patterns as $pattern) {
            $this->forgetCachePattern($pattern);
        }
    }

    private function buildCacheKey(int $companyId, int $period, ?int $storeId = null): string
    {
        $key = self::CACHE_PREFIX . ".company_{$companyId}_period_{$period}";
        
        if ($storeId) {
            $key .= "_store_{$storeId}";
        }
        
        // Ajouter l'heure pour un cache qui se rafraîchit toutes les 15 minutes
        $timeSlot = floor(now()->timestamp / self::CACHE_TTL);
        $key .= "_slot_{$timeSlot}";
        
        return $key;
    }

    private function calculateKPIs(int $companyId, int $period, ?int $storeId = null): array
    {
        $startDate = Carbon::now()->subDays($period);

        // Requête unique super-optimisée pour récupérer toutes les données nécessaires
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

        // Récupération optimisée de toutes les données en une seule requête
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
            DB::raw('(di.quantity * (di.unit_price - COALESCE(p.purchase_price, 0))) as item_profit')
        ])->get();

        // Calculs optimisés à partir des données récupérées
        $uniqueDocuments = $rawData->groupBy('document_id');
        $totalRevenue = $uniqueDocuments->sum(function ($items) {
            return $items->first()->document_total;
        });
        $totalSales = $uniqueDocuments->count();
        $estimatedProfit = $rawData->sum('item_profit');

        // Nouveaux clients (requête séparée car pas de jointure évidente)
        $newCustomersCount = Customer::where('company_id', $companyId)
            ->where('created_at', '>=', $startDate)
            ->count();

        // Données graphiques optimisées
        $chartData = $this->prepareOptimizedChartData($uniqueDocuments, $startDate);
        $categoryData = $this->getOptimizedCategoryData($rawData);
        $topProducts = $this->getOptimizedTopProducts($rawData);
        $lowStockProducts = $this->getLowStockProducts($companyId, $storeId);

        return [
            'totalRevenue' => $totalRevenue,
            'totalSales' => $totalSales,
            'estimatedProfit' => $estimatedProfit,
            'newCustomersCount' => $newCustomersCount,
            'lineChartLabels' => $chartData['labels'],
            'lineChartValues' => $chartData['values'],
            'donutChartLabels' => $categoryData['labels'],
            'donutChartValues' => $categoryData['values'],
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
        ];
    }

    private function calculateEstimatedProfit($documentIds): float
    {
        if ($documentIds->isEmpty()) {
            return 0;
        }

        return DB::table('document_items')
            ->join('products', 'document_items.product_id', '=', 'products.id')
            ->whereIn('document_items.document_id', $documentIds)
            ->sum(DB::raw('document_items.quantity * (document_items.unit_price - COALESCE(products.purchase_price, 0))'));
    }

    private function prepareChartData($kpiData, $startDate): array
    {
        $salesData = $kpiData->pluck('total_revenue', 'date');
        $chartData = [];
        $date = clone $startDate;
        
        while ($date <= now()) {
            $formattedDate = $date->format('Y-m-d');
            $chartData[$formattedDate] = $salesData->get($formattedDate, 0);
            $date->addDay();
        }

        return [
            'labels' => array_keys($chartData),
            'values' => array_values($chartData),
        ];
    }

    private function getSalesByCategory($documentIds): array
    {
        if ($documentIds->isEmpty()) {
            return ['labels' => [], 'values' => []];
        }

        $salesByCategory = DB::table('document_items')
            ->join('products', 'document_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->whereIn('document_items.document_id', $documentIds)
            ->select('categories.name', DB::raw('SUM(document_items.total_amount) as total'))
            ->groupBy('categories.name')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $salesByCategory->pluck('name')->toArray(),
            'values' => $salesByCategory->pluck('total')->toArray(),
        ];
    }

    private function getTopProducts($documentIds)
    {
        if ($documentIds->isEmpty()) {
            return collect();
        }

        return DB::table('document_items')
            ->join('products', 'document_items.product_id', '=', 'products.id')
            ->whereIn('document_items.document_id', $documentIds)
            ->select('products.name', 'products.sku', DB::raw('SUM(document_items.quantity) as total_quantity'))
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_quantity')
            ->limit(5)
            ->get();
    }

    private function getLowStockProducts(int $companyId, ?int $storeId = null)
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

    private function prepareOptimizedChartData($uniqueDocuments, $startDate): array
    {
        $chartData = [];
        $date = clone $startDate;
        
        // Regrouper les ventes par date
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

    private function getOptimizedCategoryData($rawData): array
    {
        $salesByCategory = $rawData
            ->groupBy('category_name')
            ->map(function ($items) {
                return $items->sum('item_total');
            })
            ->sortDesc()
            ->take(5);

        return [
            'labels' => $salesByCategory->keys()->toArray(),
            'values' => $salesByCategory->values()->toArray(),
        ];
    }

    private function getOptimizedTopProducts($rawData)
    {
        return $rawData
            ->groupBy('product_id')
            ->map(function ($items) {
                $firstItem = $items->first();
                return (object) [
                    'name' => $firstItem->product_name,
                    'sku' => $firstItem->product_sku,
                    'total_quantity' => $items->sum('quantity'),
                ];
            })
            ->sortByDesc('total_quantity')
            ->take(5)
            ->values();
    }

    private function forgetCachePattern(string $pattern): void
    {
        // Pour Redis/Memcached, vous pouvez utiliser des patterns
        // Pour simplifier ici, on utilise un système basique
        $cacheStore = Cache::getStore();
        
        if (method_exists($cacheStore, 'flush')) {
            // Option simple : vider tout le cache (seulement en développement)
            // En production, implementer une solution plus sophistiquée
        }
    }
}