<?php

namespace App\Livewire\Salaries;

use App\Models\SalaryDeduction;
use App\Models\SalarySlip;
use App\Services\SalaryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Bulletin de salaire - WondoStock')]
class SlipShow extends Component
{
    public SalarySlip $slip;

    public string $deductionLabel = '';

    public string $deductionType = 'deduction';

    public string $deductionAmount = '';

    public function mount(SalarySlip $slip): void
    {
        if ($slip->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $this->slip = $slip;
    }

    public function addDeduction(): void
    {
        $this->validate([
            'deductionLabel' => ['required', 'string', 'max:150'],
            'deductionType' => ['required', 'in:deduction,bonus'],
            'deductionAmount' => ['required', 'numeric', 'min:1'],
        ]);

        SalaryDeduction::create([
            'slip_id' => $this->slip->id,
            'label' => $this->deductionLabel,
            'type' => $this->deductionType,
            'amount' => (int) $this->deductionAmount,
        ]);

        $this->recomputeSlipTotals();

        $this->deductionLabel = '';
        $this->deductionAmount = '';
        $this->deductionType = 'deduction';

        $this->dispatch('notify', message: 'Ligne ajoutée.');
    }

    public function removeDeduction(int $id): void
    {
        SalaryDeduction::where('slip_id', $this->slip->id)->findOrFail($id)->delete();
        $this->recomputeSlipTotals();
        $this->dispatch('notify', message: 'Ligne supprimée.');
    }

    private function recomputeSlipTotals(): void
    {
        $this->slip->load('deductions');
        $deductions = $this->slip->deductions->where('type', 'deduction')->sum('amount');
        $bonuses = $this->slip->deductions->where('type', 'bonus')->sum('amount');

        $grossWithBonuses = $this->slip->base_salary
            + $this->slip->total_commissions
            + $this->slip->mission_allowances
            + (int) $bonuses;

        $net = $grossWithBonuses - (int) $deductions - $this->slip->total_advances;

        $this->slip->update([
            'gross_salary' => $grossWithBonuses,
            'total_deductions' => (int) $deductions,
            'net_salary' => $net,
        ]);

        app(SalaryService::class)->computePeriodTotals($this->slip->period);

        $this->slip->refresh();
    }

    public function render(): View
    {
        $this->slip->load(['deductions', 'driver', 'period']);

        return view('livewire.salaries.slip-show');
    }
}
