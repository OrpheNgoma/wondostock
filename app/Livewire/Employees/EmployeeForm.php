<?php

namespace App\Livewire\Employees;

use App\Models\Employee;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Employé - WondoStock')]
class EmployeeForm extends Component
{
    public ?Employee $employee = null;

    public string $name = '';

    public string $phone = '';

    public string $email = '';

    public string $position = '';

    public string $hire_date = '';

    public string $base_salary = '';

    public bool $is_active = true;

    public string $notes = '';

    public function mount(?Employee $employee = null): void
    {
        if ($employee && $employee->exists) {
            if ($employee->company_id !== Auth::user()->company_id) {
                abort(403);
            }

            $this->employee = $employee;
            $this->name = $employee->name;
            $this->phone = $employee->phone ?? '';
            $this->email = $employee->email ?? '';
            $this->position = $employee->position ?? '';
            $this->hire_date = $employee->hire_date?->format('Y-m-d') ?? '';
            $this->base_salary = (string) $employee->base_salary;
            $this->is_active = $employee->is_active;
            $this->notes = $employee->notes ?? '';
        }
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

        if ($this->employee && $this->employee->exists) {
            $this->employee->update($data);
        } else {
            Employee::create(array_merge($data, ['company_id' => $companyId]));
        }

        $this->redirect(route('employees.index'), navigate: true);
    }

    public function render(): View
    {
        return view('livewire.employees.employee-form');
    }
}
