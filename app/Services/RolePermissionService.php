<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

/**
 * Service pour la gestion des rôles et permissions avec isolation par entreprise.
 */
class RolePermissionService
{
    /**
     * Rôles système protégés : ne peuvent pas être renommés ni supprimés.
     *
     * @var array<string>
     */
    public const PROTECTED_ROLES = ['Super-Administrateur'];

    /**
     * Vérifie si l'utilisateur actuel peut gérer les rôles et permissions.
     */
    public function canManageRoles(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        return $user->can('feature-roles-permissions') || $this->isCompanyOwner($user);
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de l'entreprise.
     */
    public function isCompanyOwner(User $user): bool
    {
        return $user->company && $user->company->owner_id === $user->id;
    }

    /**
     * Récupère tous les rôles de l'entreprise actuelle.
     */
    public function getCompanyRoles(?int $companyId = null): \Illuminate\Database\Eloquent\Collection
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        return Role::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Récupère toutes les permissions disponibles.
     */
    public function getAvailablePermissions(): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Crée un nouveau rôle pour l'entreprise.
     */
    public function createRole(string $name, ?string $description = null, ?int $companyId = null): Role
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        $role = DB::transaction(function () use ($name, $description, $companyId): Role {
            return Role::create([
                'name' => $name,
                'description' => $description,
                'guard_name' => 'web',
                'company_id' => $companyId,
            ]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role;
    }

    /**
     * Met à jour un rôle existant.
     *
     * @throws \Exception
     */
    public function updateRole(Role $role, string $name, ?string $description = null): Role
    {
        $this->ensureRoleBelongsToCompany($role);
        $this->ensureRoleIsNotProtected($role);

        DB::transaction(function () use ($role, $name, $description): void {
            $role->update([
                'name' => $name,
                'description' => $description,
            ]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $role;
    }

    /**
     * Supprime un rôle.
     *
     * @throws \Exception
     */
    public function deleteRole(Role $role): bool
    {
        $this->ensureRoleBelongsToCompany($role);
        $this->ensureRoleIsNotProtected($role);

        if ($role->users()->count() > 0) {
            throw new \Exception('Ce rôle ne peut pas être supprimé car il est assigné à des utilisateurs.');
        }

        $deleted = DB::transaction(function () use ($role): bool {
            return (bool) $role->delete();
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();

        return $deleted;
    }

    /**
     * Synchronise les permissions d'un rôle (remplace les permissions existantes).
     *
     * @param  array<int>  $permissionIds
     *
     * @throws \Exception
     */
    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        $this->ensureRoleBelongsToCompany($role);

        $permissions = Permission::whereIn('id', $permissionIds)->get();

        DB::transaction(function () use ($role, $permissions): void {
            $role->syncPermissions($permissions);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Assigne un rôle à un utilisateur (cumul — n'efface pas les rôles existants).
     *
     * @throws \Exception
     */
    public function assignRoleToUser(User $user, Role $role): void
    {
        $this->ensureUserBelongsToCompany($user);
        $this->ensureRoleBelongsToCompany($role);

        if ($user->company_id !== $role->company_id) {
            throw new \Exception('L\'utilisateur et le rôle doivent appartenir à la même entreprise.');
        }

        DB::transaction(function () use ($user, $role): void {
            setPermissionsTeamId($user->company_id);
            $user->assignRole($role);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Remplace tous les rôles d'un utilisateur par un seul rôle (sync).
     * Utilisé lors de la création/édition d'un utilisateur depuis le formulaire.
     *
     * @throws \Exception
     */
    public function syncUserRole(User $user, Role $role): void
    {
        $this->ensureUserBelongsToCompany($user);
        $this->ensureRoleBelongsToCompany($role);

        if ($user->company_id !== $role->company_id) {
            throw new \Exception('L\'utilisateur et le rôle doivent appartenir à la même entreprise.');
        }

        DB::transaction(function () use ($user, $role): void {
            setPermissionsTeamId($user->company_id);
            $user->syncRoles([$role]);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Retire un rôle d'un utilisateur.
     *
     * @throws \Exception
     */
    public function removeRoleFromUser(User $user, Role $role): void
    {
        $this->ensureUserBelongsToCompany($user);
        $this->ensureRoleBelongsToCompany($role);

        DB::transaction(function () use ($user, $role): void {
            setPermissionsTeamId($user->company_id);
            $user->removeRole($role);
        });

        app(PermissionRegistrar::class)->forgetCachedPermissions();
    }

    /**
     * Récupère les utilisateurs de l'entreprise qui n'ont pas encore un rôle spécifique.
     *
     * @throws \Exception
     */
    public function getUsersWithoutRole(Role $role): \Illuminate\Database\Eloquent\Collection
    {
        $this->ensureRoleBelongsToCompany($role);

        return User::where('company_id', $role->company_id)
            ->whereDoesntHave('roles', function ($query) use ($role): void {
                $query->where('roles.id', $role->id);
            })
            ->get();
    }

    /**
     * Récupère les statistiques des rôles pour l'entreprise.
     *
     * @return array{total_roles: int, total_users: int, users_with_roles: int, users_without_roles: int}
     */
    public function getRoleStats(?int $companyId = null): array
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        $totalRoles = Role::where('company_id', $companyId)->count();
        $totalUsers = User::where('company_id', $companyId)->count();
        $usersWithRoles = User::where('company_id', $companyId)
            ->whereHas('roles')
            ->count();

        return [
            'total_roles' => $totalRoles,
            'total_users' => $totalUsers,
            'users_with_roles' => $usersWithRoles,
            'users_without_roles' => $totalUsers - $usersWithRoles,
        ];
    }

    /**
     * Grouper les permissions par catégorie avec des libellés explicites.
     * Seules les permissions connues du seeder sont exposées dans l'UI.
     *
     * @return \Illuminate\Support\Collection<string, \Illuminate\Support\Collection>
     */
    public function getPermissionsByGroup(): \Illuminate\Support\Collection
    {
        $groups = [
            'Tableau de bord' => ['view_dashboard_stats'],
            'Produits' => ['view_products', 'create_products', 'edit_products', 'delete_products', 'manage_products'],
            'Stock' => ['view_stock', 'create_stock_entries', 'adjust_stock', 'transfer_stock'],
            'Ventes' => ['view_documents', 'create_documents', 'edit_documents', 'delete_documents', 'validate_documents', 'record_payments', 'view_all_sales_documents'],
            'Achats' => ['view_purchases', 'create_purchases', 'edit_purchases'],
            'Clients & Fournisseurs' => ['manage_customers', 'manage_suppliers'],
            'Dépenses' => ['view_expenses', 'create_expenses', 'edit_expenses', 'delete_expenses'],
            'Caisse' => ['view_cash_sessions', 'manage_cash_sessions', 'close_cash_sessions'],
            'Livraisons' => ['view_deliveries', 'create_deliveries', 'edit_deliveries', 'close_deliveries'],
            'Achats de stock' => ['view_stock_purchases', 'create_stock_purchases', 'edit_stock_purchases', 'close_stock_purchases'],
            'Salaires & RH' => ['view_salaries', 'manage_salaries', 'validate_salaries', 'approve_salary_advances'],
            'Employés' => ['view_employees', 'manage_employees'],
            'Finance & Rapports' => ['view_financial_reports', 'view_global_reports', 'view_store_reports'],
            'Administration' => ['manage_users', 'manage_stores', 'manage_settings', 'manage_subscriptions', 'view_all_sales_documents'],
            'Audit' => ['view_audit_log'],
        ];

        $allPermissions = Permission::all()->keyBy('name');
        $result = collect();

        foreach ($groups as $groupLabel => $permissionNames) {
            $groupPerms = collect();
            foreach ($permissionNames as $name) {
                if ($allPermissions->has($name)) {
                    $groupPerms->push($allPermissions->get($name));
                }
            }
            if ($groupPerms->isNotEmpty()) {
                $result->put($groupLabel, $groupPerms);
            }
        }

        return $result;
    }

    /**
     * Vérifie si un nom de rôle est unique dans l'entreprise.
     */
    public function isRoleNameUnique(string $name, ?int $excludeRoleId = null, ?int $companyId = null): bool
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        $query = Role::where('company_id', $companyId)->where('name', $name);

        if ($excludeRoleId) {
            $query->where('id', '!=', $excludeRoleId);
        }

        return $query->doesntExist();
    }

    /**
     * Récupère un rôle de l'entreprise par son ID.
     */
    public function getCompanyRole(int $roleId, ?int $companyId = null): ?Role
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        return Role::where('company_id', $companyId)->where('id', $roleId)->first();
    }

    /**
     * S'assure qu'un rôle appartient à l'entreprise actuelle.
     *
     * @throws \Exception
     */
    private function ensureRoleBelongsToCompany(Role $role): void
    {
        if ($role->company_id !== Auth::user()->company_id) {
            throw new \Exception('Accès non autorisé à ce rôle.');
        }
    }

    /**
     * S'assure qu'un utilisateur appartient à l'entreprise actuelle.
     *
     * @throws \Exception
     */
    private function ensureUserBelongsToCompany(User $user): void
    {
        if ($user->company_id !== Auth::user()->company_id) {
            throw new \Exception('Accès non autorisé à cet utilisateur.');
        }
    }

    /**
     * S'assure qu'un rôle n'est pas dans la liste des rôles protégés.
     *
     * @throws \Exception
     */
    private function ensureRoleIsNotProtected(Role $role): void
    {
        if (in_array($role->name, self::PROTECTED_ROLES, strict: true)) {
            throw new \Exception('Le rôle "'.$role->name.'" est protégé et ne peut pas être modifié ni supprimé.');
        }
    }
}
