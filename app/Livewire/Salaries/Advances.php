<?php

namespace App\Livewire\Salaries;

use App\Enums\SalaryAdvanceStatus;
use App\Models\Driver;
use App\Models\Employee;
use App\Models\SalaryAdvance;
use App\Services\SalaryService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Avances sur salaire - WondoStock')]
class Advances extends Component
{
    public string $filterStatus = '';

    public string $filterMonth = '';

    public bool $showForm = false;

    // 'driver' ou 'employee'
    public string $formPersonType = 'driver';

    public string $formDriverId = '';

    public string $formEmployeeId = '';

    public string $formAmount = '';

    public string $formDate = '';

    public string $formReason = '';

    public function mount(): void
    {
        $this->formDate = Carbon::now()->format('Y-m-d');
    }

    public function addAdvance(): void
    {
        $rules = [
            'formPersonType' => ['required', 'in:driver,employee'],
            'formAmount' => ['required', 'numeric', 'min:1'],
            'formDate' => ['required', 'date'],
            'formReason' => ['nullable', 'string', 'max:255'],
        ];

        if ($this->formPersonType === 'driver') {
            $rules['formDriverId'] = ['required', 'exists:drivers,id'];
        } else {
            $rules['formEmployeeId'] = ['required', 'exists:employees,id'];
        }

        $this->validate($rules);

        $data = [
            'amount' => (int) $this->formAmount,
            'advance_date' => $this->formDate,
            'reason' => $this->formReason ?: null,
            'requested_by' => Auth::id(),
        ];

        if ($this->formPersonType === 'driver') {
            $data['driver_id'] = (int) $this->formDriverId;
        } else {
            $data['employee_id'] = (int) $this->formEmployeeId;
        }

        app(SalaryService::class)->addAdvance(Auth::user()->company_id, $data);

        $this->formDriverId = '';
        $this->formEmployeeId = '';
        $this->formAmount = '';
        $this->formReason = '';
        $this->formDate = Carbon::now()->format('Y-m-d');
        $this->showForm = false;

        $this->dispatch('notify', message: 'Avance enregistrée.');
    }

    public function approve(int $id): void
    {
        $advance = SalaryAdvance::where('company_id', Auth::user()->company_id)->findOrFail($id);
        app(SalaryService::class)->approveAdvance($advance);
        $this->dispatch('notify', message: 'Avance approuvée.');
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $advances = SalaryAdvance::with(['driver', 'employee'])
            ->where('company_id', $companyId)
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterMonth, fn ($q) => $q->whereMonth('advance_date', Carbon::parse($this->filterMonth)->month)
                ->whereYear('advance_date', Carbon::parse($this->filterMonth)->year))
            ->orderByDesc('advance_date')
            ->get();

        $drivers = Driver::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $statuses = SalaryAdvanceStatus::cases();

        return view('livewire.salaries.advances', compact('advances', 'drivers', 'employees', 'statuses'));
    }
}
