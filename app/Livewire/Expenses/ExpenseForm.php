<?php

namespace App\Livewire\Expenses;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Store;
use App\Services\ExpenseService;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Dépense - WondoStock')]
class ExpenseForm extends Component
{
    public ?Expense $expense = null;

    public string $label = '';

    public string $amount = '';

    public string $expense_date = '';

    public string $category_id = '';

    public string $recurrence = 'once';

    public string $notes = '';

    public ?int $store_id = null;

    public string $agent = '';

    public string $receipt_number = '';

    public function mount(?Expense $expense = null): void
    {
        if ($expense && $expense->exists) {
            $this->expense = $expense;
            $this->label = $expense->label;
            $this->amount = (string) $expense->amount;
            $this->expense_date = $expense->expense_date->format('Y-m-d');
            $this->category_id = (string) ($expense->category_id ?? '');
            $this->recurrence = $expense->recurrence ?? 'once';
            $this->notes = $expense->notes ?? '';
            $this->store_id = $expense->store_id;
            $this->agent = $expense->agent ?? '';
            $this->receipt_number = $expense->receipt_number ?? '';
        } else {
            $this->expense_date = Carbon::now()->format('Y-m-d');
        }
    }

    public function save(): void
    {
        $this->validate([
            'label' => ['required', 'string', 'max:255'],
            'amount' => ['required', 'numeric', 'min:1'],
            'expense_date' => ['required', 'date'],
            'category_id' => ['nullable', 'exists:expense_categories,id'],
            'recurrence' => ['nullable', 'in:once,monthly,weekly'],
            'notes' => ['nullable', 'string'],
            'store_id' => ['nullable', 'exists:stores,id'],
            'agent' => ['nullable', 'string', 'max:150'],
            'receipt_number' => ['nullable', 'string', 'max:100'],
        ]);

        $service = app(ExpenseService::class);
        $companyId = Auth::user()->company_id;

        $data = [
            'label' => $this->label,
            'amount' => (int) $this->amount,
            'expense_date' => $this->expense_date,
            'category_id' => $this->category_id ?: null,
            'recurrence' => $this->recurrence ?: null,
            'notes' => $this->notes ?: null,
            'store_id' => $this->store_id ?: null,
            'agent' => $this->agent ?: null,
            'receipt_number' => $this->receipt_number ?: null,
        ];

        if ($this->expense && $this->expense->exists) {
            $service->update($this->expense, $data);
        } else {
            $service->create($data, $companyId);
        }

        $this->redirect(route('expenses.index'), navigate: true);
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $categories = ExpenseCategory::where('company_id', $companyId)
            ->orderBy('type')
            ->orderBy('name')
            ->get();

        $stores = Store::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.expenses.expense-form', compact('categories', 'stores'));
    }
}
