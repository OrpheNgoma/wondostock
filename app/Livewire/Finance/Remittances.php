<?php

namespace App\Livewire\Finance;

use App\Models\CashRemittance;
use App\Models\Store;
use App\Services\CashRemittanceService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Versements DG - WondoStock')]
class Remittances extends Component
{
    public string $search = '';

    public string $storeFilter = '';

    public string $monthFilter = '';

    public bool $showForm = false;

    public ?int $form_store_id = null;

    public string $form_received_by = '';

    public string $form_amount = '';

    public string $form_remittance_date = '';

    public string $form_reference = '';

    public string $form_notes = '';

    public function mount(): void
    {
        $this->monthFilter = now()->format('Y-m');
        $this->form_remittance_date = now()->format('Y-m-d');
    }

    public function save(): void
    {
        $this->validate([
            'form_store_id' => ['required', 'exists:stores,id'],
            'form_received_by' => ['required', 'string', 'max:255'],
            'form_amount' => ['required', 'numeric', 'min:1'],
            'form_remittance_date' => ['required', 'date'],
            'form_reference' => ['nullable', 'string', 'max:100'],
            'form_notes' => ['nullable', 'string'],
        ]);

        $companyId = Auth::user()->company_id;

        app(CashRemittanceService::class)->create([
            'store_id' => $this->form_store_id,
            'received_by' => $this->form_received_by,
            'amount' => (int) $this->form_amount,
            'remittance_date' => $this->form_remittance_date,
            'reference' => $this->form_reference ?: null,
            'notes' => $this->form_notes ?: null,
        ], $companyId);

        $this->showForm = false;
        $this->form_store_id = null;
        $this->form_received_by = '';
        $this->form_amount = '';
        $this->form_remittance_date = now()->format('Y-m-d');
        $this->form_reference = '';
        $this->form_notes = '';

        $this->dispatch('notify', message: 'Versement enregistré.');
    }

    public function delete(int $id): void
    {
        $companyId = Auth::user()->company_id;

        CashRemittance::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->findOrFail($id)
            ->delete();

        $this->dispatch('notify', message: 'Versement supprimé.');
    }

    public function setShowForm(bool $value): void
    {
        $this->showForm = $value;
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $stores = Store::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $remittances = CashRemittance::withoutGlobalScopes()
            ->with(['store', 'user'])
            ->where('company_id', $companyId)
            ->when($this->search, fn ($q) => $q->where('received_by', 'like', "%{$this->search}%"))
            ->when($this->storeFilter, fn ($q) => $q->where('store_id', $this->storeFilter))
            ->when($this->monthFilter, function ($q) {
                [$year, $month] = explode('-', $this->monthFilter);
                $q->whereYear('remittance_date', $year)->whereMonth('remittance_date', $month);
            })
            ->orderByDesc('remittance_date')
            ->orderByDesc('created_at')
            ->get();

        $monthStats = [
            'total' => (int) $remittances->sum('amount'),
            'count' => $remittances->count(),
            'by_store' => $remittances->groupBy('store_id')->map(fn ($items) => [
                'store' => $items->first()->store,
                'total' => (int) $items->sum('amount'),
            ])->values(),
        ];

        return view('livewire.finance.remittances', compact('stores', 'remittances', 'monthStats'));
    }
}
