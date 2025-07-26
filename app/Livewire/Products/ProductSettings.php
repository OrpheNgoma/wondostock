<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Tax;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Paramètres des Produits - WondoStock')]
class ProductSettings extends Component
{
    // --- State for Modal/Form ---
    public bool $showForm = false;
    public string $formType = ''; // 'category' or 'tax'
    public string $activeTab = 'categories'; // 'categories' or 'taxes'

    // --- UI State ---
    public bool $isLoading = false;
    public string $searchCategories = '';
    public string $searchTaxes = '';
    public string $categoryFilter = 'all'; // 'all', 'parent', 'child'
    public string $taxFilter = 'all'; // 'all', 'default', 'custom'

    // --- Category Properties ---
    public ?Category $editingCategory;
    public string $categoryName = '';
    public ?int $categoryParentId = null;
    public string $categoryDescription = '';
    public string $categoryColor = '#3B82F6';
    public string $categoryIcon = '';

    // --- Tax Properties ---
    public ?Tax $editingTax;
    public string $taxName = '';
    public string $taxRate = '';
    public bool $taxIsDefault = false;
    public string $taxDescription = '';

    // --- Bulk Actions ---
    public array $selectedCategories = [];
    public array $selectedTaxes = [];
    public bool $selectAllCategories = false;
    public bool $selectAllTaxes = false;

