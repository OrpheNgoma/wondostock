<?php

namespace App\Enums;

enum ProductType: string
{
    case Simple = 'simple';
    case Variable = 'variable';
    case Variant = 'variant';
    
    public function label(): string
    {
        return match ($this) {
            self::Simple => 'simple',
            self::Variable => 'variable',
            self::Variant => 'variante',
        };
    }
}