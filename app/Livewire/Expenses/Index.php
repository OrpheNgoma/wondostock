<?php

namespace App\Livewire\Expenses;

use App\Models\Employee;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Store;
use App\Services\ExpenseService;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.saas')]
#[Title('Dépenses - WondoStock')]
class Index extends Component
{
    use AuthorizesLivewireActions, WithPagination;

    // --- Filtres ---
    public string $dateFrom = '';

    public string $dateTo = '';

    public string $categoryFilter = '';

    public string $typeFilter = '';

    public string $search = '';

    public string $storeFilter = '';

    // --- Panneau slide-over ---
    public bool $showPanel = false;

    public ?int $editingExpenseId = null;

    // --- Champs du formulaire ---
    public string $label = '';

    public string $amount = '';

    public string $expense_date = '';

    public string $category_id = '';

    public string $recurrence = 'once';

    public string $notes = '';

    public ?int $store_id = null;

    public string $agent = '';

    public string $receipt_number = '';

    public function mount(): void
    {
        $this->dateFrom = Carbon::now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = Carbon::now()->endOfMonth()->format('Y-m-d');
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->expense_date = Carbon::now()->format('Y-m-d');
        $this->editingExpenseId = null;
        $this->showPanel = true;
    }

    public function openEdit(int $id): void
    {
        $expense = Expense::where('company_id', Auth::user()->company_id)->findOrFail($id);

        $this->editingExpenseId = $expense->id;
        $this->label = $expense->label;
        $this->amount = (string) $expense->amount;
        $this->expense_date = $expense->expense_date->format('Y-m-d');
        $this->category_id = (string) ($expense->category_id ?? '');
        $this->recurrence = $expense->recurrence ?? 'once';
        $this->notes = $expense->notes ?? '';
        $this->store_id = $expense->store_id;
        $this->agent = $expense->agent ?? '';
        $this->receipt_number = $expense->receipt_number ?? '';
        $this->showPanel = true;
    }

    public function save(): void
    {
        $permission = $this->editingExpenseId ? 'edit_expenses' : 'create_expenses';
        if (! $this->checkPermission($permission, 'Vous n\'avez pas la permission d\'effectuer cette action.')) {
            return;
        }

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

        if ($this->editingExpenseId) {
            $expense = Expense::where('company_id', $companyId)->findOrFail($this->editingExpenseId);
            $service->update($expense, $data);
            $this->dispatch('notify', message: 'Dépense mise à jour.');
        } else {
            $service->create($data, $companyId);
            $this->dispatch('notify', message: 'Dépense enregistrée.');
        }

        $this->showPanel = false;
        $this->resetForm();
    }

    public function closePanel(): void
    {
        $this->showPanel = false;
        $this->resetForm();
    }

    public function delete(int $id): void
    {
        if (! $this->checkPermission('delete_expenses', 'Vous n\'avez pas la permission de supprimer une dépense.')) {
            return;
        }

        $expense = Expense::where('company_id', Auth::user()->company_id)->findOrFail($id);
        app(ExpenseService::class)->delete($expense);
        $this->dispatch('notify', message: 'Dépense supprimée.');
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $companyId = Auth::user()->company_id;

        $expenses = Expense::with(['category', 'store'])
            ->where('company_id', $companyId)
            ->when($this->search, fn ($q) => $q->where('label', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->typeFilter, fn ($q) => $q->whereHas('category', fn ($cq) => $cq->where('type', $this->typeFilter)))
            ->when($this->storeFilter, fn ($q) => $q->where('store_id', $this->storeFilter))
            ->when($this->dateFrom, fn ($q) => $q->where('expense_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('expense_date', '<=', $this->dateTo))
            ->orderByDesc('expense_date')
            ->get();

        $filename = 'depenses_'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($expenses) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF"); // BOM UTF-8 pour Excel
            fputcsv($handle, ['Date', 'Libellé', 'Catégorie', 'Type', 'Montant (FCFA)', 'Magasin', 'Agent', 'N° Reçu', 'Notes'], ';');

            foreach ($expenses as $expense) {
                fputcsv($handle, [
                    $expense->expense_date->format('d/m/Y'),
                    $expense->label,
                    $expense->category?->name ?? '',
                    $expense->category?->type ?? '',
                    $expense->amount,
                    $expense->store?->name ?? '',
                    $expense->agent ?? '',
                    $expense->receipt_number ?? '',
                    $expense->notes ?? '',
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryFilter(): void
    {
        $this->resetPage();
    }

    public function updatedTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatedStoreFilter(): void
    {
        $this->resetPage();
    }

    private function resetForm(): void
    {
        $this->editingExpenseId = null;
        $this->label = '';
        $this->amount = '';
        $this->expense_date = '';
        $this->category_id = '';
        $this->recurrence = 'once';
        $this->notes = '';
        $this->store_id = null;
        $this->agent = '';
        $this->receipt_number = '';
        $this->resetValidation();
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;
        $now = Carbon::now();

        $query = Expense::with(['category', 'store'])
            ->where('company_id', $companyId)
            ->when($this->search, fn ($q) => $q->where('label', 'like', "%{$this->search}%"))
            ->when($this->categoryFilter, fn ($q) => $q->where('category_id', $this->categoryFilter))
            ->when($this->typeFilter, fn ($q) => $q->whereHas('category', fn ($cq) => $cq->where('type', $this->typeFilter)))
            ->when($this->storeFilter, fn ($q) => $q->where('store_id', $this->storeFilter))
            ->when($this->dateFrom, fn ($q) => $q->where('expense_date', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->where('expense_date', '<=', $this->dateTo))
            ->orderByDesc('expense_date')
            ->orderByDesc('created_at');

        $expenses = $query->paginate(20);

        $monthExpenses = Expense::where('company_id', $companyId)
            ->whereMonth('expense_date', $now->month)
            ->whereYear('expense_date', $now->year)
            ->with('category')
            ->get();

        $stats = [
            'month_total' => (int) $monthExpenses->sum('amount'),
            'fixed_total' => (int) $monthExpenses->filter(fn ($e) => $e->category?->type === 'fixed')->sum('amount'),
            'variable_total' => (int) $monthExpenses->filter(fn ($e) => $e->category?->type === 'variable')->sum('amount'),
            'count' => $monthExpenses->count(),
        ];

        $categories = ExpenseCategory::where('company_id', $companyId)
            ->orderBy('name')
            ->get();

        $stores = Store::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $employees = Employee::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('livewire.expenses.index', compact('expenses', 'stats', 'categories', 'stores', 'employees'));
    }
}
