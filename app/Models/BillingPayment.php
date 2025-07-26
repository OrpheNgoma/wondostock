<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pour les paiements des factures SaaS.
 */
class BillingPayment extends Model
{
    /** @use HasFactory<\Database\Factories\BillingPaymentFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_id',
        'amount',
        'payment_method',
        'transaction_id',
        'status',
        'payment_date',
        'payment_details',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'payment_date' => 'date',
        'payment_details' => 'array',
        'amount' => 'decimal:2',
    ];

    /**
     * Relation : Un paiement appartient à une facture.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Vérifie si le paiement est complété.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Vérifie si le paiement a échoué.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Vérifie si le paiement est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
