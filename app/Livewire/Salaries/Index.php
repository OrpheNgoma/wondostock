<?php

namespace App\Livewire\Salaries;

use App\Models\SalaryPeriod;
use App\Services\SalaryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Salaires - WondoStock')]
class Index extends Component
{
    public function openCurrentPeriod(): void
    {
        $companyId = Auth::user()->company_id;
        $service = app(SalaryService::class);
        $now = Carbon::now();
        $period = $service->openPeriod($companyId, $now->month, $now->year);

        $this->redirect(route('salaries.period.show', $period), navigate: true);
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;
        $now = Carbon::now();

        $periods = SalaryPeriod::where('company_id', $companyId)
            ->orderByDesc('year')
            ->orderByDesc('month')
            ->get();

        $ytdTotal = SalaryPeriod::where('company_id', $companyId)
            ->whereYear('created_at', $now->year)
            ->sum('total_net');

        $stats = [
            'total_periods' => $periods->count(),
            'ytd_total' => (int) $ytdTotal,
        ];

        return view('livewire.salaries.index', compact('periods', 'stats'));
    }
}
