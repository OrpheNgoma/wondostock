<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = [
            // Dashboard
            'view_dashboard_stats',

            // Produits
            'view_products',
            'create_products',
            'edit_products',
            'delete_products',
            'manage_products',

            // Stock
            'view_stock',
            'create_stock_entries',
            'adjust_stock',
            'transfer_stock',

            // Ventes & Documents
            'view_documents',
            'create_documents',
            'edit_documents',
            'delete_documents',
            'validate_documents',
            'record_payments',

            // Achats
            'view_purchases',
            'create_purchases',
            'edit_purchases',

            // Clients & Fournisseurs
            'manage_customers',
            'manage_suppliers',

            // Dépenses
            'view_expenses',
            'create_expenses',
            'edit_expenses',
            'delete_expenses',

            // Caisse
            'view_cash_sessions',
            'manage_cash_sessions',
            'close_cash_sessions',

            // Livraisons
            'view_deliveries',
            'create_deliveries',
            'edit_deliveries',
            'close_deliveries',

            // Salaires & RH
            'view_salaries',
            'manage_salaries',
            'validate_salaries',
            'approve_salary_advances',

            // Employés
            'view_employees',
            'manage_employees',

            // Finance & Rapports
            'view_financial_reports',
            'view_global_reports',
            'view_store_reports',

            // Administration
            'manage_users',
            'manage_stores',
            'manage_settings',
            'manage_subscriptions',
            'view_all_sales_documents',

            // Audit
            'view_audit_log',
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // ----------------------------------------------------------------
        // Rôle : Vendeur / Caissier
        // Peut créer des ventes et gérer les clients
        // ----------------------------------------------------------------
        $vendeur = Role::firstOrCreate(['name' => 'Vendeur', 'guard_name' => 'web']);
        $vendeur->syncPermissions([
            'view_documents',
            'create_documents',
            'record_payments',
            'manage_customers',
            'view_products',
            'view_stock',
        ]);

        // ----------------------------------------------------------------
        // Rôle : Caissier
        // Ventes + gestion de la caisse
        // ----------------------------------------------------------------
        $caissier = Role::firstOrCreate(['name' => 'Caissier', 'guard_name' => 'web']);
        $caissier->syncPermissions([
            'view_documents',
            'create_documents',
            'record_payments',
            'manage_customers',
            'view_products',
            'view_stock',
            'view_cash_sessions',
            'manage_cash_sessions',
        ]);

        // ----------------------------------------------------------------
        // Rôle : Magasinier
        // Gestion du stock et des achats
        // ----------------------------------------------------------------
        $magasinier = Role::firstOrCreate(['name' => 'Magasinier', 'guard_name' => 'web']);
        $magasinier->syncPermissions([
            'view_products',
            'view_stock',
            'create_stock_entries',
            'adjust_stock',
            'transfer_stock',
            'view_purchases',
            'create_purchases',
            'manage_suppliers',
        ]);

        // ----------------------------------------------------------------
        // Rôle : Comptable / RH
        // Finance, dépenses, salaires
        // ----------------------------------------------------------------
        $comptable = Role::firstOrCreate(['name' => 'Comptable', 'guard_name' => 'web']);
        $comptable->syncPermissions([
            'view_dashboard_stats',
            'view_documents',
            'view_all_sales_documents',
            'view_expenses',
            'create_expenses',
            'edit_expenses',
            'view_cash_sessions',
            'close_cash_sessions',
            'view_salaries',
            'manage_salaries',
            'validate_salaries',
            'approve_salary_advances',
            'view_employees',
            'view_financial_reports',
            'view_store_reports',
            'view_global_reports',
            'view_audit_log',
        ]);

        // ----------------------------------------------------------------
        // Rôle : Gérant de Magasin
        // Tout sauf administration et finance globale
        // ----------------------------------------------------------------
        $gerant = Role::firstOrCreate(['name' => 'Gérant de Magasin', 'guard_name' => 'web']);
        $gerant->syncPermissions([
            'view_dashboard_stats',
            'view_products',
            'create_products',
            'edit_products',
            'manage_products',
            'view_stock',
            'create_stock_entries',
            'adjust_stock',
            'transfer_stock',
            'view_documents',
            'create_documents',
            'edit_documents',
            'validate_documents',
            'record_payments',
            'view_all_sales_documents',
            'view_purchases',
            'create_purchases',
            'edit_purchases',
            'manage_customers',
            'manage_suppliers',
            'view_expenses',
            'create_expenses',
            'edit_expenses',
            'delete_expenses',
            'view_cash_sessions',
            'manage_cash_sessions',
            'close_cash_sessions',
            'view_deliveries',
            'create_deliveries',
            'edit_deliveries',
            'close_deliveries',
            'view_employees',
            'view_store_reports',
            'manage_users',
        ]);

        // ----------------------------------------------------------------
        // Rôle : Administrateur
        // Tout sauf manage_subscriptions
        // ----------------------------------------------------------------
        $admin = Role::firstOrCreate(['name' => 'Administrateur', 'guard_name' => 'web']);
        $adminPerms = Permission::whereNotIn('name', ['manage_subscriptions'])->pluck('name')->toArray();
        $admin->syncPermissions($adminPerms);

        // ----------------------------------------------------------------
        // Rôle : Super-Administrateur
        // Accès total via Gate::before dans AppServiceProvider
        // ----------------------------------------------------------------
        Role::firstOrCreate(['name' => 'Super-Administrateur', 'guard_name' => 'web']);

        // ----------------------------------------------------------------
        // Rôle : Global-Admin (plateforme SaaS)
        // ----------------------------------------------------------------
        $globalAdmin = Role::firstOrCreate(['name' => 'Global-Admin', 'guard_name' => 'web']);
        $globalAdmin->syncPermissions(Permission::all());
    }
}
