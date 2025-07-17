<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Création des plans définis dans le business plan.
        // Les fonctionnalités sont des slugs que nous utiliserons dans les Gates.
        Plan::firstOrCreate(['slug' => 'essentiel'], [
            'name' => 'WondoStock ESSENTIEL',
            'features' => json_encode([
                'base_stock',
                'invoicing',
                'reporting_essentiel',
            ]),
        ]);

        Plan::firstOrCreate(['slug' => 'pro'], [
            'name' => 'WondoStock PRO',
            'features' => json_encode([
                'base_stock',
                'invoicing',
                'reporting_essentiel',
                'multi_store', // <-- Fonctionnalité PRO
                'roles_permissions', // <-- Fonctionnalité PRO
                'advanced_reporting', // <-- Fonctionnalité PRO
            ]),
        ]);

        Plan::firstOrCreate(['slug' => 'entreprise'], [
            'name' => 'WondoStock ENTREPRISE',
            'features' => json_encode([
                'base_stock',
                'invoicing',
                'reporting_essentiel',
                'multi_store',
                'roles_permissions',
                'advanced_reporting',
                'api_access', // <-- Fonctionnalité ENTREPRISE
                'priority_support', // <-- Fonctionnalité ENTREPRISE
            ]),
        ]);
    }
}
