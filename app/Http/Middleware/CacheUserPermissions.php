<?php

namespace App\Http\Middleware;

use App\Services\PermissionCacheService;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CacheUserPermissions
{
    private PermissionCacheService $permissionCacheService;

    public function __construct(PermissionCacheService $permissionCacheService)
    {
        $this->permissionCacheService = $permissionCacheService;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Pre-cache user permissions if user is authenticated
        if (Auth::check()) {
            $user = Auth::user();
            
            // Warmup cache for the current user
            $this->permissionCacheService->warmupUserCache($user->id);
            
            // Optionally, warmup company cache for admin users
            if ($user->hasRole(['Admin', 'Super-Administrateur']) && $user->company_id) {
                $this->permissionCacheService->warmupCompanyCache($user->company_id);
            }
        }

        return $next($request);
    }
}
