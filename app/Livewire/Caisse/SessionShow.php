<?php

namespace App\Livewire\Caisse;

use App\Enums\CashMovementType;
use App\Models\CashMovement;
use App\Models\CashRegisterSession;
use App\Services\CashMovementService;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Session de Caisse - WondoStock')]
class SessionShow extends Component
{
    use AuthorizesLivewireActions;

    public CashRegisterSession $session;

    // --- Formulaire ajout de mouvement ---
    public bool $showMovementForm = false;

    public string $movementType = 'cash_in';

    public string $movementAmount = '';

    public string $movementLabel = '';

    public string $notes = '';

    public function mount(CashRegisterSession $session): void
    {
        abort_unless($session->company_id === Auth::user()->company_id, 403);
        $this->requirePermission('view_cash_sessions');

        $this->session = $session;
        $this->notes = $session->notes ?? '';
    }

    public function openMovementForm(string $type = 'cash_in'): void
    {
        $this->movementType = $type;
        $this->movementAmount = '';
        $this->movementLabel = '';
        $this->showMovementForm = true;
        $this->resetValidation();
    }

    public function closeMovementForm(): void
    {
        $this->showMovementForm = false;
        $this->movementType = 'cash_in';
        $this->movementAmount = '';
        $this->movementLabel = '';
        $this->resetValidation();
    }

    public function addMovement(CashMovementService $service): void
    {
        if (! $this->checkPermission('manage_cash_sessions', 'Vous n\'avez pas la permission d\'ajouter un mouvement.')) {
            return;
        }

        $this->validate([
            'movementType' => ['required', 'in:cash_in,cash_out,remittance'],
            'movementAmount' => ['required', 'integer', 'min:1'],
            'movementLabel' => ['required', 'string', 'max:255'],
        ]);

        $service->addMovement(
            session: $this->session,
            type: CashMovementType::from($this->movementType),
            amount: (int) $this->movementAmount,
            label: $this->movementLabel,
        );

        $this->session->refresh();
        $this->closeMovementForm();
        $this->dispatch('notify', message: 'Mouvement enregistré.');
    }

    public function deleteMovement(int $movementId, CashMovementService $service): void
    {
        $movement = CashMovement::withoutGlobalScopes()
            ->where('session_id', $this->session->id)
            ->findOrFail($movementId);

        // Empêcher la suppression de mouvements liés à une dépense ou un paiement
        if ($movement->expense_id !== null) {
            $this->dispatch('notify', message: 'Ce mouvement est lié à une dépense. Supprimez la dépense pour le retirer.');

            return;
        }

        $service->deleteMovement($movement);
        $this->session->refresh();
        $this->dispatch('notify', message: 'Mouvement supprimé.');
    }

    public function saveNotes(): void
    {
        $this->validate([
            'notes' => ['nullable', 'string'],
        ]);

        $this->session->update(['notes' => $this->notes ?: null]);
        $this->session->refresh();
        $this->dispatch('notify', message: 'Notes sauvegardées.');
    }

    public function close(): void
    {
        if (! $this->checkPermission('close_cash_sessions', 'Vous n\'avez pas la permission de clôturer une session.')) {
            return;
        }

        $this->session->update([
            'status' => 'closed',
            'closed_at' => now(),
            'closing_balance' => $this->session->computeClosingBalance(),
        ]);

        $this->session->refresh();
        $this->dispatch('notify', message: 'Session clôturée avec succès.');
    }

    public function render(): View
    {
        $movements = CashMovement::withoutGlobalScopes()
            ->with(['user', 'expense'])
            ->where('session_id', $this->session->id)
            ->orderBy('movement_date')
            ->orderBy('created_at')
            ->get();

        $movementTypes = CashMovementType::cases();

        return view('livewire.caisse.session-show', compact('movements', 'movementTypes'));
    }
}
