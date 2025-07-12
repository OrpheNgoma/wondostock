<?php

namespace App\Traits;

use App\Models\Company;
use App\Scopes\CompanyScope;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

trait BelongsToCompany
{
    /**
     * Boot the trait.
     */
    protected static function bootBelongsToCompany(): void
    {
        // Appliquer automatiquement le scope de company
        static::addGlobalScope(new CompanyScope);

        // Assigner automatiquement la company_id lors de la création
        static::creating(function ($model) {
            if (Auth::check() && Auth::user()->company_id && !$model->company_id) {
                $model->company_id = Auth::user()->company_id;
            }
        });
    }

    /**
     * Relation: appartient à une company
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Scope pour filtrer par company spécifique
     */
    public function scopeForCompany($query, $companyId)
    {
        return $query->where('company_id', $companyId);
    }

    /**
     * Scope pour désactiver temporairement le filtrage par company
     */
    public function scopeWithoutCompanyScope($query)
    {
        return $query->withoutGlobalScope(CompanyScope::class);
    }
}