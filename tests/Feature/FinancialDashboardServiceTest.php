<?php

namespace Tests\Feature;

use App\Enums\DeliveryTripStatus;
use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Company;
use App\Models\DeliveryTrip;
use App\Models\Document;
use App\Models\Driver;
use App\Models\Store;
use App\Models\User;
use App\Services\FinancialDashboardService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FinancialDashboardServiceTest extends TestCase
{
    use RefreshDatabase;

    private FinancialDashboardService $service;

    private Company $company;

    private Store $store;

    private User $user;

    protected function setUp(): void
    {
        parent::setUp();

        $this->service = app(FinancialDashboardService::class);
        $this->company = Company::factory()->create();
        $this->store = Store::factory()->create(['company_id' => $this->company->id]);
        $this->user = User::factory()->create(['company_id' => $this->company->id]);

        $this->actingAs($this->user);
    }

    private function makeDocument(DocumentType $type, DocumentStatus $status, int $subTotal): Document
    {
        return Document::create([
            'company_id' => $this->company->id,
            'store_id' => $this->store->id,
            'user_id' => $this->user->id,
            'type' => $type,
            'status' => $status,
            'document_number' => 'DOC-'.uniqid(),
            'document_date' => now()->toDateString(),
            'sub_total' => $subTotal,
            'tax_amount' => (int) round($subTotal * 0.1),
            'total_amount' => $subTotal + (int) round($subTotal * 0.1),
        ]);
    }

    private function closedTrip(array $overrides = []): DeliveryTrip
    {
        $driver = Driver::create(['company_id' => $this->company->id, 'name' => 'D', 'is_active' => true]);

        return DeliveryTrip::create(array_merge([
            'company_id' => $this->company->id,
            'driver_id' => $driver->id,
            'trip_date' => now()->toDateString(),
            'status' => DeliveryTripStatus::Closed,
            'total_revenue' => 100000,
            'total_margin' => 20000,
            'total_expenses' => 5000,
            'commission_amount' => 15000,
            'funds_amount' => 60000,
        ], $overrides));
    }

    private function summary(): array
    {
        return $this->service->getSummary($this->company->id, (int) now()->month, (int) now()->year);
    }

    public function test_sales_revenue_is_invoices_minus_credit_notes_in_ht(): void
    {
        $this->makeDocument(DocumentType::Invoice, DocumentStatus::Validated, 10000);
        $this->makeDocument(DocumentType::CreditNote, DocumentStatus::Validated, 2000);

        // Doivent être ignorés :
        $this->makeDocument(DocumentType::PurchaseOrder, DocumentStatus::Validated, 5000); // achat
        $this->makeDocument(DocumentType::Invoice, DocumentStatus::Draft, 9999);           // non finalisé
        $this->makeDocument(DocumentType::Quote, DocumentStatus::Validated, 7000);         // devis

        // 10000 (facture HT) − 2000 (avoir HT), hors TVA, hors achat/devis/brouillon
        $this->assertSame(8000, $this->summary()['documents_revenue']);
    }

    public function test_delivery_funds_and_commissions_are_counted_as_charges(): void
    {
        $this->closedTrip();

        $s = $this->summary();

        $this->assertSame(100000, $s['deliveries_revenue']); // recette brute en revenu
        $this->assertSame(60000, $s['delivery_funds']);
        $this->assertSame(15000, $s['delivery_commissions']);
        $this->assertSame(5000, $s['expenses_delivery']);
    }

    public function test_base_salaries_are_included_in_charges(): void
    {
        Driver::create(['company_id' => $this->company->id, 'name' => 'D1', 'base_salary' => 50000, 'is_active' => true]);
        Driver::create(['company_id' => $this->company->id, 'name' => 'D2', 'base_salary' => 30000, 'is_active' => false]); // inactif ignoré

        $this->assertSame(50000, $this->summary()['salaries_base']);
    }

    public function test_net_result_reconciles_revenue_minus_all_charges(): void
    {
        $this->makeDocument(DocumentType::Invoice, DocumentStatus::Validated, 8000);
        $this->closedTrip(['driver_id' => Driver::create(['company_id' => $this->company->id, 'name' => 'Dx', 'base_salary' => 50000, 'is_active' => true])->id]);

        $s = $this->summary();

        // Revenus = 8000 + 100000 = 108000
        $this->assertSame(108000, $s['total_revenue']);
        // Charges = 0 (gén.) + 5000 (tournées) + 60000 (fonds) + 15000 (commissions) + 50000 (salaires) = 130000
        $this->assertSame(130000, $s['total_charges']);
        // Net = 108000 − 130000 = −22000  (contribution livraison = marge 20000, ventes 8000, − salaires 50000)
        $this->assertSame(-22000, $s['net_result']);
    }

    public function test_evolution_is_null_when_previous_period_has_no_base(): void
    {
        $this->makeDocument(DocumentType::Invoice, DocumentStatus::Validated, 10000);

        // Aucune donnée le mois précédent => base 0 => évolution nulle (pas de % absurde)
        $this->assertNull($this->summary()['evolution']['revenue']);
    }
}
