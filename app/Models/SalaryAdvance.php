<?php

namespace App\Models;

use App\Enums\SalaryAdvanceStatus;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SalaryAdvance extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'driver_id',
        'employee_id',
        'user_id',
        'requested_by',
        'amount',
        'advance_date',
        'reason',
        'status',
        'deducted_on_slip_id',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => SalaryAdvanceStatus::class,
            'amount' => 'integer',
            'advance_date' => 'date',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function requestedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'requested_by');
    }

    public function deductedOnSlip(): BelongsTo
    {
        return $this->belongsTo(SalarySlip::class, 'deducted_on_slip_id');
    }

    /**
     * Retourne le nom de la personne concernée par l'avance.
     */
    public function getPersonNameAttribute(): string
    {
        return $this->driver?->name ?? $this->employee?->name ?? '—';
    }
}
