<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SalarySlip extends Model
{
    use BelongsToCompany;

    protected $fillable = [
        'company_id',
        'period_id',
        'driver_id',
        'employee_id',
        'user_id',
        'employee_name',
        'base_salary',
        'gross_salary',
        'total_deductions',
        'total_advances',
        'net_salary',
        'trips_count',
        'total_commissions',
        'mission_allowances',
        'status',
        'paid_at',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'base_salary' => 'integer',
            'gross_salary' => 'integer',
            'total_deductions' => 'integer',
            'total_advances' => 'integer',
            'net_salary' => 'integer',
            'trips_count' => 'integer',
            'total_commissions' => 'integer',
            'mission_allowances' => 'integer',
            'paid_at' => 'datetime',
        ];
    }

    public function period(): BelongsTo
    {
        return $this->belongsTo(SalaryPeriod::class, 'period_id');
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

    public function deductions(): HasMany
    {
        return $this->hasMany(SalaryDeduction::class, 'slip_id');
    }

    public function getNetAfterAdvancesAttribute(): int
    {
        return $this->net_salary;
    }

    /**
     * Retourne le nom de la personne liée (driver, employee ou user).
     */
    public function getPersonNameAttribute(): string
    {
        return $this->employee_name;
    }

    /**
     * Indique si le bulletin est pour un employé non-chauffeur.
     */
    public function isForEmployee(): bool
    {
        return $this->employee_id !== null;
    }
}
