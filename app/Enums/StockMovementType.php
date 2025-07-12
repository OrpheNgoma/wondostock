<?php

namespace App\Enums;

enum StockMovementType: string {
    case Sale = 'sale';
    case Purchase = 'purchase';
    case TransferIn = 'transfer_in';
    case TransferOut = 'transfer_out';
    case Adjustment = 'adjustment'; // Correction ici

    public function label(): string
    {
        return match ($this) {
            self::Sale => 'Vente',
            self::Purchase => 'Achat',
            self::TransferIn => 'Entrée par transfert',
            self::TransferOut => 'Sortie par transfert',
            self::Adjustment => "Ajustement d'inventaire",
        };
    }
}