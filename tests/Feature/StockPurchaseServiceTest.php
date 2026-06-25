<?php

namespace Tests\Feature;

use App\Enums\DeliveryTripStatus;
use App\Enums\StockMovementType;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Product;
use App\Models\StockPurchaseTrip;
use App\Models\Store;
use App\Models\User;
use App\Services\StockPurchaseService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StockPurchaseServiceTest extends TestCase
{
    use RefreshDatabase;

    private StockPurchaseService $service;

    private Company $company;

    private Store $store;

    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(StockPurchaseService::class);
        $this->company = Company::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $this->company->id]);
        $this->driver = Driver::create([
            'company_id' => $this->company->id,
            'name' => 'Chauffeur Test',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['company_id' => $this->company->id]);
        $this->actingAs($user);
    }

    private function makeTrip(): StockPurchaseTrip
    {
        return StockPurchaseTrip::create([
            'company_id' => $this->company->id,
            'driver_id' => $this->driver->id,
            'store_id' => $this->store->id,
            'trip_date' => now()->toDateString(),
            'status' => DeliveryTripStatus::Draft,
            'mission_allowance_amount' => StockPurchaseTrip::DEFAULT_MISSION_ALLOWANCE,
        ]);
    }

    public function test_start_trip_records_empty_crates_and_moves_to_in_progress(): void
    {
        $trip = $this->makeTrip();

        $trip = $this->service->startTrip($trip, 40);

        $this->assertSame(DeliveryTripStatus::InProgress, $trip->status);
        $this->assertSame(40, $trip->empty_crates_out);
        $this->assertNotNull($trip->departed_at);
    }

    public function test_record_return_creates_items_and_computes_total_cost(): void
    {
        $product = Product::factory()->create(['company_id' => $this->company->id]);
        $trip = $this->service->startTrip($this->makeTrip(), 40);

        $trip = $this->service->recordReturnWithItems($trip, [
            ['product_id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'unit_cost' => 5000, 'qty' => 10],
        ], 40);

        $this->assertSame(DeliveryTripStatus::Completed, $trip->status);
        $this->assertSame(40, $trip->full_crates_in);
        $this->assertSame(50000, $trip->total_purchase_cost);
        $this->assertCount(1, $trip->items);
    }

    public function test_close_applies_stock_entry_to_destination_store(): void
    {
        $product = Product::factory()->create(['company_id' => $this->company->id]);
        $product->stores()->attach($this->store->id, ['quantity' => 15]);

        $trip = $this->service->startTrip($this->makeTrip(), 40);
        $trip = $this->service->recordReturnWithItems($trip, [
            ['product_id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'unit_cost' => 5000, 'qty' => 10],
        ], 40);

        $trip = $this->service->close($trip);

        $this->assertSame(DeliveryTripStatus::Closed, $trip->status);
        $this->assertNotNull($trip->stock_applied_at);

        // Stock du dépôt augmenté : 15 + 10 = 25
        $pivot = $product->stores()->where('store_id', $this->store->id)->first();
        $this->assertSame(25, (int) $pivot->pivot->quantity);

        // Mouvement de stock tracé
        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $product->id,
            'store_id' => $this->store->id,
            'type' => StockMovementType::Purchase->value,
            'quantity' => 10,
            'source_type' => StockPurchaseTrip::class,
            'source_id' => $trip->id,
        ]);
    }

    public function test_close_creates_pivot_when_product_not_yet_in_store(): void
    {
        $product = Product::factory()->create(['company_id' => $this->company->id]);

        $trip = $this->service->startTrip($this->makeTrip(), 40);
        $trip = $this->service->recordReturnWithItems($trip, [
            ['product_id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'unit_cost' => 3000, 'qty' => 8],
        ], 40);
        $trip = $this->service->close($trip);

        $pivot = $product->stores()->where('store_id', $this->store->id)->first();
        $this->assertNotNull($pivot);
        $this->assertSame(8, (int) $pivot->pivot->quantity);
    }

    public function test_mission_allowance_defaults_to_ten_thousand(): void
    {
        $trip = $this->makeTrip();

        $this->assertSame(10000, $trip->mission_allowance_amount);
        $this->assertSame(10000, StockPurchaseTrip::DEFAULT_MISSION_ALLOWANCE);
    }

    public function test_cannot_close_a_trip_that_is_not_completed(): void
    {
        $this->expectException(\Illuminate\Validation\ValidationException::class);

        $trip = $this->service->startTrip($this->makeTrip(), 40);
        $this->service->close($trip); // Encore en in_progress
    }
}
