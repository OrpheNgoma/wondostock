<?php

namespace App\Models;

use App\Models\Product;
use App\Models\Document;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DocumentItem extends Model
{
    use HasFactory;
    
    protected $fillable = ['document_id', 'product_id', 'description', 'quantity', 'unit_price', 'tax_rate', 'total_amount'];

    protected $casts = ['quantity' => 'decimal:3', 'unit_price' => 'decimal:3', 'tax_rate' => 'decimal:3', 'total_amount' => 'decimal:3'];

    public function document(): BelongsTo
    {
        return $this->belongsTo(Document::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
