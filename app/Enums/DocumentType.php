<?php

namespace App\Enums;

enum DocumentType: string
{
    case Quote = 'quote';
    case Proforma = 'proforma';
    case Order = 'order';
    case DeliveryNote = 'delivery_note';
    case Invoice = 'invoice';
    case CreditNote = 'credit_note';
    case PurchaseOrder = 'purchase_order'; // Bon de Commande Fournisseur

    public function label(): string
    {
        return match ($this) {
            self::Quote => 'Devis',
            self::Proforma => 'Facture Pro-forma',
            self::Order => 'Bon de commande',
            self::DeliveryNote => 'Bon de livraison',
            self::Invoice => 'Facture',
            self::CreditNote => 'Avoir',
            self::PurchaseOrder => 'Bon de Commande Fournisseur',
        };
    }
}
