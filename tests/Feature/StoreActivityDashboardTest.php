<?php

namespace Tests\Feature;

use App\Models\Company;
use App\Models\Store;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StoreActivityDashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_store_activity_dashboard_is_accessible()
    {
        // Créer une entreprise et un utilisateur
        $company = Company::factory()->create();
        $store = Store::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create([
            'company_id' => $company->id,
            'store_id' => $store->id,
        ]);

        // Simuler l'authentification
        $response = $this->actingAs($user)
            ->get(route('store-activity.dashboard'));

        $response->assertStatus(200);
        $response->assertSee('Activité Magasin');
    }

    public function test_store_activity_dashboard_requires_authentication()
    {
        $response = $this->get(route('store-activity.dashboard'));

        $response->assertRedirect(route('login'));
    }

    public function test_store_activity_dashboard_with_specific_store()
    {
        $company = Company::factory()->create();
        $store = Store::factory()->create(['company_id' => $company->id]);
        $user = User::factory()->create(['company_id' => $company->id]);

        $response = $this->actingAs($user)
            ->get(route('store-activity.dashboard', ['storeId' => $store->id]));

        $response->assertStatus(200);
        $response->assertSee($store->name);
    }
}
