<?php

namespace App\Livewire\Expenses;

use App\Models\ExpenseCategory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Paramètres Dépenses - WondoStock')]
class Settings extends Component
{
    public bool $showForm = false;

    public ?int $editingId = null;

    /** @var array{name: string, type: string, color: string, icon: string} */
    public array $form = [
        'name' => '',
        'type' => 'variable',
        'color' => '#6366f1',
        'icon' => '',
    ];

    public string $activeTab = 'fixed';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    public function save(): void
    {
        $this->validate([
            'form.name' => ['required', 'string', 'max:150'],
            'form.type' => ['required', 'in:fixed,variable'],
            'form.color' => ['nullable', 'string', 'max:7'],
            'form.icon' => ['nullable', 'string', 'max:50'],
        ]);

        $companyId = Auth::user()->company_id;

        if ($this->editingId) {
            ExpenseCategory::where('company_id', $companyId)
                ->where('id', $this->editingId)
                ->update([
                    'name' => $this->form['name'],
                    'type' => $this->form['type'],
                    'color' => $this->form['color'] ?: null,
                    'icon' => $this->form['icon'] ?: null,
                ]);
        } else {
            ExpenseCategory::create([
                'company_id' => $companyId,
                'name' => $this->form['name'],
                'type' => $this->form['type'],
                'color' => $this->form['color'] ?: null,
                'icon' => $this->form['icon'] ?: null,
            ]);
        }

        $this->resetForm();
        $this->dispatch('notify', message: 'Catégorie enregistrée avec succès.');
    }

    public function edit(int $id): void
    {
        $category = ExpenseCategory::where('company_id', Auth::user()->company_id)
            ->findOrFail($id);

        $this->editingId = $category->id;
        $this->form = [
            'name' => $category->name,
            'type' => $category->type,
            'color' => $category->color ?? '#6366f1',
            'icon' => $category->icon ?? '',
        ];
        $this->activeTab = $category->type;
        $this->showForm = true;
    }

    public function delete(int $id): void
    {
        ExpenseCategory::where('company_id', Auth::user()->company_id)
            ->where('id', $id)
            ->delete();

        $this->dispatch('notify', message: 'Catégorie supprimée.');
    }

    public function resetForm(): void
    {
        $this->showForm = false;
        $this->editingId = null;
        $this->form = [
            'name' => '',
            'type' => $this->activeTab,
            'color' => '#6366f1',
            'icon' => '',
        ];
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $fixedCategories = ExpenseCategory::where('company_id', $companyId)
            ->where('type', 'fixed')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        $variableCategories = ExpenseCategory::where('company_id', $companyId)
            ->where('type', 'variable')
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.expenses.settings', compact('fixedCategories', 'variableCategories'));
    }
}
