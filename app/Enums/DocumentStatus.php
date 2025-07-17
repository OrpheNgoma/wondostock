<?php

namespace App\Enums;

enum DocumentStatus: string
{
    case Draft = 'draft';
    case Ordered = 'ordered'; // Nouveau statut pour les BC
    case Completed = 'completed'; // Nouveau statut pour les BC
    case Validated = 'validated';
    case Sent = 'sent';
    case Paid = 'paid';
    case PartiallyPaid = 'partially_paid';
    case Overdue = 'overdue';
    case Cancelled = 'cancelled';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Brouillon',
            self::Ordered => 'Commandé',
            self::Completed => 'Terminé / Reçu',
            self::Validated => 'Validé',
            self::Sent => 'Envoyé',
            self::Paid => 'Payé',
            self::PartiallyPaid => 'Payé partiellement',
            self::Overdue => 'En retard',
            self::Cancelled => 'Annulé',
        };
    }
}
