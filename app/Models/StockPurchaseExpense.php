<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockPurchaseExpense extends Model
{
    use HasFactory;

    protected $fillable = [
        'trip_id',
        'category_id',
        'label',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
        ];
    }

    public function trip(): BelongsTo
    {
        return $this->belongsTo(StockPurchaseTrip::class, 'trip_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(DeliveryExpenseCategory::class, 'category_id');
    }
}
