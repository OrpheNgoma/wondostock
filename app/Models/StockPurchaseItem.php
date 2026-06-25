<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPurchaseItem extends Model
{
    /** @use HasFactory<\Database\Factories\StockPurchaseItemFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'product_id',
        'product_ref',
        'product_designation',
        'qty_purchased',
        'unit_cost',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_purchased' => 'integer',
            'unit_cost' => 'integer',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(StockPurchaseTrip::class, 'trip_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Coût total de la ligne (quantité achetée × coût unitaire). */
    public function getLineTotalAttribute(): int
    {
        return $this->qty_purchased * $this->unit_cost;
    }
}
