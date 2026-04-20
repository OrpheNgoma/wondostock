<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Audit extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'company_id',
        'user_id',
        'auditable_type',
        'auditable_id',
        'event',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected function casts(): array
    {
        return [
            'old_values' => 'array',
            'new_values' => 'array',
            'created_at' => 'datetime',
        ];
    }

    public function auditable(): MorphTo
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Libellé lisible du type d'entité auditée.
     */
    public function auditableLabel(): string
    {
        $map = [
            Document::class => 'Vente',
            Expense::class => 'Dépense',
            SalaryPeriod::class => 'Période de salaire',
            DeliveryTrip::class => 'Tournée',
            StockMovement::class => 'Mouvement stock',
        ];

        return $map[$this->auditable_type] ?? class_basename($this->auditable_type);
    }

    /**
     * Libellé lisible de l'événement.
     */
    public function eventLabel(): string
    {
        return match ($this->event) {
            'created' => 'Création',
            'updated' => 'Modification',
            'deleted' => 'Suppression',
            default => ucfirst($this->event),
        };
    }
}
