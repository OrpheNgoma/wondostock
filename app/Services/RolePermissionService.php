<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Service pour la gestion des rôles et permissions avec isolation par entreprise
 */
class RolePermissionService
{
    /**
     * Vérifie si l'utilisateur actuel peut gérer les rôles et permissions
     */
    public function canManageRoles(): bool
    {
        $user = Auth::user();

        if (! $user) {
            return false;
        }

        // Seuls les propriétaires d'entreprise peuvent gérer les rôles
        return $user->can('feature-roles-permissions') || $this->isCompanyOwner($user);
    }

    /**
     * Vérifie si l'utilisateur est propriétaire de l'entreprise
     */
    public function isCompanyOwner(User $user): bool
    {
        return $user->company && $user->company->owner_id === $user->id;
    }

    /**
     * Récupère tous les rôles de l'entreprise actuelle
     */
    public function getCompanyRoles(?int $companyId = null): \Illuminate\Database\Eloquent\Collection
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        return Role::where('company_id', $companyId)
            ->orderBy('name')
            ->get();
    }

    /**
     * Récupère toutes les permissions disponibles
     */
    public function getAvailablePermissions(): \Illuminate\Database\Eloquent\Collection
    {
        return Permission::orderBy('name')->get();
    }

    /**
     * Crée un nouveau rôle pour l'entreprise
     */
    public function createRole(string $name, ?string $description = null, ?int $companyId = null): Role
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        return Role::create([
            'name' => $name,
            'description' => $description,
            'guard_name' => 'web',
            'company_id' => $companyId,
        ]);
    }

    /**
     * Met à jour un rôle existant
     */
    public function updateRole(Role $role, string $name, ?string $description = null): Role
    {
        // Vérifier que le rôle appartient à l'entreprise actuelle
        $this->ensureRoleBelongsToCompany($role);

        $role->update([
            'name' => $name,
            'description' => $description,
        ]);

        return $role;
    }

    /**
     * Supprime un rôle
     */
    public function deleteRole(Role $role): bool
    {
        // Vérifier que le rôle appartient à l'entreprise actuelle
        $this->ensureRoleBelongsToCompany($role);

        // Vérifier qu'aucun utilisateur n'a ce rôle
        if ($role->users()->count() > 0) {
            throw new \Exception('Ce rôle ne peut pas être supprimé car il est assigné à des utilisateurs.');
        }

        return $role->delete();
    }

    /**
     * Assigne des permissions à un rôle
     */
    public function syncRolePermissions(Role $role, array $permissionIds): void
    {
        // Vérifier que le rôle appartient à l'entreprise actuelle
        $this->ensureRoleBelongsToCompany($role);

        $permissions = Permission::whereIn('id', $permissionIds)->get();
        $role->syncPermissions($permissions);
    }

    /**
     * Assigne un rôle à un utilisateur
     */
    public function assignRoleToUser(User $user, Role $role): void
    {
        // Vérifier que l'utilisateur et le rôle appartiennent à la même entreprise
        $this->ensureUserBelongsToCompany($user);
        $this->ensureRoleBelongsToCompany($role);

        if ($user->company_id !== $role->company_id) {
            throw new \Exception('L\'utilisateur et le rôle doivent appartenir à la même entreprise.');
        }

        $user->assignRole($role);
    }

    /**
     * Retire un rôle d'un utilisateur
     */
    public function removeRoleFromUser(User $user, Role $role): void
    {
        // Vérifier que l'utilisateur et le rôle appartiennent à la même entreprise
        $this->ensureUserBelongsToCompany($user);
        $this->ensureRoleBelongsToCompany($role);

        $user->removeRole($role);
    }

    /**
     * Récupère les utilisateurs de l'entreprise qui n'ont pas un rôle spécifique
     */
    public function getUsersWithoutRole(Role $role): \Illuminate\Database\Eloquent\Collection
    {
        $this->ensureRoleBelongsToCompany($role);

        return User::where('company_id', $role->company_id)
            ->whereDoesntHave('roles', function ($query) use ($role) {
                $query->where('roles.id', $role->id);
            })
            ->get();
    }

    /**
     * Récupère les statistiques des rôles pour l'entreprise
     */
    public function getRoleStats(?int $companyId = null): array
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        $totalRoles = Role::where('company_id', $companyId)->count();
        $totalUsers = User::where('company_id', $companyId)->count();
        $usersWithRoles = User::where('company_id', $companyId)
            ->whereHas('roles')
            ->count();
        $usersWithoutRoles = $totalUsers - $usersWithRoles;

        return [
            'total_roles' => $totalRoles,
            'total_users' => $totalUsers,
            'users_with_roles' => $usersWithRoles,
            'users_without_roles' => $usersWithoutRoles,
        ];
    }

    /**
     * S'assure qu'un rôle appartient à l'entreprise actuelle
     */
    private function ensureRoleBelongsToCompany(Role $role): void
    {
        if ($role->company_id !== Auth::user()->company_id) {
            throw new \Exception('Accès non autorisé à ce rôle.');
        }
    }

    /**
     * S'assure qu'un utilisateur appartient à l'entreprise actuelle
     */
    private function ensureUserBelongsToCompany(User $user): void
    {
        if ($user->company_id !== Auth::user()->company_id) {
            throw new \Exception('Accès non autorisé à cet utilisateur.');
        }
    }

    /**
     * Grouper les permissions par catégorie
     */
    public function getPermissionsByGroup(): \Illuminate\Support\Collection
    {
        return Permission::all()->groupBy(function ($permission) {
            $parts = explode('_', $permission->name);

            return $parts[1] ?? 'general';
        });
    }

    /**
     * Vérifie si un nom de rôle est unique dans l'entreprise
     */
    public function isRoleNameUnique(string $name, ?int $excludeRoleId = null, ?int $companyId = null): bool
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        $query = Role::where('company_id', $companyId)
            ->where('name', $name);

        if ($excludeRoleId) {
            $query->where('id', '!=', $excludeRoleId);
        }

        return $query->count() === 0;
    }

    /**
     * Récupère un rôle de l'entreprise par son ID
     */
    public function getCompanyRole(int $roleId, ?int $companyId = null): ?Role
    {
        $companyId = $companyId ?? Auth::user()->company_id;

        return Role::where('company_id', $companyId)
            ->where('id', $roleId)
            ->first();
    }
}
