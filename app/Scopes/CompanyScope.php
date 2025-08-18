<?php

namespace App\Scopes;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class CompanyScope implements Scope
{
    /**
     * Apply the scope to a given Eloquent query builder.
     */
    public function apply(Builder $builder, Model $model): void
    {
        // Vérifications de sécurité renforcées
        if (! Auth::check()) {
            // Pas d'utilisateur connecté = pas d'accès aux données
            $builder->whereRaw('1 = 0');

            return;
        }

        $user = Auth::user();

        // Utilisateur global admin peut contourner le scope
        if ($this->isGlobalAdmin($user)) {
            return;
        }

        // Vérifications strictes pour les utilisateurs normaux
        if (! $user->company_id) {
            // Utilisateur sans company_id = pas d'accès
            $builder->whereRaw('1 = 0');

            return;
        }

        // Vérifier que l'entreprise est active et accessible
        if (! $this->isCompanyActiveAndAccessible($user->company_id)) {
            $builder->whereRaw('1 = 0');

            return;
        }

        // Application du filtre company_id standard
        $builder->where($model->getTable().'.company_id', $user->company_id);
    }

    /**
     * Vérifie si l'utilisateur est un administrateur global
     */
    private function isGlobalAdmin($user): bool
    {
        // Cache le statut global admin pour éviter les requêtes répétitives
        return Cache::remember("user_{$user->id}_is_global_admin", 300, function () use ($user) {
            return $user->hasRole(['Global-Admin', 'Super-Administrateur']);
        });
    }

    /**
     * Vérifie que l'entreprise est active et accessible
     */
    private function isCompanyActiveAndAccessible(int $companyId): bool
    {
        return Cache::remember("company_{$companyId}_is_accessible", 900, function () use ($companyId) {
            $company = \App\Models\Company::find($companyId);

            return $company &&
                   $company->is_active &&
                   ! $company->is_suspended &&
                   ($company->subscription_expires_at === null || $company->subscription_expires_at->isFuture());
        });
    }
}
