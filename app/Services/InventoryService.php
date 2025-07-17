<?php

namespace App\Services;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Support\Facades\DB;

class InventoryService
{
    public function __construct(
        private DashboardCacheService $dashboardCacheService
    ) {}

    /**
     * Add stock to a product in a specific store
     */
    public function addStock(
        Product $product, 
        Store $store, 
        int $quantity, 
        ?string $reason = null,
        ?float $unitCost = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $store, $quantity, $reason, $unitCost) {
            // Create stock movement record
            $movement = StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'store_id' => $store->id,
                'type' => StockMovementType::Entry,
                'quantity' => $quantity,
                'unit_cost' => $unitCost,
                'reason' => $reason ?? 'Manual stock entry',
                'reference' => $this->generateMovementReference(),
            ]);

            // Update product stock in store
            $this->updateProductStockInStore($product, $store, $quantity);

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($product->company_id);

            return $movement;
        });
    }

    /**
     * Remove stock from a product in a specific store
     */
    public function removeStock(
        Product $product, 
        Store $store, 
        int $quantity, 
        ?string $reason = null
    ): StockMovement {
        return DB::transaction(function () use ($product, $store, $quantity, $reason) {
            // Check available stock
            $availableStock = $this->getProductStockInStore($product, $store);
            if ($availableStock < $quantity) {
                throw new \InvalidArgumentException(
                    "Insufficient stock. Available: {$availableStock}, Requested: {$quantity}"
                );
            }

            // Create stock movement record
            $movement = StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'store_id' => $store->id,
                'type' => StockMovementType::Exit,
                'quantity' => -$quantity, // Negative for exit
                'reason' => $reason ?? 'Manual stock removal',
                'reference' => $this->generateMovementReference(),
            ]);

            // Update product stock in store
            $this->updateProductStockInStore($product, $store, -$quantity);

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($product->company_id);

            return $movement;
        });
    }

    /**
     * Transfer stock between stores
     */
    public function transferStock(
        Product $product,
        Store $fromStore,
        Store $toStore,
        int $quantity,
        ?string $reason = null
    ): array {
        return DB::transaction(function () use ($product, $fromStore, $toStore, $quantity, $reason) {
            // Check available stock in source store
            $availableStock = $this->getProductStockInStore($product, $fromStore);
            if ($availableStock < $quantity) {
                throw new \InvalidArgumentException(
                    "Insufficient stock in source store. Available: {$availableStock}, Requested: {$quantity}"
                );
            }

            $reference = $this->generateMovementReference();
            $transferReason = $reason ?? "Transfer from {$fromStore->name} to {$toStore->name}";

            // Create exit movement from source store
            $exitMovement = StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'store_id' => $fromStore->id,
                'type' => StockMovementType::Transfer,
                'quantity' => -$quantity,
                'reason' => $transferReason,
                'reference' => $reference,
            ]);

            // Create entry movement to destination store
            $entryMovement = StockMovement::create([
                'company_id' => $product->company_id,
                'product_id' => $product->id,
                'store_id' => $toStore->id,
                'type' => StockMovementType::Transfer,
                'quantity' => $quantity,
                'reason' => $transferReason,
                'reference' => $reference,
            ]);

            // Update stock in both stores
            $this->updateProductStockInStore($product, $fromStore, -$quantity);
            $this->updateProductStockInStore($product, $toStore, $quantity);

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($product->company_id);

            return [$exitMovement, $entryMovement];
        });
    }

    /**
     * Get current stock level for a product in a specific store
     */
    public function getProductStockInStore(Product $product, Store $store): int
    {
        // Check if there's a pivot record
        $pivot = $product->stores()->where('store_id', $store->id)->first();
        return $pivot ? $pivot->pivot->quantity : 0;
    }

    /**
     * Get low stock products for a company
     */
    public function getLowStockProducts(int $companyId, ?int $storeId = null): \Illuminate\Support\Collection
    {
        $query = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->with(['stores' => function ($query) use ($storeId) {
                $query->whereRaw('quantity <= low_stock_threshold');
                if ($storeId) {
                    $query->where('stores.id', $storeId);
                }
            }]);

        return $query->get()->filter(function ($product) {
            return $product->stores->isNotEmpty();
        });
    }

    /**
     * Get stock movement history for a product
     */
    public function getProductStockHistory(
        Product $product, 
        ?Store $store = null, 
        ?\Carbon\Carbon $startDate = null,
        ?\Carbon\Carbon $endDate = null
    ): \Illuminate\Support\Collection {
        $query = StockMovement::where('product_id', $product->id);

        if ($store) {
            $query->where('store_id', $store->id);
        }

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('created_at', '<=', $endDate);
        }

        return $query->with(['store', 'product'])
            ->orderBy('created_at', 'desc')
            ->get();
    }

    /**
     * Calculate total stock value for a company
     */
    public function calculateStockValue(int $companyId, ?int $storeId = null): array
    {
        $query = DB::table('products as p')
            ->join('product_store as ps', 'p.id', '=', 'ps.product_id')
            ->where('p.company_id', $companyId)
            ->where('p.is_active', true);

        if ($storeId) {
            $query->where('ps.store_id', $storeId);
        }

        $results = $query->select([
            DB::raw('SUM(ps.quantity) as total_quantity'),
            DB::raw('SUM(ps.quantity * COALESCE(p.purchase_price, 0)) as total_cost_value'),
            DB::raw('SUM(ps.quantity * p.selling_price) as total_sale_value'),
            DB::raw('COUNT(DISTINCT p.id) as unique_products'),
        ])->first();

        return [
            'total_quantity' => $results->total_quantity ?? 0,
            'total_cost_value' => $results->total_cost_value ?? 0,
            'total_sale_value' => $results->total_sale_value ?? 0,
            'unique_products' => $results->unique_products ?? 0,
            'estimated_profit' => ($results->total_sale_value ?? 0) - ($results->total_cost_value ?? 0),
        ];
    }

    private function updateProductStockInStore(Product $product, Store $store, int $quantityChange): void
    {
        $product->stores()->syncWithoutDetaching([
            $store->id => [
                'quantity' => DB::raw("GREATEST(0, COALESCE(quantity, 0) + {$quantityChange})")
            ]
        ]);
    }

    private function generateMovementReference(): string
    {
        return 'SM-' . now()->format('YmdHis') . '-' . strtoupper(substr(uniqid(), -4));
    }
}