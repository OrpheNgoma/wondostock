<?php

namespace App\Enums;

enum ModuleKey: string
{
    case Deliveries = 'deliveries';
    case Salaries = 'salaries';
    case Expenses = 'expenses';
    case AdvancedReports = 'advanced_reports';
    case Fuel = 'fuel';
    case Caisse = 'caisse';
    case CashRemittances = 'cash_remittances';
    case MonthlyAccounting = 'monthly_accounting';

    public function label(): string
    {
        return match ($this) {
            self::Deliveries => 'Tournées de livraison',
            self::Salaries => 'Gestion des salaires',
            self::Expenses => 'Gestion des dépenses',
            self::AdvancedReports => 'Rapports avancés',
            self::Fuel => 'Suivi carburant',
            self::Caisse => 'Caisse',
            self::CashRemittances => 'Versements DG',
            self::MonthlyAccounting => 'Point de Comptabilité',
        };
    }

    public function description(): string
    {
        return match ($this) {
            self::Deliveries => 'Gestion des chauffeurs, tournées, chargements/retours et commissions.',
            self::Salaries => 'Avances sur salaire, quinzaines et bulletins de paie.',
            self::Expenses => 'Suivi des dépenses : loyer, internet, réparations, etc.',
            self::AdvancedReports => 'Tableaux de bord avancés : recettes, performance par chauffeur/zone.',
            self::Fuel => 'Suivi de la consommation de carburant par véhicule.',
            self::Caisse => 'Gestion de la caisse journalière',
            self::CashRemittances => 'Versements au directeur général',
            self::MonthlyAccounting => 'Rapport mensuel consolidé par boutique',
        };
    }

    public function icon(): string
    {
        return match ($this) {
            self::Deliveries => 'heroicon-o-truck',
            self::Salaries => 'heroicon-o-banknotes',
            self::Expenses => 'heroicon-o-receipt-percent',
            self::AdvancedReports => 'heroicon-o-chart-bar',
            self::Fuel => 'heroicon-o-fire',
            self::Caisse => 'heroicon-o-banknotes',
            self::CashRemittances => 'heroicon-o-arrow-up-tray',
            self::MonthlyAccounting => 'heroicon-o-document-chart-bar',
        };
    }

    public function category(): string
    {
        return match ($this) {
            self::Deliveries, self::Fuel => 'Logistique',
            self::Salaries, self::Expenses, self::Caisse, self::CashRemittances => 'Finance',
            self::AdvancedReports, self::MonthlyAccounting => 'Analyses',
        };
    }

    /** @return array<string, string> */
    public static function groupedByCategory(): array
    {
        $groups = [];
        foreach (self::cases() as $case) {
            $groups[$case->category()][$case->value] = $case->label();
        }

        return $groups;
    }
}
