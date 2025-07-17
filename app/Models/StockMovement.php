<?php

namespace App\Models;

use App\Enums\StockMovementType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    use HasFactory;

    public $timestamps = ['created_at'];

    const UPDATED_AT = null;

    protected $fillable = ['company_id', 'product_id', 'store_id', 'user_id', 'source_id', 'source_type', 'type', 'quantity'];

    protected $casts = ['type' => StockMovementType::class];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function source()
    {
        return $this->morphTo();
    }
}
