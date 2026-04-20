<?php

namespace App\Livewire\Finance;

use App\Models\Store;
use App\Services\MonthlyAccountingService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Point de Comptabilité - WondoStock')]
class MonthlyAccounting extends Component
{
    public ?int $store_id = null;

    public string $date_from = '';

    public string $date_to = '';

    public bool $reportGenerated = false;

    public function mount(): void
    {
        $this->date_from = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->date_to = Carbon::now()->format('Y-m-d');

        $companyId = Auth::user()->company_id;
        $stores = Store::where('company_id', $companyId)->where('is_active', true)->get();

        if ($stores->count() === 1) {
            $this->store_id = $stores->first()->id;
        }
    }

    public function generate(): void
    {
        $this->validate([
            'store_id' => ['required', 'integer', 'exists:stores,id'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date', 'after_or_equal:date_from'],
        ]);

        $companyId = Auth::user()->company_id;

        abort_unless(
            Store::where('company_id', $companyId)->where('id', $this->store_id)->exists(),
            403
        );

        $this->reportGenerated = true;
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $stores = Store::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $report = null;

        if ($this->reportGenerated && $this->store_id && $this->date_from && $this->date_to) {
            $report = app(MonthlyAccountingService::class)->getReport(
                $companyId,
                $this->store_id,
                Carbon::parse($this->date_from),
                Carbon::parse($this->date_to)
            );
        }

        return view('livewire.finance.monthly-accounting', compact('stores', 'report'));
    }
}
