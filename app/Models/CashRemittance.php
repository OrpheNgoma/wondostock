<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CashRemittance extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'store_id',
        'user_id',
        'received_by',
        'amount',
        'remittance_date',
        'reference',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
            'remittance_date' => 'date',
        ];
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
