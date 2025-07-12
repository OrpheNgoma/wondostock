<?php

namespace App\Models;

use App\Models\User;
use App\Models\Store;
use App\Models\Product;
use App\Models\Customer;
use App\Models\Document;
use App\Models\Subscription;
use Spatie\MediaLibrary\HasMedia;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\InteractsWithMedia;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Company extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CompanyFactory> */
    use HasFactory, SoftDeletes, InteractsWithMedia;

    protected $fillable = [
        'name', 'legal_name', 'address', 'phone_number', 'email', 'rccm', 'nif', 'owner_id', 'is_active'
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

    public function hasFeature(string $featureSlug): bool
    {
        if (!$this->subscription || !$this->subscription->plan) {
            return false;
        }
        return in_array($featureSlug, $this->subscription->plan->features);
    }
}
