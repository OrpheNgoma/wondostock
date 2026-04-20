<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GlobalAdminSeeder extends Seeder
{
    public function run(): void
    {
        $name = env('ADMIN_NAME', 'Admin WondoStock');
        $email = env('ADMIN_EMAIL', 'admin@wondostock.com');
        $password = env('ADMIN_PASSWORD');

        if (! $password) {
            $this->command->error('ADMIN_PASSWORD est absent du fichier .env — le compte Global-Admin n\'a pas été créé.');
            $this->command->warn('Ajoutez ADMIN_PASSWORD=<mot_de_passe_fort> dans votre .env puis relancez le seeder.');

            return;
        }

        $globalAdmin = User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'company_id' => null,
                'is_global_admin' => true,
            ]
        );

        $globalAdmin->syncRoles(['Global-Admin']);

        $this->command->info("Global-Admin créé : {$email}");
    }
}
