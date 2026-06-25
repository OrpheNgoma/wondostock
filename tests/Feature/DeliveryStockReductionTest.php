<?php

namespace Tests\Feature;

use App\Enums\DeliveryTripStatus;
use App\Enums\StockMovementType;
use App\Models\Company;
use App\Models\DeliveryTrip;
use App\Models\Driver;
use App\Models\Product;
use App\Models\Store;
use App\Models\User;
use App\Services\DeliveryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryStockReductionTest extends TestCase
{
    use RefreshDatabase;

    private DeliveryService $service;

    private Company $company;

    private Store $store;

    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(DeliveryService::class);
        $this->company = Company::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $this->company->id]);
        $this->driver = Driver::create([
            'company_id' => $this->company->id,
            'name' => 'Chauffeur Test',
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create(['company_id' => $this->company->id]));
    }

    private function makeProductWithStock(int $qty): Product
    {
        $product = Product::factory()->create(['company_id' => $this->company->id]);
        $product->stores()->attach($this->store->id, ['quantity' => $qty]);

        return $product;
    }

    private function loadedTrip(Product $product, int $deliveredQty): DeliveryTrip
    {
        $trip = DeliveryTrip::create([
            'company_id' => $this->company->id,
            'driver_id' => $this->driver->id,
            'store_id' => $this->store->id,
            'zone_id' => null,
            'trip_date' => now()->toDateString(),
            'status' => DeliveryTripStatus::Draft,
            'bank_percentage' => 80,
        ]);

        return $this->service->loadWithItems($trip, [[
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit_price' => 1000,
            'margin_per_unit' => 200,
            'qty' => $deliveredQty,
        ]]);
    }

    private function stockInStore(Product $product): int
    {
        return (int) $product->stores()->where('store_id', $this->store->id)->first()->pivot->quantity;
    }

    public function test_loading_does_not_reduce_stock(): void
    {
        $product = $this->makeProductWithStock(100);

        $this->loadedTrip($product, 20);

        // Le départ (chargement) ne décrémente pas encore l'inventaire
        $this->assertSame(100, $this->stockInStore($product));
    }

    public function test_return_with_all_sold_reduces_full_delivered_quantity(): void
    {
        $product = $this->makeProductWithStock(100);
        $trip = $this->loadedTrip($product, 20);
        $itemId = $trip->items->first()->id;

        // Aucun casier retourné => tout vendu
        $this->service->recordReturnFromItems($trip, [(string) $itemId => 0]);

        $this->assertSame(80, $this->stockInStore($product)); // 100 − 20
    }

    public function test_return_with_some_returned_reduces_only_sold(): void
    {
        $product = $this->makeProductWithStock(100);
        $trip = $this->loadedTrip($product, 20);
        $itemId = $trip->items->first()->id;

        // 5 retournés => 15 vendus
        $this->service->recordReturnFromItems($trip, [(string) $itemId => 5]);

        $this->assertSame(85, $this->stockInStore($product)); // 100 − 15
    }

    public function test_reduction_is_clamped_at_zero_when_stock_insufficient(): void
    {
        $product = $this->makeProductWithStock(10);
        $trip = $this->loadedTrip($product, 20);
        $itemId = $trip->items->first()->id;

        $this->service->recordReturnFromItems($trip, [(string) $itemId => 0]); // 20 vendus, stock 10

        $this->assertSame(0, $this->stockInStore($product)); // plafonné à 0, pas négatif
    }

    public function test_return_records_sale_stock_movement(): void
    {
        $product = $this->makeProductWithStock(100);
        $trip = $this->loadedTrip($product, 20);
        $itemId = $trip->items->first()->id;

        $this->service->recordReturnFromItems($trip, [(string) $itemId => 5]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'store_id' => $this->store->id,
            'type' => StockMovementType::Sale->value,
            'quantity' => -15,
            'source_type' => DeliveryTrip::class,
            'source_id' => $trip->id,
        ]);
    }

    public function test_no_reduction_without_source_store(): void
    {
        $product = $this->makeProductWithStock(100);

        $trip = DeliveryTrip::create([
            'company_id' => $this->company->id,
            'driver_id' => $this->driver->id,
            'store_id' => null,
            'trip_date' => now()->toDateString(),
            'status' => DeliveryTripStatus::Draft,
            'bank_percentage' => 80,
        ]);
        $trip = $this->service->loadWithItems($trip, [[
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'unit_price' => 1000,
            'margin_per_unit' => 200,
            'qty' => 20,
        ]]);

        $this->service->recordReturnFromItems($trip, [(string) $trip->items->first()->id => 0]);

        $this->assertSame(100, $this->stockInStore($product)); // pas de dépôt => pas de décrément
    }
}
