<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class ReportingService
{
    public function __construct(
        private DashboardCacheService $dashboardCacheService,
        private InventoryService $inventoryService
    ) {}

    /**
     * Generate sales report for a period
     */
    public function generateSalesReport(
        int $companyId,
        Carbon $startDate,
        Carbon $endDate,
        ?int $storeId = null
    ): array {
        $query = DB::table('documents as d')
            ->join('document_items as di', 'd.id', '=', 'di.document_id')
            ->join('products as p', 'di.product_id', '=', 'p.id')
            ->leftJoin('customers as c', 'd.customer_id', '=', 'c.id')
            ->leftJoin('categories as cat', 'p.category_id', '=', 'cat.id')
            ->where('d.company_id', $companyId)
            ->where('d.type', DocumentType::Invoice->value)
            ->whereIn('d.status', [
                DocumentStatus::Validated->value,
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
            ])
            ->whereBetween('d.document_date', [$startDate, $endDate]);

        if ($storeId) {
            $query->where('d.store_id', $storeId);
        }

        // Summary statistics
        $summary = $query->select([
            DB::raw('COUNT(DISTINCT d.id) as total_invoices'),
            DB::raw('SUM(di.quantity) as total_quantity_sold'),
            DB::raw('SUM(di.total_amount) as total_revenue'),
            DB::raw('AVG(d.total_amount) as average_invoice_value'),
            DB::raw('COUNT(DISTINCT d.customer_id) as unique_customers'),
            DB::raw('COUNT(DISTINCT di.product_id) as unique_products_sold'),
        ])->first();

        // Sales by day
        $dailySalesQuery = clone $query;
        $dailySales = $dailySalesQuery->select([
            DB::raw('DATE(d.document_date) as date'),
            DB::raw('COUNT(DISTINCT d.id) as invoices_count'),
            DB::raw('SUM(di.total_amount) as daily_revenue'),
            DB::raw('SUM(di.quantity) as daily_quantity'),
        ])
        ->groupBy(DB::raw('DATE(d.document_date)'))
        ->orderBy(DB::raw('DATE(d.document_date)'))
        ->get();

        // Top products by revenue
        $topProductsQuery = clone $query;
        $topProductsByRevenue = $topProductsQuery->select([
            'p.id',
            'p.name',
            'p.sku',
            DB::raw('SUM(di.quantity) as total_quantity'),
            DB::raw('SUM(di.total_amount) as total_revenue'),
            DB::raw('AVG(di.unit_price) as average_price'),
        ])
        ->groupBy('p.id', 'p.name', 'p.sku')
        ->orderByDesc('total_revenue')
        ->limit(10)
        ->get();

        // Sales by category
        $categoryQuery = clone $query;
        $salesByCategory = $categoryQuery->select([
            'cat.id',
            'cat.name',
            DB::raw('SUM(di.quantity) as total_quantity'),
            DB::raw('SUM(di.total_amount) as total_revenue'),
            DB::raw('COUNT(DISTINCT di.product_id) as products_count'),
        ])
        ->groupBy('cat.id', 'cat.name')
        ->orderByDesc('total_revenue')
        ->get();

        // Top customers
        $customersQuery = clone $query;
        $topCustomers = $customersQuery->select([
            'c.id',
            'c.name',
            'c.email',
            DB::raw('COUNT(DISTINCT d.id) as invoices_count'),
            DB::raw('SUM(di.total_amount) as total_spent'),
            DB::raw('AVG(d.total_amount) as average_order_value'),
        ])
        ->groupBy('c.id', 'c.name', 'c.email')
        ->orderByDesc('total_spent')
        ->limit(10)
        ->get();

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'days' => $startDate->diffInDays($endDate) + 1,
            ],
            'summary' => $summary,
            'daily_sales' => $dailySales,
            'top_products_by_revenue' => $topProductsByRevenue,
            'sales_by_category' => $salesByCategory,
            'top_customers' => $topCustomers,
        ];
    }

    /**
     * Generate inventory report
     */
    public function generateInventoryReport(int $companyId, ?int $storeId = null): array
    {
        // Stock value calculation
        $stockValue = $this->inventoryService->calculateStockValue($companyId, $storeId);

        // Low stock products
        $lowStockProducts = $this->inventoryService->getLowStockProducts($companyId, $storeId);

        // Stock by category
        $stockByCategory = DB::table('products as p')
            ->join('product_store as ps', 'p.id', '=', 'ps.product_id')
            ->leftJoin('categories as c', 'p.category_id', '=', 'c.id')
            ->where('p.company_id', $companyId)
            ->where('p.is_active', true)
            ->when($storeId, function ($query) use ($storeId) {
                return $query->where('ps.store_id', $storeId);
            })
            ->select([
                'c.id',
                'c.name',
                DB::raw('COUNT(DISTINCT p.id) as products_count'),
                DB::raw('SUM(ps.quantity) as total_quantity'),
                DB::raw('SUM(ps.quantity * p.selling_price) as total_value'),
            ])
            ->groupBy('c.id', 'c.name')
            ->orderByDesc('total_value')
            ->get();

        // Most valuable products
        $mostValuableProducts = DB::table('products as p')
            ->join('product_store as ps', 'p.id', '=', 'ps.product_id')
            ->where('p.company_id', $companyId)
            ->where('p.is_active', true)
            ->when($storeId, function ($query) use ($storeId) {
                return $query->where('ps.store_id', $storeId);
            })
            ->select([
                'p.id',
                'p.name',
                'p.sku',
                'ps.quantity',
                'p.selling_price',
                DB::raw('ps.quantity * p.selling_price as total_value'),
            ])
            ->orderByDesc('total_value')
            ->limit(20)
            ->get();

        // Products with no stock
        $outOfStockProducts = DB::table('products as p')
            ->leftJoin('product_store as ps', 'p.id', '=', 'ps.product_id')
            ->where('p.company_id', $companyId)
            ->where('p.is_active', true)
            ->where(function ($query) {
                $query->whereNull('ps.quantity')
                      ->orWhere('ps.quantity', '<=', 0);
            })
            ->when($storeId, function ($query) use ($storeId) {
                return $query->where('ps.store_id', $storeId);
            })
            ->select('p.id', 'p.name', 'p.sku', 'p.selling_price')
            ->get();

        return [
            'stock_value' => $stockValue,
            'low_stock_products' => $lowStockProducts,
            'out_of_stock_products' => $outOfStockProducts,
            'stock_by_category' => $stockByCategory,
            'most_valuable_products' => $mostValuableProducts,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate profit and loss report
     */
    public function generateProfitLossReport(
        int $companyId,
        Carbon $startDate,
        Carbon $endDate,
        ?int $storeId = null
    ): array {
        // Revenue from sales
        $revenueData = DB::table('documents as d')
            ->join('document_items as di', 'd.id', '=', 'di.document_id')
            ->where('d.company_id', $companyId)
            ->where('d.type', DocumentType::Invoice->value)
            ->whereIn('d.status', [
                DocumentStatus::Validated->value,
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
            ])
            ->whereBetween('d.document_date', [$startDate, $endDate])
            ->when($storeId, function ($query) use ($storeId) {
                return $query->where('d.store_id', $storeId);
            })
            ->select([
                DB::raw('SUM(di.total_amount) as total_revenue'),
                DB::raw('SUM(di.quantity * COALESCE(p.purchase_price, 0)) as cost_of_goods_sold'),
                DB::raw('COUNT(DISTINCT d.id) as total_invoices'),
            ])
            ->leftJoin('products as p', 'di.product_id', '=', 'p.id')
            ->first();

        $totalRevenue = $revenueData->total_revenue ?? 0;
        $costOfGoodsSold = $revenueData->cost_of_goods_sold ?? 0;
        $grossProfit = $totalRevenue - $costOfGoodsSold;
        $grossProfitMargin = $totalRevenue > 0 ? ($grossProfit / $totalRevenue) * 100 : 0;

        // Monthly breakdown
        $monthlyBreakdown = DB::table('documents as d')
            ->join('document_items as di', 'd.id', '=', 'di.document_id')
            ->leftJoin('products as p', 'di.product_id', '=', 'p.id')
            ->where('d.company_id', $companyId)
            ->where('d.type', DocumentType::Invoice->value)
            ->whereIn('d.status', [
                DocumentStatus::Validated->value,
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
            ])
            ->whereBetween('d.document_date', [$startDate, $endDate])
            ->when($storeId, function ($query) use ($storeId) {
                return $query->where('d.store_id', $storeId);
            })
            ->select([
                DB::raw('YEAR(d.document_date) as year'),
                DB::raw('MONTH(d.document_date) as month'),
                DB::raw('SUM(di.total_amount) as revenue'),
                DB::raw('SUM(di.quantity * COALESCE(p.purchase_price, 0)) as cogs'),
                DB::raw('COUNT(DISTINCT d.id) as invoices_count'),
            ])
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->map(function ($item) {
                $item->gross_profit = $item->revenue - $item->cogs;
                $item->gross_profit_margin = $item->revenue > 0 ? ($item->gross_profit / $item->revenue) * 100 : 0;
                return $item;
            });

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => [
                'total_revenue' => $totalRevenue,
                'cost_of_goods_sold' => $costOfGoodsSold,
                'gross_profit' => $grossProfit,
                'gross_profit_margin' => round($grossProfitMargin, 2),
                'total_invoices' => $revenueData->total_invoices ?? 0,
            ],
            'monthly_breakdown' => $monthlyBreakdown,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }

    /**
     * Generate customer analysis report
     */
    public function generateCustomerAnalysisReport(
        int $companyId,
        Carbon $startDate,
        Carbon $endDate
    ): array {
        // Customer statistics
        $customerStats = DB::table('customers')
            ->where('company_id', $companyId)
            ->select([
                DB::raw('COUNT(*) as total_customers'),
                DB::raw('COUNT(CASE WHEN created_at >= ? THEN 1 END) as new_customers'),
            ])
            ->addBinding($startDate, 'select')
            ->first();

        // Customer purchase behavior
        $customerBehavior = DB::table('documents as d')
            ->join('customers as c', 'd.customer_id', '=', 'c.id')
            ->where('d.company_id', $companyId)
            ->where('d.type', DocumentType::Invoice->value)
            ->whereIn('d.status', [
                DocumentStatus::Validated->value,
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
            ])
            ->whereBetween('d.document_date', [$startDate, $endDate])
            ->select([
                'c.id',
                'c.name',
                'c.email',
                DB::raw('COUNT(d.id) as total_orders'),
                DB::raw('SUM(d.total_amount) as total_spent'),
                DB::raw('AVG(d.total_amount) as average_order_value'),
                DB::raw('MAX(d.document_date) as last_order_date'),
                DB::raw('MIN(d.document_date) as first_order_date'),
            ])
            ->groupBy('c.id', 'c.name', 'c.email')
            ->orderByDesc('total_spent')
            ->get();

        // Customer segmentation
        $segments = [
            'high_value' => $customerBehavior->where('total_spent', '>', 10000)->count(),
            'medium_value' => $customerBehavior->whereBetween('total_spent', [1000, 10000])->count(),
            'low_value' => $customerBehavior->where('total_spent', '<', 1000)->count(),
            'frequent_buyers' => $customerBehavior->where('total_orders', '>', 5)->count(),
            'one_time_buyers' => $customerBehavior->where('total_orders', '=', 1)->count(),
        ];

        return [
            'period' => [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
            ],
            'summary' => $customerStats,
            'customer_behavior' => $customerBehavior->take(50), // Top 50 customers
            'segments' => $segments,
            'generated_at' => now()->format('Y-m-d H:i:s'),
        ];
    }
}