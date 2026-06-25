<?php

namespace Tests\Feature;

use App\Enums\DeliveryTripStatus;
use App\Livewire\StockPurchases\Index;
use App\Livewire\StockPurchases\TripForm;
use App\Livewire\StockPurchases\TripShow;
use App\Models\Company;
use App\Models\Driver;
use App\Models\Product;
use App\Models\StockPurchaseTrip;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Permission;
use Tests\TestCase;

class StockPurchaseLivewireTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private Store $store;

    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $this->company->id]);
        $this->driver = Driver::create([
            'company_id' => $this->company->id,
            'name' => 'Chauffeur Test',
            'is_active' => true,
        ]);

        $user = User::factory()->create(['company_id' => $this->company->id]);

        setPermissionsTeamId($this->company->id);
        foreach (['view_stock_purchases', 'create_stock_purchases', 'edit_stock_purchases', 'close_stock_purchases'] as $name) {
            Permission::findOrCreate($name, 'web');
        }
        $user->givePermissionTo(['view_stock_purchases', 'create_stock_purchases', 'edit_stock_purchases', 'close_stock_purchases']);

        $this->actingAs($user);
    }

    public function test_index_renders_for_authorized_user(): void
    {
        Livewire::test(Index::class)
            ->assertStatus(200)
            ->assertSee('Achats de stock');
    }

    public function test_form_creates_trip_with_default_allowance(): void
    {
        Livewire::test(TripForm::class)
            ->set('driver_id', $this->driver->id)
            ->set('store_id', $this->store->id)
            ->set('trip_date', now()->toDateString())
            ->call('save')
            ->assertHasNoErrors();

        $trip = StockPurchaseTrip::first();
        $this->assertNotNull($trip);
        $this->assertSame(DeliveryTripStatus::Draft, $trip->status);
        $this->assertSame(10000, $trip->mission_allowance_amount);
        $this->assertSame($this->store->id, $trip->store_id);
    }

    public function test_form_requires_driver_and_store(): void
    {
        Livewire::test(TripForm::class)
            ->set('driver_id', null)
            ->set('store_id', null)
            ->call('save')
            ->assertHasErrors(['driver_id', 'store_id']);
    }

    public function test_full_workflow_through_show_applies_stock(): void
    {
        $product = Product::factory()->create(['company_id' => $this->company->id]);
        $product->stores()->attach($this->store->id, ['quantity' => 5]);

        $trip = StockPurchaseTrip::create([
            'company_id' => $this->company->id,
            'driver_id' => $this->driver->id,
            'store_id' => $this->store->id,
            'trip_date' => now()->toDateString(),
            'status' => DeliveryTripStatus::Draft,
            'mission_allowance_amount' => StockPurchaseTrip::DEFAULT_MISSION_ALLOWANCE,
        ]);

        $component = Livewire::test(TripShow::class, ['trip' => $trip])
            ->set('empty_crates_out', 30)
            ->call('startTrip')
            ->assertHasNoErrors();

        $component->set('purchaseRows', [
            ['product_id' => $product->id, 'name' => $product->name, 'sku' => $product->sku, 'unit_cost' => 4000, 'qty' => 12],
        ])
            ->set('full_crates_in', 30)
            ->call('recordReturn')
            ->assertHasNoErrors()
            ->call('close')
            ->assertHasNoErrors();

        $pivot = $product->stores()->where('store_id', $this->store->id)->first();
        $this->assertSame(17, (int) $pivot->pivot->quantity); // 5 + 12
        $this->assertSame(DeliveryTripStatus::Closed, $trip->fresh()->status);
    }
}
