<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Réinitialiser le cache des rôles et permissions
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        // -----------------------------------------------------------------
        // Création des Permissions
        // -----------------------------------------------------------------

        // Dashboard
        Permission::firstOrCreate(['name' => 'view_dashboard_stats', 'guard_name' => 'web']);

        // Gestion des Produits
        Permission::firstOrCreate(['name' => 'view_products', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_products', 'guard_name' => 'web']); // Créer, éditer, supprimer

        // Gestion des Stocks
        Permission::firstOrCreate(['name' => 'manage_inventory', 'guard_name' => 'web']); // Faire des inventaires, ajuster
        Permission::firstOrCreate(['name' => 'transfer_stock', 'guard_name' => 'web']); // Transférer entre magasins

        // Gestion des Documents de Vente
        Permission::firstOrCreate(['name' => 'create_sales_documents', 'guard_name' => 'web']); // Devis, Factures, BL...
        Permission::firstOrCreate(['name' => 'view_all_sales_documents', 'guard_name' => 'web']); // Voir les docs de tous les vendeurs
        Permission::firstOrCreate(['name' => 'delete_sales_documents', 'guard_name' => 'web']); // Permission critique

        // Gestion des Clients
        Permission::firstOrCreate(['name' => 'manage_customers', 'guard_name' => 'web']);

        // Gestion des Rapports
        Permission::firstOrCreate(['name' => 'view_store_reports', 'guard_name' => 'web']); // Rapports du magasin assigné
        Permission::firstOrCreate(['name' => 'view_global_reports', 'guard_name' => 'web']); // Rapports de toute l'entreprise

        // Gestion des Utilisateurs et Magasins (Permissions d'Admin)
        Permission::firstOrCreate(['name' => 'manage_users', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_stores', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_settings', 'guard_name' => 'web']);
        Permission::firstOrCreate(['name' => 'manage_subscriptions', 'guard_name' => 'web']);

        // -----------------------------------------------------------------
        // Création des Rôles et Assignation des Permissions
        // -----------------------------------------------------------------

        // Rôle Vendeur / Caissier
        $vendeurRole = Role::firstOrCreate(['name' => 'Vendeur', 'guard_name' => 'web']);
        $vendeurRole->givePermissionTo([
            'create_sales_documents',
            'manage_customers',
        ]);

        // Rôle Gérant de Magasin
        $gerantRole = Role::firstOrCreate(['name' => 'Gérant de Magasin', 'guard_name' => 'web']);
        $gerantRole->givePermissionTo([
            // Tout ce que le vendeur peut faire
            'create_sales_documents',
            'manage_customers',
            // Plus des permissions étendues
            'view_dashboard_stats',
            'view_products',
            'manage_products',
            'manage_inventory',
            'transfer_stock',
            'view_all_sales_documents',
            'view_store_reports',
            'manage_users', // Peut gérer les vendeurs de son magasin (logique à affiner dans le code)
        ]);

        // Rôle Administrateur
        $adminRole = Role::firstOrCreate(['name' => 'Administrateur', 'guard_name' => 'web']);
        // L'admin a la plupart des permissions, sauf les plus critiques
        $adminRole->givePermissionTo([
            'view_dashboard_stats',
            'view_products',
            'manage_products',
            'manage_inventory',
            'transfer_stock',
            'create_sales_documents',
            'view_all_sales_documents',
            'delete_sales_documents',
            'manage_customers',
            'view_store_reports',
            'view_global_reports',
            'manage_users',
            'manage_stores',
            'manage_settings',
        ]);

        // Rôle Super-Administrateur
        // Ce rôle a toutes les permissions. On peut utiliser un Gate::before pour lui donner un accès total.
        // C'est plus propre et plus facile à maintenir que de lui assigner toutes les permissions une par une.
        Role::firstOrCreate(['name' => 'Super-Administrateur', 'guard_name' => 'web']);
        // $superAdminRole->givePermissionTo(Permission::all()); // La méthode simple : on donne tout
        // NOUVEAU RÔLE : Admin Global de la plateforme SaaS
        $globalAdminRole = Role::firstOrCreate(['name' => 'Global-Admin', 'guard_name' => 'web']);
        $globalAdminRole->givePermissionTo(Permission::all()); // La méthode simple : on donne tout
    }
}