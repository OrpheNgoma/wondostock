<?php

namespace App\Livewire\Settings;

use App\Models\Audit;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.saas')]
#[Title('Journal d\'audit - WondoStock')]
class AuditLog extends Component
{
    use AuthorizesLivewireActions, WithPagination;

    public string $filterEvent = '';

    public string $filterModel = '';

    public string $filterUser = '';

    public function mount(): void
    {
        $this->requirePermission('view_audit_log');
    }

    public function updatedFilterEvent(): void
    {
        $this->resetPage();
    }

    public function updatedFilterModel(): void
    {
        $this->resetPage();
    }

    public function updatedFilterUser(): void
    {
        $this->resetPage();
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $audits = Audit::with('user')
            ->where('company_id', $companyId)
            ->when($this->filterEvent, fn ($q) => $q->where('event', $this->filterEvent))
            ->when($this->filterModel, fn ($q) => $q->where('auditable_type', $this->filterModel))
            ->when($this->filterUser, fn ($q) => $q->where('user_id', (int) $this->filterUser))
            ->latest('created_at')
            ->paginate(25);

        /** @var array<string> $modelTypes */
        $modelTypes = Audit::where('company_id', $companyId)
            ->distinct()
            ->pluck('auditable_type')
            ->toArray();

        $users = \App\Models\User::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name']);

        return view('livewire.settings.audit-log', compact('audits', 'modelTypes', 'users'));
    }
}
