<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

trait SecureCompanyAccess
{
    /**
     * Vérifie et retourne l'utilisateur et son entreprise de manière sécurisée
     * 
     * @return array{user: \App\Models\User|null, company: \App\Models\Company|null, valid: bool}
     */
    protected function getSecureUserAndCompany(): array
    {
        try {
            $user = Auth::user();
            
            if (!$user) {
                return ['user' => null, 'company' => null, 'valid' => false];
            }
            
            $company = $user->company;
            
            if (!$company) {
                Log::warning('User without company access attempt', ['user_id' => $user->id]);
                return ['user' => $user, 'company' => null, 'valid' => false];
            }
            
            return ['user' => $user, 'company' => $company, 'valid' => true];
        } catch (\Exception $e) {
            Log::error('Error in getSecureUserAndCompany: ' . $e->getMessage());
            return ['user' => null, 'company' => null, 'valid' => false];
        }
    }
    
    /**
     * Vérifie si l'utilisateur a accès à une ressource d'une entreprise donnée
     * 
     * @param int $resourceCompanyId
     * @return bool
     */
    protected function hasCompanyAccess(int $resourceCompanyId): bool
    {
        $userCompany = $this->getSecureUserAndCompany();
        
        if (!$userCompany['valid']) {
            return false;
        }
        
        return $userCompany['company']->id === $resourceCompanyId;
    }
    
    /**
     * Retourne un scope de base pour les requêtes limitées à l'entreprise de l'utilisateur
     * 
     * @param string $model
     * @return \Illuminate\Database\Eloquent\Builder|null
     */
    protected function getCompanyScope(string $model)
    {
        $userCompany = $this->getSecureUserAndCompany();
        
        if (!$userCompany['valid']) {
            return null;
        }
        
        return $model::where('company_id', $userCompany['company']->id);
    }
    
    /**
     * Dispatch une notification d'erreur sécurisée
     * 
     * @param string $message
     * @param string $type
     */
    protected function dispatchSecureError(string $message = 'Une erreur s\'est produite.', string $type = 'error'): void
    {
        $this->dispatch('notify', message: $message, type: $type);
    }
}