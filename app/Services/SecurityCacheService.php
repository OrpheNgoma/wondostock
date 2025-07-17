<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;

class SecurityCacheService
{
    /**
     * Invalidate user security cache
     */
    public function invalidateUserCache(int $userId): void
    {
        Cache::forget("user_{$userId}_is_global_admin");
    }

    /**
     * Invalidate company accessibility cache
     */
    public function invalidateCompanyCache(int $companyId): void
    {
        Cache::forget("company_{$companyId}_is_accessible");
    }

    /**
     * Invalidate user permissions cache
     */
    public function invalidateUserPermissions(int $userId): void
    {
        Cache::forget("user_{$userId}_permissions");
        Cache::forget("user_{$userId}_roles");
        
        // Utiliser aussi le nouveau service de permissions
        app(PermissionCacheService::class)->invalidateUserCache($userId);
    }

    /**
     * Invalidate all security cache for a user
     */
    public function invalidateAllUserCache(int $userId): void
    {
        $this->invalidateUserCache($userId);
        $this->invalidateUserPermissions($userId);
    }

    /**
     * Refresh user global admin status
     */
    public function refreshUserGlobalAdminStatus(int $userId): bool
    {
        $this->invalidateUserCache($userId);
        
        $user = \App\Models\User::find($userId);
        if (!$user) {
            return false;
        }

        return Cache::remember("user_{$userId}_is_global_admin", 300, function () use ($user) {
            return $user->hasRole(['Global-Admin', 'Super-Administrateur']);
        });
    }

    /**
     * Refresh company accessibility status
     */
    public function refreshCompanyAccessibility(int $companyId): bool
    {
        $this->invalidateCompanyCache($companyId);
        
        return Cache::remember("company_{$companyId}_is_accessible", 900, function () use ($companyId) {
            $company = \App\Models\Company::find($companyId);
            
            return $company && 
                   $company->is_active && 
                   !$company->is_suspended &&
                   ($company->subscription_expires_at === null || $company->subscription_expires_at->isFuture());
        });
    }
}