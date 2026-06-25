<?php

namespace App\Livewire\Finance;

use App\Services\FinancialDashboardService;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Dashboard Financier - WondoStock')]
class Dashboard extends Component
{
    use AuthorizesLivewireActions;

    public int $selectedMonth;

    public int $selectedYear;

    public function mount(): void
    {
        $this->requirePermission('view_financial_reports');

        $now = Carbon::now();
        $this->selectedMonth = $now->month;
        $this->selectedYear = $now->year;
    }

    public function render(): View
    {
        $summary = app(FinancialDashboardService::class)->getSummary(
            Auth::user()->company_id,
            $this->selectedMonth,
            $this->selectedYear,
        );

        $months = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[$m] = Carbon::createFromDate($this->selectedYear, $m, 1)->translatedFormat('F');
        }

        $years = range(Carbon::now()->year, Carbon::now()->year - 3);

        return view('livewire.finance.dashboard', compact('summary', 'months', 'years'));
    }
}
