<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionCacheService
{
    private const CACHE_TTL = 3600; // 1 heure

    private const CACHE_PREFIX = 'permissions';

    /**
     * Get cached user permissions
     */
    public function getUserPermissions(int $userId): array
    {
        return Cache::remember(
            $this->getUserPermissionsCacheKey($userId),
            self::CACHE_TTL,
            function () use ($userId) {
                $user = \App\Models\User::find($userId);

                return $user ? $user->getAllPermissions()->pluck('name')->toArray() : [];
            }
        );
    }

    /**
     * Get cached user roles
     */
    public function getUserRoles(int $userId): array
    {
        return Cache::remember(
            $this->getUserRolesCacheKey($userId),
            self::CACHE_TTL,
            function () use ($userId) {
                $user = \App\Models\User::find($userId);

                return $user ? $user->getRoleNames()->toArray() : [];
            }
        );
    }

    /**
     * Check if user has specific permission (cached)
     */
    public function userHasPermission(int $userId, string $permission): bool
    {
        $permissions = $this->getUserPermissions($userId);

        return in_array($permission, $permissions);
    }

    /**
     * Check if user has specific role (cached)
     */
    public function userHasRole(int $userId, $roles): bool
    {
        $userRoles = $this->getUserRoles($userId);

        if (is_string($roles)) {
            return in_array($roles, $userRoles);
        }

        if (is_array($roles)) {
            return ! empty(array_intersect($roles, $userRoles));
        }

        return false;
    }

    /**
     * Get cached company permissions structure
     */
    public function getCompanyPermissions(int $companyId): array
    {
        return Cache::remember(
            $this->getCompanyPermissionsCacheKey($companyId),
            self::CACHE_TTL,
            function () use ($companyId) {
                // Récupérer toutes les permissions de l'entreprise via les utilisateurs
                $users = \App\Models\User::where('company_id', $companyId)
                    ->with(['roles.permissions', 'permissions'])
                    ->get();

                $companyPermissions = [];
                foreach ($users as $user) {
                    $companyPermissions[$user->id] = [
                        'roles' => $user->getRoleNames()->toArray(),
                        'permissions' => $user->getAllPermissions()->pluck('name')->toArray(),
                    ];
                }

                return $companyPermissions;
            }
        );
    }

    /**
     * Get all available permissions (cached)
     */
    public function getAllPermissions(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX.'.all_permissions',
            self::CACHE_TTL * 24, // Cache plus long car les permissions changent rarement
            function () {
                return Permission::all()->pluck('name', 'id')->toArray();
            }
        );
    }

    /**
     * Get all available roles (cached)
     */
    public function getAllRoles(): array
    {
        return Cache::remember(
            self::CACHE_PREFIX.'.all_roles',
            self::CACHE_TTL * 24, // Cache plus long car les rôles changent rarement
            function () {
                return Role::all()->pluck('name', 'id')->toArray();
            }
        );
    }

    /**
     * Invalidate user permissions cache
     */
    public function invalidateUserCache(int $userId): void
    {
        Cache::forget($this->getUserPermissionsCacheKey($userId));
        Cache::forget($this->getUserRolesCacheKey($userId));
    }

    /**
     * Invalidate company permissions cache
     */
    public function invalidateCompanyCache(int $companyId): void
    {
        Cache::forget($this->getCompanyPermissionsCacheKey($companyId));
    }

    /**
     * Invalidate global permissions cache
     */
    public function invalidateGlobalCache(): void
    {
        Cache::forget(self::CACHE_PREFIX.'.all_permissions');
        Cache::forget(self::CACHE_PREFIX.'.all_roles');
    }

    /**
     * Warm up user permissions cache
     */
    public function warmupUserCache(int $userId): void
    {
        $this->getUserPermissions($userId);
        $this->getUserRoles($userId);
    }

    /**
     * Warm up company permissions cache
     */
    public function warmupCompanyCache(int $companyId): void
    {
        $this->getCompanyPermissions($companyId);
    }

    private function getUserPermissionsCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX.".user_{$userId}_permissions";
    }

    private function getUserRolesCacheKey(int $userId): string
    {
        return self::CACHE_PREFIX.".user_{$userId}_roles";
    }

    private function getCompanyPermissionsCacheKey(int $companyId): string
    {
        return self::CACHE_PREFIX.".company_{$companyId}_permissions";
    }
}
