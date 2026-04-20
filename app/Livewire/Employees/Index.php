<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Employés - WondoStock')]
class Index extends Component
{
    public string $search = '';

    public bool $showInactive = false;

    // --- Panneau slide-over ---
    public bool $showPanel = false;

    public ?int $editingEmployeeId = null;

    // --- Champs du formulaire ---
    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $position = '';

    public string $hire_date = '';

    public string $base_salary = '';

    public bool $is_active = true;

    public string $notes = '';

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showPanel = true;
    }

    public function openEdit(int $id): void
    {
        $employee = Employee::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $this->editingEmployeeId = $employee->id;
        $this->name = $employee->name;
        $this->phone = $employee->phone ?? '';
        $this->email = $employee->email ?? '';
        $this->position = $employee->position ?? '';
        $this->hire_date = $employee->hire_date?->format('Y-m-d') ?? '';
        $this->base_salary = (string) $employee->base_salary;
        $this->is_active = $employee->is_active;
        $this->notes = $employee->notes ?? '';
        $this->showPanel = true;
    }

    public function save(): void
    {
        $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'position' => ['nullable', 'string', 'max:255'],
            'hire_date' => ['nullable', 'date'],
            'base_salary' => ['required', 'numeric', 'min:0'],
            'is_active' => ['boolean'],
            'notes' => ['nullable', 'string'],
        ]);

        $companyId = Auth::user()->company_id;

        $data = [
            'name' => $this->name,
            'phone' => $this->phone ?: null,
            'email' => $this->email ?: null,
            'position' => $this->position ?: null,
            'hire_date' => $this->hire_date ?: null,
            'base_salary' => (int) $this->base_salary,
            'is_active' => $this->is_active,
            'notes' => $this->notes ?: null,
        ];

        if ($this->editingEmployeeId) {
            $employee = Employee::where('company_id', $companyId)->findOrFail($this->editingEmployeeId);
            $employee->update($data);
            $this->dispatch('notify', message: 'Employé mis à jour.');
        } else {
            Employee::create(array_merge($data, ['company_id' => $companyId]));
            $this->dispatch('notify', message: 'Employé ajouté.');
        }

        $this->showPanel = false;
        $this->resetForm();
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->resetForm();
    }

    public function toggleActive(Employee $employee): void
    {
        if ($employee->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        $employee->update(['is_active' => ! $employee->is_active]);
        $this->dispatch('notify', message: $employee->is_active ? 'Employé réactivé.' : 'Employé désactivé.');
    }

    public function delete(int $employeeId): void
    {
        $employee = Employee::where('company_id', Auth::user()->company_id)->findOrFail($employeeId);
        $employee->delete();
        $this->dispatch('notify', message: 'Employé supprimé.');
    }

    private function resetForm(): void
    {
        $this->editingEmployeeId = null;
        $this->name = '';
        $this->phone = '';
        $this->email = '';
        $this->position = '';
        $this->hire_date = '';
        $this->base_salary = '';
        $this->is_active = true;
        $this->notes = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $employees = Employee::where('company_id', $companyId)
            ->when($this->search, fn ($q) => $q->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('position', 'like', '%'.$this->search.'%'))
            ->when(! $this->showInactive, fn ($q) => $q->where('is_active', true))
            ->orderBy('name')
            ->get();

        $stats = [
            'total' => Employee::where('company_id', $companyId)->count(),
            'active' => Employee::where('company_id', $companyId)->where('is_active', true)->count(),
            'total_base' => (int) Employee::where('company_id', $companyId)->where('is_active', true)->sum('base_salary'),
        ];

        return view('livewire.employees.index', compact('employees', 'stats'));
    }
}
