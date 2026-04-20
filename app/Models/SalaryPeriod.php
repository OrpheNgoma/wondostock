<?php

namespace App\Models;

use App\Enums\SalaryPeriodStatus;
use App\Traits\Auditable;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalaryPeriod extends Model
{
    use Auditable, BelongsToCompany;

    protected $fillable = [
        'company_id',
        'label',
        'month',
        'year',
        'status',
        'total_gross',
        'total_deductions',
        'total_net',
        'total_advances',
        'validated_at',
        'validated_by',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SalaryPeriodStatus::class,
            'total_gross' => 'integer',
            'total_deductions' => 'integer',
            'total_net' => 'integer',
            'total_advances' => 'integer',
            'month' => 'integer',
            'year' => 'integer',
            'validated_at' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    public function slips(): HasMany
    {
        return $this->hasMany(SalarySlip::class, 'period_id');
    }

    public function validatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function isDraft(): bool
    {
        return $this->status === SalaryPeriodStatus::Draft;
    }

    public function isValidated(): bool
    {
        return $this->status === SalaryPeriodStatus::Validated;
    }

    public function isPaid(): bool
    {
        return $this->status === SalaryPeriodStatus::Paid;
    }
}
