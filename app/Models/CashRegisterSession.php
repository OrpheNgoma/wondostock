<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CashRegisterSession extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'store_id',
        'user_id',
        'session_date',
        'opening_balance',
        'cash_in',
        'cash_out',
        'remittances',
        'closing_balance',
        'status',
        'notes',
        'closed_at',
    ];

    protected function casts(): array
    {
        return [
            'opening_balance' => 'integer',
            'cash_in' => 'integer',
            'cash_out' => 'integer',
            'remittances' => 'integer',
            'closing_balance' => 'integer',
            'session_date' => 'date',
            'closed_at' => 'datetime',
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

    public function movements(): HasMany
    {
        return $this->hasMany(CashMovement::class, 'session_id')->orderBy('movement_date')->orderBy('created_at');
    }

    public function computeClosingBalance(): int
    {
        return $this->opening_balance + $this->cash_in - $this->cash_out - $this->remittances;
    }

    public function isOpen(): bool
    {
        return $this->status === 'open';
    }

    public function isClosed(): bool
    {
        return $this->status === 'closed';
    }

    public function scopeOpen($query): void
    {
        $query->where('status', 'open');
    }

    public function scopeClosed($query): void
    {
        $query->where('status', 'closed');
    }
}
