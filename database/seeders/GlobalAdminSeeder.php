<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class GlobalAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Création de l'administrateur principal de la plateforme WondoStock
        $globalAdmin = User::factory()->create([
            'name' => 'Admin WondoStock',
            'email' => 'admin@wondostock.com',
            'password' => Hash::make('password'),
            'company_id' => null, // Cet utilisateur n'appartient à aucune compagnie cliente
        ]);

        $globalAdmin->assignRole('Global-Admin');
    }
}