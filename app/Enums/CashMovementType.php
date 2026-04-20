<?php

namespace App\Enums;

enum CashMovementType: string
{
    case CashIn = 'cash_in';
    case CashOut = 'cash_out';
    case Remittance = 'remittance';

    public function label(): string
    {
        return match ($this) {
            self::CashIn => 'Encaissement',
            self::CashOut => 'Sortie espèces',
            self::Remittance => 'Versement DG',
        };
    }

    public function isDebit(): bool
    {
        return match ($this) {
            self::CashIn => false,
            self::CashOut => true,
            self::Remittance => true,
        };
    }
}
