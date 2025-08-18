<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DocumentItem extends Model
{
    use HasFactory;

    protected $fillable = ['document_id', 'product_id', 'description', 'quantity', 'unit_price', 'tax_rate', 'total_amount'];

    protected $casts = ['quantity' => 'integer', 'unit_price' => 'integer', 'tax_rate' => 'decimal:3', 'total_amount' => 'integer'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
