<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    case Card = 'card';
    case BankTransfer = 'bank_transfer';
    case MobileMoney = 'mobile_money';
    case Cheque = 'check';
    case Other = 'other';
    // Anciens types pour compatibilité
    case AirtelMoney = 'airtel_money';
    case MoovMoney = 'moov_money';

    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::Card => 'Carte Bancaire',
            self::BankTransfer => 'Virement Bancaire',
            self::MobileMoney => 'Mobile Money',
            self::Cheque => 'Chèque',
            self::Other => 'Autre',
            self::AirtelMoney => 'Airtel Money',
            self::MoovMoney => 'Moov Money',
        };
    }

    public static function getOptions(): array
    {
        return collect(self::cases())
            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
            ->toArray();
    }
}
