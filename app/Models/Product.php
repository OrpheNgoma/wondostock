<?php

namespace App\Models;

use App\Enums\ProductType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia; // spatie/laravel-medialibrary

class Product extends Model implements HasMedia
{
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $fillable = [
        'company_id', 'parent_id', 'category_id', 'tax_id', 'type', 'name', 'sku',
        'description', 'attributes', 'purchase_price', 'selling_price', 'unit_id', 'is_active',
    ];

    protected $casts = [
        'type' => ProductType::class,
        'attributes' => 'array',
        'purchase_price' => 'integer',
        'selling_price' => 'integer',
        'is_active' => 'boolean',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tax(): BelongsTo
    {
        return $this->belongsTo(Tax::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'parent_id');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(Product::class, 'parent_id');
    }

    public function stores(): BelongsToMany
    {
        return $this->belongsToMany(Store::class, 'product_store')->withPivot('quantity', 'low_stock_threshold')->withTimestamps();
    }

    public function documentItems(): HasMany
    {
        return $this->hasMany(DocumentItem::class);
    }

    // Accesseur pour obtenir la première image ou une image par défaut
    public function getImageUrlAttribute(): string
    {
        $firstImage = $this->getFirstMediaUrl('images');

        return $firstImage ?: asset('images/product-placeholder.png');
    }

    // Accesseur pour obtenir toutes les images du produit
    public function getImagesAttribute()
    {
        return $this->getMedia('images');
    }
}
