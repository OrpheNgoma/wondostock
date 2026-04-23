<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ZoneProductPrice extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'zone_id',
        'product_id',
        'selling_price',
        'margin_override',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'selling_price' => 'integer',
            'margin_override' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Marge effective : override manuel si défini, sinon selling_price − purchase_price.
     */
    public function getEffectiveMarginAttribute(): int
    {
        if ($this->margin_override !== null) {
            return $this->margin_override;
        }

        return max(0, $this->selling_price - ($this->product->purchase_price ?? 0));
    }
}
