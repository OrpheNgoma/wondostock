<?php

namespace App\Console\Commands;

use App\Services\PermissionCacheService;
use Illuminate\Console\Command;

class CachePermissionsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permissions:cache 
                           {action : Action to perform (warmup|clear|refresh)}
                           {--user= : Specific user ID to target}
                           {--company= : Specific company ID to target}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Manage permissions cache (warmup, clear, or refresh)';

    private PermissionCacheService $permissionCacheService;

    public function __construct(PermissionCacheService $permissionCacheService)
    {
        parent::__construct();
        $this->permissionCacheService = $permissionCacheService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        $userId = $this->option('user');
        $companyId = $this->option('company');

        switch ($action) {
            case 'warmup':
                $this->warmupCache($userId, $companyId);
                break;

            case 'clear':
                $this->clearCache($userId, $companyId);
                break;

            case 'refresh':
                $this->refreshCache($userId, $companyId);
                break;

            default:
                $this->error("Unknown action: {$action}. Use warmup, clear, or refresh.");

                return 1;
        }

        return 0;
    }

    private function warmupCache(?int $userId, ?int $companyId): void
    {
        if ($userId) {
            $this->info("Warming up cache for user {$userId}...");
            $this->permissionCacheService->warmupUserCache($userId);
            $this->info('✓ User cache warmed up');

            return;
        }

        if ($companyId) {
            $this->info("Warming up cache for company {$companyId}...");
            $this->permissionCacheService->warmupCompanyCache($companyId);
            $this->info('✓ Company cache warmed up');

            return;
        }

        $this->info('Warming up global permissions cache...');
        $this->permissionCacheService->getAllPermissions();
        $this->permissionCacheService->getAllRoles();

        // Warmup pour tous les utilisateurs actifs
        \App\Models\User::whereNotNull('company_id')
            ->chunk(100, function ($users) {
                foreach ($users as $user) {
                    $this->permissionCacheService->warmupUserCache($user->id);
                }
                $this->info('Warmed up cache for '.$users->count().' users');
            });

        $this->info('✓ Global cache warmed up');
    }

    private function clearCache(?int $userId, ?int $companyId): void
    {
        if ($userId) {
            $this->info("Clearing cache for user {$userId}...");
            $this->permissionCacheService->invalidateUserCache($userId);
            $this->info('✓ User cache cleared');

            return;
        }

        if ($companyId) {
            $this->info("Clearing cache for company {$companyId}...");
            $this->permissionCacheService->invalidateCompanyCache($companyId);
            $this->info('✓ Company cache cleared');

            return;
        }

        $this->info('Clearing all permissions cache...');
        $this->permissionCacheService->invalidateGlobalCache();

        // Clear pour tous les utilisateurs
        \App\Models\User::chunk(100, function ($users) {
            foreach ($users as $user) {
                $this->permissionCacheService->invalidateUserCache($user->id);
            }
            $this->info('Cleared cache for '.$users->count().' users');
        });

        $this->info('✓ All permissions cache cleared');
    }

    private function refreshCache(?int $userId, ?int $companyId): void
    {
        $this->clearCache($userId, $companyId);
        $this->warmupCache($userId, $companyId);
        $this->info('✓ Cache refreshed');
    }
}
