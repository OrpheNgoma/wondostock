<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Company extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'name', 'legal_name', 'address', 'phone_number', 'email', 'rccm', 'nif', 'owner_id', 'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function stores(): HasMany
    {
        return $this->hasMany(Store::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class)->where('status', 'active')->latest('starts_at');
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function paymentNotifications(): HasMany
    {
        return $this->hasMany(PaymentNotification::class);
    }

    public function featureLocks(): HasMany
    {
        return $this->hasMany(FeatureLock::class);
    }

    public function hasFeature(string $featureSlug): bool
    {
        if (! $this->subscription || ! $this->subscription->plan) {
            return false;
        }

        $features = $this->subscription->plan->features;

        // S'assurer que features est un tableau
        if (! is_array($features)) {
            // Si c'est une chaîne JSON, la décoder
            if (is_string($features)) {
                $features = json_decode($features, true);
            }

            // Si ce n'est toujours pas un tableau, retourner false
            if (! is_array($features)) {
                return false;
            }
        }

        return in_array($featureSlug, $features);
    }

    // Nouvelles méthodes pour l'administration globale
    public function getTotalUsersAttribute(): int
    {
        return $this->users()->count();
    }

    public function getTotalProductsAttribute(): int
    {
        return $this->products()->count();
    }

    public function getTotalDocumentsAttribute(): int
    {
        return $this->documents()->count();
    }

    public function getLastLoginAttribute(): ?string
    {
        // Pour l'instant, utilisons created_at pour éviter les erreurs
        // TODO: Implémenter un système de tracking des connexions
        $lastUser = $this->users()
            ->orderBy('created_at', 'desc')
            ->first();

        if (! $lastUser) {
            return 'Aucune activité';
        }

        return 'Inscrit '.$lastUser->created_at->diffForHumans();
    }

    public function getUsageStatsAttribute(): array
    {
        return [
            'users' => $this->users()->count(),
            'products' => $this->products()->count(),
            'documents' => $this->documents()->count(),
            'stores' => $this->stores()->count(),
            'customers' => $this->customers()->count(),
        ];
    }

    public function getRevenueAttribute(): float
    {
        if (! $this->subscription || ! $this->subscription->plan) {
            return 0.0;
        }

        return (float) $this->subscription->plan->price;
    }

    public function getStatusBadgeAttribute(): string
    {
        if ($this->trashed()) {
            return '<span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-red-600/20">
                        <div class="h-1.5 w-1.5 rounded-full bg-red-500"></div>
                        Supprimée
                    </span>';
        }

        if ($this->is_active) {
            return '<span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-green-600/20">
                        <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                        Active
                    </span>';
        }

        return '<span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-2 py-1 text-xs font-medium text-yellow-800 ring-1 ring-yellow-600/20">
                    <div class="h-1.5 w-1.5 rounded-full bg-yellow-500"></div>
                    Inactive
                </span>';
    }

    public function getPlanBadgeAttribute(): string
    {
        if (! $this->subscription || ! $this->subscription->plan) {
            return '<span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 ring-1 ring-gray-600/20">
                        Aucun plan
                    </span>';
        }

        $plan = $this->subscription->plan;
        $config = match ($plan->slug) {
            'essentiel' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'ring' => 'ring-blue-600/20'],
            'pro' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'ring' => 'ring-purple-600/20'],
            'entreprise' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'ring' => 'ring-amber-600/20'],
            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20'],
        };

        return "<span class=\"inline-flex items-center rounded-full {$config['bg']} px-2 py-1 text-xs font-medium {$config['text']} ring-1 {$config['ring']}\">
                    ".e($plan->name).'
                </span>';
    }
}
