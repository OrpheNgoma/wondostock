<?php

namespace App\Services;

use App\Enums\SalaryPeriodStatus;
use App\Models\DeliveryTrip;
use App\Models\Document;
use App\Models\Driver;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\SalaryPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class FinancialDashboardService
{
    /**
     * @return array{
     *   documents_revenue: int,
     *   deliveries_revenue: int,
     *   total_revenue: int,
     *   expenses_general: int,
     *   expenses_delivery: int,
     *   salaries_net: int,
     *   total_charges: int,
     *   net_result: int,
     *   margin_rate: float,
     *   evolution: array{revenue: float|null, charges: float|null, net_result: float|null},
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
            'salaries_net' => $this->evolutionPercent($previous['salaries_net'], $current['salaries_net']),
        ];

        // Dépenses par catégorie
        $expensesByCategory = ExpenseCategory::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->withSum(['expenses as month_total' => function ($q) use ($month, $year) {
                $q->whereMonth('expense_date', $month)->whereYear('expense_date', $year);
            }], 'amount')
            ->having('month_total', '>', 0)
            ->orderByDesc('month_total')
            ->get();

        // Top 5 chauffeurs
        $topDrivers = Driver::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->withCount(['deliveryTrips as trips_month' => function ($q) use ($month, $year) {
                $q->where('status', 'closed')
                    ->whereMonth('trip_date', $month)
                    ->whereYear('trip_date', $year);
            }])
            ->withSum(['deliveryTrips as revenue_month' => function ($q) use ($month, $year) {
                $q->where('status', 'closed')
                    ->whereMonth('trip_date', $month)
                    ->whereYear('trip_date', $year);
            }], 'total_revenue')
            ->having('trips_month', '>', 0)
            ->orderByDesc('revenue_month')
            ->limit(5)
            ->get();

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
     * Calcule tous les agrégats financiers pour un mois/année donné.
     *
     * @return array{
     *   documents_revenue: int,
     *   deliveries_revenue: int,
     *   total_revenue: int,
     *   expenses_general: int,
     *   expenses_delivery: int,
     *   salaries_net: int,
     *   total_charges: int,
     *   net_result: int,
     *   margin_rate: float
     * }
     */
    private function computePeriod(int $companyId, int $month, int $year): array
    {
        $documentsRevenue = (int) Document::where('company_id', $companyId)
            ->whereIn('status', ['validated', 'paid', 'partially_paid'])
            ->whereMonth('document_date', $month)
            ->whereYear('document_date', $year)
            ->sum('total_amount');

        $deliveriesRevenue = (int) DeliveryTrip::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'closed')
            ->whereMonth('trip_date', $month)
            ->whereYear('trip_date', $year)
            ->sum('total_revenue');

        $totalRevenue = $documentsRevenue + $deliveriesRevenue;

        $expensesGeneral = (int) Expense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');

        $expensesDelivery = (int) DeliveryTrip::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('status', 'closed')
            ->whereMonth('trip_date', $month)
            ->whereYear('trip_date', $year)
            ->sum('total_expenses');

        $salariesNet = (int) SalaryPeriod::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereIn('status', [SalaryPeriodStatus::Validated->value, SalaryPeriodStatus::Paid->value])
            ->where('month', $month)
            ->where('year', $year)
            ->sum('total_net');

        $totalCharges = $expensesGeneral + $expensesDelivery + $salariesNet;
        $netResult = $totalRevenue - $totalCharges;
        $marginRate = $totalRevenue > 0 ? round($netResult / $totalRevenue * 100, 1) : 0.0;

        return [
            'documents_revenue' => $documentsRevenue,
            'deliveries_revenue' => $deliveriesRevenue,
            'total_revenue' => $totalRevenue,
            'expenses_general' => $expensesGeneral,
            'expenses_delivery' => $expensesDelivery,
            'salaries_net' => $salariesNet,
            'total_charges' => $totalCharges,
            'net_result' => $netResult,
            'margin_rate' => $marginRate,
        ];
    }

    /**
     * Calcule l'évolution en % entre deux valeurs. Retourne null si la base est 0.
     */
    private function evolutionPercent(int $previous, int $current): ?float
    {
        if ($previous === 0) {
            return null;
        }

        return round(($current - $previous) / $previous * 100, 1);
    }
}
