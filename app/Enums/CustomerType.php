<?php

namespace App\Enums;

enum CustomerType: string
{
    case Individual = 'individual';
    case Professional = 'professional';
    case Particulier = 'Particulier';

    // public function label(): string
    // {
    //     return match ($this) {
    //         self::Individual => 'Particulier',
    //         self::Professional => 'Professionnel',
    //     };
    // }
}
