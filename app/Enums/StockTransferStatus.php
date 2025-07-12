<?php

namespace App\Enums;

enum StockTransferStatus: string
{
    case Pending = 'pending';
    case Completed = 'completed';
    case Cancelled = 'cancelled';
    
    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Completed => 'Terminé',
            self::Cancelled => 'Annulé',
        };
    }
}