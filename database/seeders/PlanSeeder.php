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
        Plan::updateOrCreate(['slug' => 'essentiel'], [
            'name' => 'WondoStock ESSENTIEL',
            'description' => 'Plan de base pour les petites entreprises avec fonctionnalités essentielles de gestion de stock.',
            'price' => 29.99,
            'user_limit' => 3,
            'unlimited_users' => false,
            'features' => json_encode([
                'base_stock',
                'invoicing',
                'reporting_essentiel',
            ]),
        ]);

        Plan::updateOrCreate(['slug' => 'pro'], [
            'name' => 'WondoStock PRO',
            'description' => 'Plan professionnel avec fonctionnalités avancées pour les moyennes entreprises multi-magasins.',
            'price' => 79.99,
            'user_limit' => 10,
            'unlimited_users' => false,
            'features' => json_encode([
                'base_stock',
                'invoicing',
                'reporting_essentiel',
                'multi_store', // <-- Fonctionnalité PRO
                'roles_permissions', // <-- Fonctionnalité PRO
                'advanced_reporting', // <-- Fonctionnalité PRO
            ]),
        ]);

        Plan::updateOrCreate(['slug' => 'entreprise'], [
            'name' => 'WondoStock ENTREPRISE',
            'description' => 'Plan entreprise avec accès API et support prioritaire pour les grandes organisations.',
            'price' => 149.99,
            'user_limit' => 0, // 0 pour indiquer illimité
            'unlimited_users' => true,
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
