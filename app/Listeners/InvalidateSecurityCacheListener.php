<?php

namespace App\Listeners;

use App\Services\SecurityCacheService;
use Illuminate\Contracts\Queue\ShouldQueue;

class InvalidateSecurityCacheListener implements ShouldQueue
{
    private SecurityCacheService $securityCacheService;

    public function __construct(SecurityCacheService $securityCacheService)
    {
        $this->securityCacheService = $securityCacheService;
    }

    public function handle($event): void
    {
        $model = $this->extractModelFromEvent($event);
        
        if (!$model) {
            return;
        }

        $this->handleModelChange($model);
    }

    private function extractModelFromEvent($event)
    {
        if (isset($event->model)) {
            return $event->model;
        }

        if (is_object($event) && property_exists($event, 'model')) {
            return $event->model;
        }

        return null;
    }

    private function handleModelChange($model): void
    {
        $modelClass = get_class($model);

        switch ($modelClass) {
            case 'App\Models\User':
                $this->handleUserChange($model);
                break;
                
            case 'App\Models\Company':
                $this->handleCompanyChange($model);
                break;
                
            case 'Spatie\Permission\Models\Role':
            case 'Spatie\Permission\Models\Permission':
                $this->handleRolePermissionChange($model);
                break;
        }
    }

    private function handleUserChange($user): void
    {
        // Invalider le cache de l'utilisateur
        $this->securityCacheService->invalidateAllUserCache($user->id);
        
        // Si le company_id a changé, invalider aussi l'ancien
        if ($user->isDirty('company_id') && $user->getOriginal('company_id')) {
            $this->securityCacheService->invalidateCompanyCache($user->getOriginal('company_id'));
        }
    }

    private function handleCompanyChange($company): void
    {
        // Invalider le cache d'accessibilité de l'entreprise
        $this->securityCacheService->invalidateCompanyCache($company->id);
    }

    private function handleRolePermissionChange($model): void
    {
        // Pour les changements de rôles/permissions, invalider le cache de tous les utilisateurs
        // En production, on pourrait être plus spécifique
        \App\Models\User::chunk(1000, function ($users) {
            foreach ($users as $user) {
                $this->securityCacheService->invalidateUserPermissions($user->id);
            }
        });
    }
}