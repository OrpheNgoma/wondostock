<?php

namespace App\Services;

use App\Enums\DocumentType;
use App\Models\DeliveryTrip;
use App\Models\Document;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FinancialDashboardService
{
    /**
     * Statuts considérés comme « finalisés » pour la reconnaissance du revenu,
     * alignés sur la convention de décrémentation de stock (DocumentManagementService).
     *
     * @var array<int, string>
     */
    private const FINALIZED_DOCUMENT_STATUSES = ['validated', 'paid', 'partially_paid'];

    /**
     * Mémoïsation intra-requête des agrégats par période (clé "année-mois"),
     * pour éviter de recalculer les mois qui se chevauchent (courant, précédent, tendance).
     *
     * @var array<string, array<string, int|float>>
     */
    private array $periodCache = [];

    /**
     * @return array{
     *   documents_revenue: int,
     *   deliveries_revenue: int,
     *   total_revenue: int,
     *   expenses_general: int,
     *   expenses_delivery: int,
     *   delivery_funds: int,
     *   delivery_commissions: int,
     *   salaries_base: int,
     *   total_charges: int,
     *   net_result: int,
     *   margin_rate: float,
     *   evolution: array<string, float|null>,
     *   expenses_by_category: Collection,
     *   top_drivers: Collection,
     *   monthly_trend: array
     * }
     */
    public function getSummary(int $companyId, int $month, int $year): array
    {
        $current = $this->computePeriod($companyId, $month, $year);

        // Mois précédent pour l'évolution
        $prevDate = Carbon::createFromDate($year, $month, 1)->subMonth();
        $previous = $this->computePeriod($companyId, $prevDate->month, $prevDate->year);

        $evolution = [
            'revenue' => $this->evolutionPercent($previous['total_revenue'], $current['total_revenue']),
            'charges' => $this->evolutionPercent($previous['total_charges'], $current['total_charges']),
            'net_result' => $this->evolutionPercent($previous['net_result'], $current['net_result']),
            'expenses_general' => $this->evolutionPercent($previous['expenses_general'], $current['expenses_general']),
            'salaries_base' => $this->evolutionPercent($previous['salaries_base'], $current['salaries_base']),
        ];

        [$start, $end] = $this->monthRange($month, $year);

        // Dépenses par catégorie (dépenses générales uniquement).
        // Filtrage/tri côté PHP : un HAVING sur un alias agrégé n'est pas portable (SQLite).
        $expensesByCategory = ExpenseCategory::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->withSum(['expenses as month_total' => function ($q) use ($start, $end) {
                $q->whereBetween('expense_date', [$start, $end]);
            }], 'amount')
            ->get()
            ->filter(fn ($cat) => (int) $cat->month_total > 0)
            ->sortByDesc('month_total')
            ->values();

        // Top 5 chauffeurs (recette brute encaissée)
        $topDrivers = Driver::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->withCount(['deliveryTrips as trips_month' => function ($q) use ($start, $end) {
                $q->where('status', 'closed')->whereBetween('trip_date', [$start, $end]);
            }])
            ->withSum(['deliveryTrips as revenue_month' => function ($q) use ($start, $end) {
                $q->where('status', 'closed')->whereBetween('trip_date', [$start, $end]);
            }], 'total_revenue')
            ->get()
            ->filter(fn ($driver) => $driver->trips_month > 0)
            ->sortByDesc('revenue_month')
            ->take(5)
            ->values();

        // Tendance 6 mois (inclut salaires dans le résultat)
        $monthlyTrend = [];
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::createFromDate($year, $month, 1)->subMonths($i);
            $p = $this->computePeriod($companyId, $date->month, $date->year);

            $monthlyTrend[] = [
                'month_label' => $date->translatedFormat('M Y'),
                'revenue' => $p['total_revenue'],
                'charges' => $p['total_charges'],
                'net_result' => $p['net_result'],
            ];
        }

        return array_merge($current, [
            'evolution' => $evolution,
            'expenses_by_category' => $expensesByCategory,
            'top_drivers' => $topDrivers,
            'monthly_trend' => $monthlyTrend,
        ]);
    }

    /**
     * Calcule tous les agrégats financiers pour un mois/année donné (mémoïsé).
     *
     * Modèle de revenu/charges cohérent :
     *  - Recette ventes  = Factures (HT) − Avoirs (HT), statuts finalisés uniquement.
     *  - Recette livraisons = recette brute encaissée (total_revenue).
     *  - Charges = dépenses générales + dépenses tournées
     *              + fonds reversés au fournisseur + commissions chauffeurs
     *              + masse salariale de base (chauffeurs + employés actifs).
     *
     * Les commissions sont reconnues depuis les tournées clôturées du mois
     * (indépendamment de la validation de la paie), et la main-d'œuvre est
     * comptée au brut (base + commissions), jamais nette d'avances.
     *
     * @return array{
     *   documents_revenue: int, deliveries_revenue: int, total_revenue: int,
     *   expenses_general: int, expenses_delivery: int, delivery_funds: int,
     *   delivery_commissions: int, salaries_base: int, total_charges: int,
     *   net_result: int, margin_rate: float
     * }
     */
    private function computePeriod(int $companyId, int $month, int $year): array
    {
        $cacheKey = "{$year}-{$month}";

        if (isset($this->periodCache[$cacheKey])) {
            return $this->periodCache[$cacheKey];
        }

        [$start, $end] = $this->monthRange($month, $year);

        // --- Recettes ventes : Factures (HT) − Avoirs (HT) ---
        $salesBase = fn () => Document::where('company_id', $companyId)
            ->whereIn('status', self::FINALIZED_DOCUMENT_STATUSES)
            ->whereBetween('document_date', [$start, $end]);

        $invoices = (int) $salesBase()->where('type', DocumentType::Invoice)->sum('sub_total');
        $creditNotes = (int) $salesBase()->where('type', DocumentType::CreditNote)->sum('sub_total');
        $documentsRevenue = $invoices - $creditNotes;

        // --- Recette livraisons (brute) + composantes à reverser/rémunérer ---
        $deliveryAggregates = DeliveryTrip::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'closed')
            ->whereBetween('trip_date', [$start, $end])
            ->selectRaw('
                COALESCE(SUM(total_revenue), 0) as revenue,
                COALESCE(SUM(total_expenses), 0) as expenses,
                COALESCE(SUM(funds_amount), 0) as funds,
                COALESCE(SUM(commission_amount), 0) as commissions
            ')
            ->first();

        $deliveriesRevenue = (int) $deliveryAggregates->revenue;
        $expensesDelivery = (int) $deliveryAggregates->expenses;
        $deliveryFunds = (int) $deliveryAggregates->funds;
        $deliveryCommissions = (int) $deliveryAggregates->commissions;

        $totalRevenue = $documentsRevenue + $deliveriesRevenue;

        // --- Dépenses générales ---
        $expensesGeneral = (int) Expense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$start, $end])
            ->sum('amount');

        // --- Masse salariale de base (fixe) : chauffeurs + employés actifs ---
        $salariesBase = (int) Driver::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->sum('base_salary');

        $salariesBase += (int) Employee::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->sum('base_salary');

        $totalCharges = $expensesGeneral + $expensesDelivery
            + $deliveryFunds + $deliveryCommissions + $salariesBase;
        $netResult = $totalRevenue - $totalCharges;
        $marginRate = $totalRevenue > 0 ? round($netResult / $totalRevenue * 100, 1) : 0.0;

        return $this->periodCache[$cacheKey] = [
            'documents_revenue' => $documentsRevenue,
            'deliveries_revenue' => $deliveriesRevenue,
            'total_revenue' => $totalRevenue,
            'expenses_general' => $expensesGeneral,
            'expenses_delivery' => $expensesDelivery,
            'delivery_funds' => $deliveryFunds,
            'delivery_commissions' => $deliveryCommissions,
            'salaries_base' => $salariesBase,
            'total_charges' => $totalCharges,
            'net_result' => $netResult,
            'margin_rate' => $marginRate,
        ];
    }

    /**
     * Bornes [début, fin] d'un mois, pour des filtres de date *sargables* (index-friendly).
     *
     * @return array{0: Carbon, 1: Carbon}
     */
    private function monthRange(int $month, int $year): array
    {
        $start = Carbon::createFromDate($year, $month, 1)->startOfMonth();

        return [$start, $start->copy()->endOfMonth()];
    }

    /**
     * Évolution en % entre deux valeurs. Retourne null si la base est ≤ 0
     * (un pourcentage sur une base nulle ou négative n'aurait pas de sens).
     */
    private function evolutionPercent(int $previous, int $current): ?float
    {
        if ($previous <= 0) {
            return null;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }
}
