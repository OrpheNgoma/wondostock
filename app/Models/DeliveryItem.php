<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DeliveryItem extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryItemFactory> */
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'customer_id',
        'product_id',
        'product_ref',
        'product_designation',
        'qty_delivered',
        'qty_returned',
        'unit_price',
        'margin_per_unit',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'qty_delivered' => 'integer',
            'qty_returned' => 'integer',
            'unit_price' => 'integer',
            'margin_per_unit' => 'integer',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(DeliveryTrip::class, 'trip_id');
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Quantité nette vendue (livrée − retournée). */
    public function getNetQtyAttribute(): int
    {
        return $this->qty_delivered - $this->qty_returned;
    }

    /** Montant total de la ligne (qté nette × prix unitaire). */
    public function getTotalAttribute(): int
    {
        return $this->net_qty * $this->unit_price;
    }

    /** Marge totale de la ligne (qté nette × marge par unité). */
    public function getLineTotalMarginAttribute(): int
    {
        return $this->net_qty * $this->margin_per_unit;
    }
}
