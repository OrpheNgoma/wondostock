<?php

namespace App\Services;

use App\Enums\SalaryAdvanceStatus;
use App\Enums\SalaryPeriodStatus;
use App\Models\DeliveryTrip;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\SalaryAdvance;
use App\Models\SalaryPeriod;
use App\Models\SalarySlip;
use Illuminate\Support\Carbon;

class SalaryService
{
    public function openPeriod(int $companyId, int $month, int $year): SalaryPeriod
    {
        $monthLabel = Carbon::createFromDate($year, $month, 1)->translatedFormat('F Y');

        return SalaryPeriod::withoutGlobalScopes()->firstOrCreate(
            ['company_id' => $companyId, 'month' => $month, 'year' => $year],
            ['label' => $monthLabel, 'status' => SalaryPeriodStatus::Draft]
        );
    }

    public function generateSlips(SalaryPeriod $period): void
    {
        // --- Chauffeurs (avec tournées, commissions, indemnités) ---
        $drivers = Driver::withoutGlobalScopes()
            ->where('company_id', $period->company_id)
            ->where('is_active', true)
            ->get();

        foreach ($drivers as $driver) {
            $trips = DeliveryTrip::withoutGlobalScopes()
                ->where('company_id', $period->company_id)
                ->where('status', 'closed')
                ->whereMonth('trip_date', $period->month)
                ->whereYear('trip_date', $period->year)
                ->where('driver_id', $driver->id)
                ->get();

            $tripsCount = $trips->count();
            $missionAllowances = (int) $trips->sum('mission_allowance_amount');
            $totalCommissions = (int) ($trips->sum('total_revenue') * DeliveryTrip::COMMISSION_RATE);
            $baseSalary = (int) $driver->base_salary;
            $grossSalary = $baseSalary + $totalCommissions + $missionAllowances;

            $advances = SalaryAdvance::withoutGlobalScopes()
                ->where('company_id', $period->company_id)
                ->where('driver_id', $driver->id)
                ->where('status', SalaryAdvanceStatus::Approved)
                ->whereMonth('advance_date', $period->month)
                ->whereYear('advance_date', $period->year)
                ->get();

            $totalAdvances = (int) $advances->sum('amount');
            $netSalary = $grossSalary - $totalAdvances;

            $slip = SalarySlip::withoutGlobalScopes()->updateOrCreate(
                ['period_id' => $period->id, 'driver_id' => $driver->id],
                [
                    'company_id' => $period->company_id,
                    'employee_name' => $driver->name,
                    'base_salary' => $baseSalary,
                    'gross_salary' => $grossSalary,
                    'total_deductions' => 0,
                    'total_advances' => $totalAdvances,
                    'net_salary' => $netSalary,
                    'trips_count' => $tripsCount,
                    'total_commissions' => $totalCommissions,
                    'mission_allowances' => $missionAllowances,
                ]
            );

            foreach ($advances as $advance) {
                $advance->update([
                    'status' => SalaryAdvanceStatus::Deducted,
                    'deducted_on_slip_id' => $slip->id,
                ]);
            }
        }

        // --- Employés non-chauffeurs (salaire fixe uniquement) ---
        $employees = Employee::withoutGlobalScopes()
            ->where('company_id', $period->company_id)
            ->where('is_active', true)
            ->get();

        foreach ($employees as $employee) {
            $baseSalary = (int) $employee->base_salary;
            $grossSalary = $baseSalary;

            $advances = SalaryAdvance::withoutGlobalScopes()
                ->where('company_id', $period->company_id)
                ->where('employee_id', $employee->id)
                ->where('status', SalaryAdvanceStatus::Approved)
                ->whereMonth('advance_date', $period->month)
                ->whereYear('advance_date', $period->year)
                ->get();

            $totalAdvances = (int) $advances->sum('amount');
            $netSalary = $grossSalary - $totalAdvances;

            $slip = SalarySlip::withoutGlobalScopes()->updateOrCreate(
                ['period_id' => $period->id, 'employee_id' => $employee->id],
                [
                    'company_id' => $period->company_id,
                    'employee_name' => $employee->name,
                    'base_salary' => $baseSalary,
                    'gross_salary' => $grossSalary,
                    'total_deductions' => 0,
                    'total_advances' => $totalAdvances,
                    'net_salary' => $netSalary,
                    'trips_count' => 0,
                    'total_commissions' => 0,
                    'mission_allowances' => 0,
                ]
            );

            foreach ($advances as $advance) {
                $advance->update([
                    'status' => SalaryAdvanceStatus::Deducted,
                    'deducted_on_slip_id' => $slip->id,
                ]);
            }
        }

        $this->computePeriodTotals($period);
    }

    public function computePeriodTotals(SalaryPeriod $period): void
    {
        $slips = SalarySlip::withoutGlobalScopes()
            ->where('period_id', $period->id)
            ->get();

        $period->update([
            'total_gross' => (int) $slips->sum('gross_salary'),
            'total_deductions' => (int) $slips->sum('total_deductions'),
            'total_advances' => (int) $slips->sum('total_advances'),
            'total_net' => (int) $slips->sum('net_salary'),
        ]);
    }

    public function validatePeriod(SalaryPeriod $period, int $userId): void
    {
        $this->computePeriodTotals($period);

        $period->update([
            'status' => SalaryPeriodStatus::Validated,
            'validated_at' => now(),
            'validated_by' => $userId,
        ]);
    }

    public function markPaid(SalaryPeriod $period): void
    {
        $period->update([
            'status' => SalaryPeriodStatus::Paid,
            'paid_at' => now(),
        ]);
    }

    public function addAdvance(int $companyId, array $data): SalaryAdvance
    {
        return SalaryAdvance::create(array_merge($data, [
            'company_id' => $companyId,
            'status' => SalaryAdvanceStatus::Pending,
        ]));
    }

    public function approveAdvance(SalaryAdvance $advance): void
    {
        $advance->update(['status' => SalaryAdvanceStatus::Approved]);
    }
}
