<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * En production, utiliser : php artisan db:seed --class=ProductionSeeder
     * En développement        : php artisan db:seed  (appelle ce fichier)
     */
    public function run(): void
    {
        // Données structurelles (identiques à la prod)
        $this->call(ProductionSeeder::class);

        // Données de démonstration (développement uniquement)
        $this->call([
            CompanySeeder::class,
            NumberingSettingsSeeder::class,
            CategorySeeder::class,
            SupplierSeeder::class,
            ProductSeeder::class,
            CustomerSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('Données de test chargées.');
        $this->command->info('Client Admin → admin@pixel.com / password');
    }
}
