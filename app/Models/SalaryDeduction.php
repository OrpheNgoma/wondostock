<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryDeduction extends Model
{
    protected $fillable = [
        'slip_id',
        'label',
        'type',
        'amount',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'integer',
        ];
    }

    public function slip(): BelongsTo
    {
        return $this->belongsTo(SalarySlip::class, 'slip_id');
    }
}
