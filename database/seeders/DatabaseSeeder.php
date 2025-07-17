<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Seeders de base (indépendants)
        $this->call([
            RoleAndPermissionSeeder::class,
            PlanSeeder::class,
            UnitSeeder::class,
            GlobalAdminSeeder::class,
        ]);

        // 2. Seeders qui créent les données de test
        $this->call([
            CompanySeeder::class,
            NumberingSettingsSeeder::class,
            SupplierSeeder::class, // On ajoute le seeder des fournisseurs
            ProductSeeder::class,
            CustomerSeeder::class,
            // Ajoutez ici d'autres seeders si nécessaire (ex: DocumentSeeder)
        ]);

        $this->command->info('Base de données remplie avec des données de test !');
        $this->command->info('Global-Admin Email: admin@wondostock.com | Mot de passe: password');
        $this->command->info('Client Admin Email: admin@pixel.com | Mot de passe: password');
    }
}
