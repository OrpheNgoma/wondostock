<?php

namespace App\Livewire\Salaries;

use App\Models\SalaryPeriod;
use App\Services\SalaryService;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Période de salaire - WondoStock')]
class PeriodShow extends Component
{
    use AuthorizesLivewireActions;

    public SalaryPeriod $period;

    public function mount(SalaryPeriod $period): void
    {
        if ($period->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $this->requirePermission('view_salaries');
        $this->period = $period;
    }

    public function generateSlips(): void
    {
        if (! $this->checkPermission('manage_salaries', 'Vous n\'avez pas la permission de gérer les salaires.')) {
            return;
        }

        app(SalaryService::class)->generateSlips($this->period);
        $this->period->refresh();
        $this->dispatch('notify', message: 'Bulletins générés avec succès.');
    }

    public function validatePeriod(): void
    {
        if (! $this->checkPermission('validate_salaries', 'Vous n\'avez pas la permission de valider les salaires.')) {
            return;
        }

        app(SalaryService::class)->validatePeriod($this->period, Auth::id());
        $this->period->refresh();
        $this->dispatch('notify', message: 'Période validée.');
    }

    public function markPaid(): void
    {
        if (! $this->checkPermission('validate_salaries', 'Vous n\'avez pas la permission de marquer une période comme payée.')) {
            return;
        }

        app(SalaryService::class)->markPaid($this->period);
        $this->period->refresh();
        $this->dispatch('notify', message: 'Période marquée comme payée.');
    }

    public function render(): View
    {
        $this->period->load(['slips.driver', 'validatedBy']);

        return view('livewire.salaries.period-show');
    }
}
