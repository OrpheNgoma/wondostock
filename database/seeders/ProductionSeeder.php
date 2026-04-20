<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder de production — ne contient que les données structurelles
 * indispensables au fonctionnement de l'application.
 *
 * Utilisation : php artisan db:seed --class=ProductionSeeder
 *
 * NE PAS inclure de données de démonstration ou de test ici.
 */
class ProductionSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            // 1. Permissions Spatie + rôles globaux de plateforme
            RoleAndPermissionSeeder::class,

            // 2. Plans d'abonnement (Essentiel, Pro, Entreprise)
            PlanSeeder::class,

            // 3. Unités de mesure par défaut (Pièce, kg, L, …)
            UnitSeeder::class,

            // 4. Compte Global-Admin de la plateforme WondoStock
            //    Credentials via variables d'environnement :
            //    ADMIN_NAME, ADMIN_EMAIL, ADMIN_PASSWORD
            GlobalAdminSeeder::class,
        ]);

        $this->command->info('');
        $this->command->info('✓  Production seedée avec succès.');
        $this->command->info('   Global-Admin : '.config('app.admin_email', env('ADMIN_EMAIL', 'admin@wondostock.com')));
    }
}
