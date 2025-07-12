<?php

namespace App\Enums;

enum PaymentMethod: string
{
    case Cash = 'cash';
    // Nouveaux types de paiement
    case AirtelMoney = 'airtel_money';
    case MoovMoney = 'moov_money';
    case BankTransfer = 'bank_transfer';
    case Cheque = 'cheque';
    
    public function label(): string
    {
        return match ($this) {
            self::Cash => 'Espèces',
            self::AirtelMoney => 'Airtel Money',
            self::MoovMoney => 'Moov Money',
            self::BankTransfer => 'Virement bancaire',
            self::Cheque => 'Chèque',
        };
    }
}