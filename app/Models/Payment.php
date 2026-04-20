<?php

namespace App\Models;

use App\Enums\PaymentMethod;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Payment extends Model
{
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'invoice_id',
        'user_id',
        'amount',
        'status',
        'transaction_id',
        'payment_date',
        'paid_at',
        'processed_at',
        'payment_method',
        'reference',
        'notes',
        'gateway_response',
    ];

    protected $casts = [
        'amount' => 'integer',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'processed_at' => 'datetime',
        'gateway_response' => 'array',
        'payment_method' => PaymentMethod::class,
    ];

    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class, 'invoice_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scopes pour faciliter les requêtes
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Mutateurs et Accesseurs
     */
    public function getFormattedAmountAttribute(): string
    {
        return number_format($this->amount / 100, 0, ',', ' ').' FCFA';
    }

    public function getIsCompletedAttribute(): bool
    {
        return $this->status === 'completed';
    }
}
