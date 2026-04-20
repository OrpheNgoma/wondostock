<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class ExpenseService
{
    public function create(array $data, int $companyId): Expense
    {
        return Expense::create(array_merge($data, [
            'company_id' => $companyId,
            'user_id' => $data['user_id'] ?? Auth::id(),
        ]));
    }

    public function update(Expense $expense, array $data): Expense
    {
        $expense->update($data);

        return $expense->fresh();
    }

    public function delete(Expense $expense): void
    {
        $expense->delete();
    }

    public function getMonthlyTotal(int $companyId, int $month, int $year): int
    {
        return (int) Expense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->whereMonth('expense_date', $month)
            ->whereYear('expense_date', $year)
            ->sum('amount');
    }

    /**
     * @return array{total: int, by_category: Collection, fixed_total: int, variable_total: int}
     */
    public function getPeriodSummary(int $companyId, Carbon $start, Carbon $end): array
    {
        $expenses = Expense::withoutGlobalScopes()
            ->with('category')
            ->where('company_id', $companyId)
            ->whereBetween('expense_date', [$start->toDateString(), $end->toDateString()])
            ->get();

        $byCategory = $expenses
            ->groupBy('category_id')
            ->map(function ($items) {
                $first = $items->first();

                return [
                    'category' => $first->category,
                    'total' => $items->sum('amount'),
                    'count' => $items->count(),
                ];
            })
            ->values();

        $fixedTotal = (int) $expenses
            ->filter(fn ($e) => $e->category?->type === 'fixed')
            ->sum('amount');

        $variableTotal = (int) $expenses
            ->filter(fn ($e) => $e->category?->type === 'variable')
            ->sum('amount');

        return [
            'total' => (int) $expenses->sum('amount'),
            'by_category' => $byCategory,
            'fixed_total' => $fixedTotal,
            'variable_total' => $variableTotal,
        ];
    }
}
