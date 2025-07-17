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
            self::Simple => 'Produit simple',
            self::Variable => 'Produit variable',
            self::Variant => 'Variante de produit',
        };
    }
}
