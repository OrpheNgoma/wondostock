<?php

namespace App\Models;

use App\Models\Product;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Unit extends Model
{
    use HasFactory, BelongsToCompany;
    
    protected $fillable = ['company_id', 'name', 'symbol'];

    /**
     * Relation: Une unité peut avoir plusieurs produits
     */
    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
