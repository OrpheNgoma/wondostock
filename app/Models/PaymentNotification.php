<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pour les notifications de paiement.
 */
class PaymentNotification extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'invoice_id',
        'type',
        'title',
        'message',
        'status',
        'sent_at',
        'scheduled_for',
        'notification_data',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'sent_at' => 'datetime',
        'scheduled_for' => 'datetime',
        'notification_data' => 'array',
    ];

    /**
     * Types de notifications disponibles.
     */
    const TYPE_REMINDER = 'reminder';

    const TYPE_OVERDUE = 'overdue';

    const TYPE_PAYMENT_RECEIVED = 'payment_received';

    const TYPE_SUSPENSION_WARNING = 'suspension_warning';

    /**
     * Statuts des notifications.
     */
    const STATUS_PENDING = 'pending';

    const STATUS_SENT = 'sent';

    const STATUS_FAILED = 'failed';

    const STATUS_CANCELLED = 'cancelled';

    /**
     * Relation : Une notification appartient à une entreprise.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation : Une notification peut être liée à une facture.
     */
    public function invoice(): BelongsTo
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Vérifie si la notification est en attente.
     */
    public function isPending(): bool
    {
        return $this->status === self::STATUS_PENDING;
    }

    /**
     * Vérifie si la notification a été envoyée.
     */
    public function isSent(): bool
    {
        return $this->status === self::STATUS_SENT;
    }

    /**
     * Marque la notification comme envoyée.
     */
    public function markAsSent(): void
    {
        $this->update([
            'status' => self::STATUS_SENT,
            'sent_at' => now(),
        ]);
    }

    /**
     * Marque la notification comme échouée.
     */
    public function markAsFailed(): void
    {
        $this->update([
            'status' => self::STATUS_FAILED,
        ]);
    }
}
