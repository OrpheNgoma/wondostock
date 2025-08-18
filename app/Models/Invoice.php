<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle pour les factures des abonnements SaaS.
 */
class Invoice extends Model
{
    /** @use HasFactory<\Database\Factories\InvoiceFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'invoice_number',
        'company_id',
        'subscription_id',
        'amount',
        'tax_amount',
        'total_amount',
        'status',
        'issue_date',
        'due_date',
        'paid_at',
        'billing_address',
        'notes',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'issue_date' => 'date',
        'due_date' => 'date',
        'paid_at' => 'date',
        'billing_address' => 'array',
        'amount' => 'integer',
        'tax_amount' => 'integer',
        'total_amount' => 'integer',
    ];

    /**
     * Relation : Une facture appartient à une entreprise.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation : Une facture est liée à un abonnement.
     */
    public function subscription(): BelongsTo
    {
        return $this->belongsTo(Subscription::class);
    }

    /**
     * Relation : Une facture peut avoir plusieurs paiements.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(BillingPayment::class);
    }

    /**
     * Relation : Une facture peut avoir plusieurs notifications.
     */
    public function paymentNotifications(): HasMany
    {
        return $this->hasMany(PaymentNotification::class);
    }

    /**
     * Vérifie si la facture est en retard.
     */
    public function isOverdue(): bool
    {
        return $this->status !== 'paid' && $this->due_date->isPast();
    }

    /**
     * Vérifie si la facture est payée.
     */
    public function isPaid(): bool
    {
        return $this->status === 'paid';
    }

    /**
     * Génère un numéro de facture unique.
     */
    public static function generateInvoiceNumber(): string
    {
        $year = now()->year;
        $lastInvoice = self::whereYear('created_at', $year)
            ->orderBy('id', 'desc')
            ->first();

        $nextNumber = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return 'INV-'.$year.'-'.str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
    }
}
