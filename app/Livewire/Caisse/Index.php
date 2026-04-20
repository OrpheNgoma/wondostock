<?php

namespace App\Livewire\Caisse;

use App\Models\CashRegisterSession;
use App\Models\Store;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Gestion de la Caisse - WondoStock')]
class Index extends Component
{
    public string $selectedStore = '';

    public string $monthFilter = '';

    public bool $showForm = false;

    public ?int $form_store_id = null;

    public string $form_session_date = '';

    public int $form_opening_balance = 0;

    public function mount(): void
    {
        $this->monthFilter = now()->format('Y-m');
        $this->form_session_date = now()->format('Y-m-d');
    }

    public function openSession(): void
    {
        $this->validate([
            'form_store_id' => ['required', 'exists:stores,id'],
            'form_session_date' => ['required', 'date'],
            'form_opening_balance' => ['required', 'integer', 'min:0'],
        ]);

        $companyId = Auth::user()->company_id;

        $existing = CashRegisterSession::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $this->form_store_id)
            ->whereDate('session_date', $this->form_session_date)
            ->first();

        if ($existing) {
            $this->addError('form_session_date', 'Une session existe déjà pour cette boutique à cette date.');

            return;
        }

        CashRegisterSession::create([
            'company_id' => $companyId,
            'store_id' => $this->form_store_id,
            'user_id' => Auth::id(),
            'session_date' => $this->form_session_date,
            'opening_balance' => $this->form_opening_balance,
            'cash_in' => 0,
            'cash_out' => 0,
            'remittances' => 0,
            'closing_balance' => $this->form_opening_balance,
            'status' => 'open',
        ]);

        $this->showForm = false;
        $this->form_store_id = null;
        $this->form_session_date = now()->format('Y-m-d');
        $this->form_opening_balance = 0;

        $this->dispatch('notify', message: 'Session de caisse ouverte.');
    }

    public function closeSession(int $id): void
    {
        $companyId = Auth::user()->company_id;

        $session = CashRegisterSession::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->findOrFail($id);

        $session->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closing_balance' => $session->computeClosingBalance(),
        ]);

        $this->dispatch('notify', message: 'Session de caisse clôturée.');
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

        $sessions = CashRegisterSession::withoutGlobalScopes()
            ->with(['store', 'user'])
            ->where('company_id', $companyId)
            ->when($this->selectedStore, fn ($q) => $q->where('store_id', $this->selectedStore))
            ->when($this->monthFilter, function ($q) {
                [$year, $month] = explode('-', $this->monthFilter);
                $q->whereYear('session_date', $year)->whereMonth('session_date', $month);
            })
            ->orderByDesc('session_date')
            ->get();

        $monthStats = [
            'avg_closing' => $sessions->isNotEmpty() ? (int) $sessions->avg('closing_balance') : 0,
            'total_cash_in' => (int) $sessions->sum('cash_in'),
            'total_cash_out' => (int) $sessions->sum('cash_out'),
            'total_remittances' => (int) $sessions->sum('remittances'),
        ];

        return view('livewire.caisse.index', compact('stores', 'sessions', 'monthStats'));
    }
}
