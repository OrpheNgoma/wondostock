<?php

namespace App\Enums;

enum SalaryAdvanceStatus: string
{
    case Pending = 'pending';
    case Approved = 'approved';
    case Paid = 'paid';
    case Deducted = 'deducted';

    public function label(): string
    {
        return match ($this) {
            self::Pending => 'En attente',
            self::Approved => 'Approuvée',
            self::Paid => 'Payée',
            self::Deducted => 'Déduite',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending => 'yellow',
            self::Approved => 'blue',
            self::Paid => 'green',
            self::Deducted => 'purple',
        };
    }
}
