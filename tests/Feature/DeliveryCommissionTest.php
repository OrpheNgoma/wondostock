<?php

namespace Tests\Feature;

use App\Enums\DeliveryTripStatus;
use App\Models\Company;
use App\Models\DeliveryTrip;
use App\Models\Driver;
use App\Models\SalarySlip;
use App\Models\User;
use App\Services\DeliveryService;
use App\Services\SalaryService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeliveryCommissionTest extends TestCase
{
    use RefreshDatabase;

    private Company $company;

    private Driver $driver;

    protected function setUp(): void
    {
        parent::setUp();

        $this->company = Company::factory()->create();
        $this->driver = Driver::create([
            'company_id' => $this->company->id,
            'name' => 'Chauffeur Test',
            'base_salary' => 50000,
            'is_active' => true,
        ]);

        $this->actingAs(User::factory()->create(['company_id' => $this->company->id]));
    }

    /** Crée une tournée « completed » avec une ligne produit, sans zone. */
    private function makeCompletedTrip(int $revenue = 100000, int $marginPerUnit = 2000): DeliveryTrip
    {
        $trip = DeliveryTrip::create([
            'company_id' => $this->company->id,
            'driver_id' => $this->driver->id,
            'zone_id' => null, // commission indépendante de la zone
            'trip_date' => now()->toDateString(),
            'status' => DeliveryTripStatus::Completed,
            'total_revenue' => $revenue,
            'bank_percentage' => 80,
        ]);

        $trip->items()->create([
            'product_designation' => 'REGAB 65cl',
            'qty_delivered' => 10,
            'qty_returned' => 0,
            'unit_price' => (int) ($revenue / 10),
            'margin_per_unit' => $marginPerUnit,
        ]);

        return $trip->fresh(['items']);
    }

    public function test_close_computes_15_percent_commission_on_revenue(): void
    {
        $trip = $this->makeCompletedTrip(revenue: 100000);

        $trip = app(DeliveryService::class)->close($trip);

        $this->assertSame(15000, $trip->commission_amount); // 15% de 100 000
        $this->assertSame(0, $trip->mission_allowance_amount); // plus de prime de zone
    }

    public function test_close_deducts_commission_from_funds(): void
    {
        // revenue 100000, marge = 10 × 2000 = 20000, commission = 15000
        $trip = $this->makeCompletedTrip(revenue: 100000, marginPerUnit: 2000);
        $trip->expenses()->create(['label' => 'Carburant', 'amount' => 5000]);

        $trip = app(DeliveryService::class)->close($trip);

        // funds = 100000 − 20000 (marge) − 5000 (dépenses) − 15000 (commission) = 60000
        $this->assertSame(20000, $trip->total_margin);
        $this->assertSame(5000, $trip->total_expenses);
        $this->assertSame(15000, $trip->commission_amount);
        $this->assertSame(60000, $trip->funds_amount);
    }

    public function test_commission_is_independent_of_zone(): void
    {
        // Aucune zone définie : la commission s'applique quand même
        $trip = app(DeliveryService::class)->close($this->makeCompletedTrip(revenue: 80000));

        $this->assertNull($trip->zone_id);
        $this->assertSame(12000, $trip->commission_amount); // 15% de 80 000
    }

    public function test_salary_slip_uses_trip_commissions_and_no_mission_allowance(): void
    {
        // Deux tournées clôturées ce mois-ci
        app(DeliveryService::class)->close($this->makeCompletedTrip(revenue: 100000)); // 15000
        app(DeliveryService::class)->close($this->makeCompletedTrip(revenue: 50000));  // 7500

        $service = app(SalaryService::class);
        $period = $service->openPeriod($this->company->id, (int) now()->month, (int) now()->year);
        $service->generateSlips($period);

        $slip = SalarySlip::where('period_id', $period->id)
            ->where('driver_id', $this->driver->id)
            ->first();

        $this->assertNotNull($slip);
        $this->assertSame(22500, $slip->total_commissions); // 15000 + 7500
        $this->assertSame(0, $slip->mission_allowances);
        $this->assertSame(2, $slip->trips_count);
        $this->assertSame(72500, $slip->gross_salary); // 50000 base + 22500 commission
    }
}
