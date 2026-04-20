<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class AdvancedRolesSeeder extends Seeder
{
    /**
     * Permissions regroupées par domaine.
     * Format : 'action.ressource'
     *
     * @var array<string, list<string>>
     */
    private array $permissions = [
        // Produits & Stock
        'products' => [
            'view.products', 'create.products', 'edit.products', 'delete.products',
            'view.stock', 'create.stock-entry', 'create.stock-transfer',
        ],
        // Ventes & Documents
        'sales' => [
            'view.documents', 'create.documents', 'edit.documents', 'delete.documents',
            'validate.documents', 'view.customers', 'create.customers', 'edit.customers',
        ],
        // Achats
        'purchases' => [
            'view.purchases', 'create.purchases', 'edit.purchases',
            'view.suppliers', 'create.suppliers', 'edit.suppliers',
        ],
        // Modules métier (activés séparément par tenant)
        'deliveries' => [
            'view.deliveries', 'create.deliveries', 'edit.deliveries', 'close.deliveries',
            'view.drivers', 'create.drivers', 'edit.drivers',
            'view.vehicles', 'create.vehicles', 'edit.vehicles',
            'view.zones', 'manage.zones',
        ],
        'salaries' => [
            'view.salaries', 'manage.salaries', 'approve.salary-advances',
            'generate.payslips', 'view.payslips',
        ],
        'expenses' => [
            'view.expenses', 'create.expenses', 'edit.expenses', 'delete.expenses',
        ],
        'reports' => [
            'view.reports', 'export.reports',
            'view.dashboard-advanced',
        ],
        // Administration tenant
        'settings' => [
            'manage.company-settings', 'manage.users', 'manage.roles',
            'manage.stores', 'manage.numbering',
        ],
    ];

    /**
     * Permissions par rôle.
     *
     * @var array<string, list<string>>
     */
    private array $rolePermissions = [
        'owner' => [
            // Accès total
            'view.products', 'create.products', 'edit.products', 'delete.products',
            'view.stock', 'create.stock-entry', 'create.stock-transfer',
            'view.documents', 'create.documents', 'edit.documents', 'delete.documents',
            'validate.documents', 'view.customers', 'create.customers', 'edit.customers',
            'view.purchases', 'create.purchases', 'edit.purchases',
            'view.suppliers', 'create.suppliers', 'edit.suppliers',
            'view.deliveries', 'create.deliveries', 'edit.deliveries', 'close.deliveries',
            'view.drivers', 'create.drivers', 'edit.drivers',
            'view.vehicles', 'create.vehicles', 'edit.vehicles',
            'view.zones', 'manage.zones',
            'view.salaries', 'manage.salaries', 'approve.salary-advances',
            'generate.payslips', 'view.payslips',
            'view.expenses', 'create.expenses', 'edit.expenses', 'delete.expenses',
            'view.reports', 'export.reports', 'view.dashboard-advanced',
            'manage.company-settings', 'manage.users', 'manage.roles',
            'manage.stores', 'manage.numbering',
        ],
        'manager' => [
            'view.products', 'create.products', 'edit.products',
            'view.stock', 'create.stock-entry', 'create.stock-transfer',
            'view.documents', 'create.documents', 'edit.documents', 'validate.documents',
            'view.customers', 'create.customers', 'edit.customers',
            'view.purchases', 'create.purchases', 'edit.purchases',
            'view.suppliers', 'create.suppliers', 'edit.suppliers',
            'view.deliveries', 'create.deliveries', 'edit.deliveries', 'close.deliveries',
            'view.drivers', 'create.drivers', 'edit.drivers',
            'view.vehicles', 'create.vehicles', 'edit.vehicles',
            'view.zones', 'manage.zones',
            'view.expenses', 'create.expenses',
            'view.reports', 'export.reports', 'view.dashboard-advanced',
            'manage.stores',
        ],
        'accountant' => [
            'view.products', 'view.stock',
            'view.documents', 'view.customers',
            'view.purchases', 'view.suppliers',
            'view.deliveries',
            'view.salaries', 'manage.salaries', 'approve.salary-advances',
            'generate.payslips', 'view.payslips',
            'view.expenses', 'create.expenses', 'edit.expenses',
            'view.reports', 'export.reports', 'view.dashboard-advanced',
        ],
        'data_operator' => [
            'view.products',
            'view.stock', 'create.stock-entry',
            'view.documents', 'create.documents',
            'view.customers', 'create.customers',
            'view.deliveries', 'create.deliveries',
            'view.expenses', 'create.expenses',
        ],
    ];

    public function run(): void
    {
        // Créer toutes les permissions
        $allPermissions = collect($this->permissions)->flatten()->unique();
        foreach ($allPermissions as $permission) {
            Permission::firstOrCreate(['name' => $permission, 'guard_name' => 'web']);
        }

        // Créer les rôles et assigner les permissions
        foreach ($this->rolePermissions as $roleName => $permissionNames) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);
            $role->syncPermissions($permissionNames);

            $this->command->info("Rôle « {$roleName} » : ".count($permissionNames).' permissions assignées.');
        }

        $this->command->info('Rôles avancés créés : owner, manager, accountant, data_operator');
    }
}
