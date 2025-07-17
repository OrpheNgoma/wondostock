<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Store;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class CompanySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Créer une compagnie de test principale
        $company = Company::factory()->create([
            'name' => 'Pixel Parfait',
            'email' => 'contact@pixelparfait.com',
        ]);

        // 2. Créer le Super-Administrateur pour cette compagnie
        $superAdmin = User::factory()->create([
            'company_id' => $company->id,
            'name' => 'Orphe NGOMA',
            'email' => 'admin@pixel.com',
            'password' => Hash::make('password'),
        ]);
        $superAdmin->assignRole('Super-Administrateur');

        // Lier le propriétaire à la compagnie
        $company->owner_id = $superAdmin->id;
        $company->save();

        // 3. Créer un abonnement pour la compagnie
        $plan = Plan::where('slug', 'pro')->firstOrFail();
        $company->subscription()->create([
            'plan_id' => $plan->id,
            'starts_at' => now(),
            'ends_at' => now()->addYear(),
        ]);

        // 4. Créer des magasins pour cette compagnie
        $store1 = Store::factory()->create(['company_id' => $company->id, 'name' => 'Magasin Central (Libreville)']);
        $store2 = Store::factory()->create(['company_id' => $company->id, 'name' => 'Annexe Oloumi']);

        // 5. Créer un Gérant de magasin et un Vendeur
        $manager = User::factory()->create(['company_id' => $company->id, 'store_id' => $store1->id]);
        $manager->assignRole('Gérant de Magasin');

        $seller = User::factory()->create(['company_id' => $company->id, 'store_id' => $store2->id]);
        $seller->assignRole('Vendeur');
    }
}
