<?php

namespace App\Enums;

enum ExpenseType: string
{
    case Fixed = 'fixed';
    case Variable = 'variable';

    public function label(): string
    {
        return match ($this) {
            self::Fixed => 'Fixe',
            self::Variable => 'Variable',
        };
    }
}