    protected function rules()
    {
        if ($this->formType === 'category') {
            return [
                'categoryName' => 'required|string|min:2|max:255',
                'categoryDescription' => 'nullable|string|max:500',
                'categoryColor' => 'required|string|regex:/^#[0-9A-F]{6}$/i',
                'categoryIcon' => 'nullable|string|max:50',
            ];
        }
        if ($this->formType === 'tax') {
            return [
                'taxName' => 'required|string|min:3|max:255',
                'taxRate' => 'required|numeric|min:0|max:100',
                'taxDescription' => 'nullable|string|max:500',
            ];
        }

        return [];
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function updatedSelectAllCategories($value)
    {
        if ($value) {
            $this->selectedCategories = $this->getFilteredCategories()->pluck('id')->toArray();
        } else {
            $this->selectedCategories = [];
        }
    }

    public function updatedSelectAllTaxes($value)
    {
        if ($value) {
            $this->selectedTaxes = $this->getFilteredTaxes()->pluck('id')->toArray();
        } else {
            $this->selectedTaxes = [];
        }
    }

    public function bulkDeleteCategories()
    {
        if (empty($this->selectedCategories)) {
            return;
        }

        $count = Category::whereIn('id', $this->selectedCategories)
            ->where('company_id', Auth::user()->company_id)
            ->delete();

        $this->selectedCategories = [];
        $this->selectAllCategories = false;
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Suppression de {$count} catégorie(s) réussie"
        ]);
    }

    public function bulkDeleteTaxes()
    {
        if (empty($this->selectedTaxes)) {
            return;
        }

        $count = Tax::whereIn('id', $this->selectedTaxes)
            ->where('company_id', Auth::user()->company_id)
            ->delete();

        $this->selectedTaxes = [];
        $this->selectAllTaxes = false;
        
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => "Suppression de {$count} taxe(s) réussie"
        ]);
    }

    // ========== CATEGORY METHODS ==========

    public function createCategory()
    {
        $this->resetForm();
        $this->formType = 'category';
        $this->editingCategory = new Category;
        $this->categoryColor = '#3B82F6';
        $this->dispatch('open-form');
    }

    public function editCategory(Category $category)
    {
        $this->resetForm();
        $this->formType = 'category';
        $this->editingCategory = $category;
        $this->categoryName = $category->name;
        $this->categoryParentId = $category->parent_id;
        $this->categoryDescription = $category->description ?? '';
        $this->categoryColor = $category->color ?? '#3B82F6';
        $this->categoryIcon = $category->icon ?? '';
        $this->dispatch('open-form');
    }

    public function deleteCategory(Category $category)
    {
        // Add logic here to check if category is in use before deleting
        $category->delete();
        $this->dispatch('notify', message: 'Catégorie supprimée.');
    }

    // ========== TAX METHODS ==========

    public function createTax()
    {
        $this->resetForm();
        $this->formType = 'tax';
        $this->editingTax = new Tax;
        $this->dispatch('open-form');
    }

    public function editTax(Tax $tax)
    {
        $this->resetForm();
        $this->formType = 'tax';
        $this->editingTax = $tax;
        $this->taxName = $tax->name;
        $this->taxRate = (string) $tax->rate;
        $this->taxIsDefault = $tax->is_default;
        $this->dispatch('open-form');
    }

    public function deleteTax(Tax $tax)
    {
        // Add logic here to check if tax is in use before deleting
        $tax->delete();
        $this->dispatch('notify', message: 'Taxe supprimée.');
    }

    // ========== SAVE & RENDER ==========

    public function save()
    {
        $this->validate();
        $companyId = Auth::user()->company_id;

        if ($this->formType === 'category') {
            $data = [
                'name' => $this->categoryName,
                'parent_id' => $this->categoryParentId,
                'description' => $this->categoryDescription,
                'slug' => Str::slug($this->categoryName),
                'company_id' => $companyId,
            ];

            if ($this->editingCategory->exists) {
                $this->editingCategory->update($data);
                $this->dispatch('notify', message: 'Catégorie mise à jour.');
            } else {
                Category::create($data);
                $this->dispatch('notify', message: 'Catégorie créée.');
            }
        }

        if ($this->formType === 'tax') {
            $data = [
                'name' => $this->taxName,
                'rate' => $this->taxRate,
                'is_default' => $this->taxIsDefault,
                'company_id' => $companyId,
            ];

            DB::transaction(function () use ($data, $companyId) {
                // If this tax is set as default, unset other defaults for the same company
                if ($data['is_default']) {
                    Tax::where('company_id', $companyId)->update(['is_default' => false]);
                }

                if ($this->editingTax->exists) {
                    $this->editingTax->update($data);
                    $this->dispatch('notify', message: 'Taxe mise à jour.');
                } else {
                    Tax::create($data);
                    $this->dispatch('notify', message: 'Taxe créée.');
                }
            });
        }

        $this->dispatch('close-form');
    }

    private function resetForm()
    {
        $this->resetErrorBag();
        $this->editingCategory = null;
        $this->categoryName = '';
        $this->categoryParentId = null;
        $this->categoryDescription = '';
        $this->editingTax = null;
        $this->taxName = '';
        $this->taxRate = '';
        $this->taxIsDefault = false;
    }

    private function getFilteredCategories()
    {
        $companyId = Auth::user()->company_id;
        $query = Category::where('company_id', $companyId)->with('children');

        // Search filter
        if ($this->searchCategories) {
            $query->where('name', 'like', '%' . $this->searchCategories . '%');
        }

        // Type filter
        switch ($this->categoryFilter) {
            case 'parent':
                $query->whereNull('parent_id');
                break;
            case 'child':
                $query->whereNotNull('parent_id');
                break;
        }

        return $query->orderBy('name')->get();
    }

    private function getFilteredTaxes()
    {
        $companyId = Auth::user()->company_id;
        $query = Tax::where('company_id', $companyId);

        // Search filter
        if ($this->searchTaxes) {
            $query->where('name', 'like', '%' . $this->searchTaxes . '%');
        }

        // Type filter
        switch ($this->taxFilter) {
            case 'default':
                $query->where('is_default', true);
                break;
            case 'custom':
                $query->where('is_default', false);
                break;
        }

        return $query->orderBy('rate')->get();
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;
        
        $categories = $this->getFilteredCategories();
        $taxes = $this->getFilteredTaxes();
        $categoryOptions = Category::where('company_id', $companyId)->pluck('name', 'id');

        return view('livewire.saas.products.product-settings', [
            'categories' => $categories,
            'taxes' => $taxes,
            'categoryOptions' => $categoryOptions,
        ]);
    }
}
