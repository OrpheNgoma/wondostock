<?php

namespace App\Livewire\Products;

use App\Models\Tax;
use Livewire\Component;
use App\Models\Category;
use Illuminate\Support\Str;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Paramètres des Produits - WondoStock')]
class ProductSettings extends Component
{
    // --- State for Modal/Form ---
    public bool $showForm = false;
    public string $formType = ''; // 'category' or 'tax'

    // --- Category Properties ---
    public ?Category $editingCategory;
    public string $categoryName = '';
    public ?int $categoryParentId = null;
    public string $categoryDescription = '';

    // --- Tax Properties ---
    public ?Tax $editingTax;
    public string $taxName = '';
    public string $taxRate = '';
    public bool $taxIsDefault = false;

    protected function rules()
    {
        if ($this->formType === 'category') {
            return ['categoryName' => 'required|string|min:2|max:255'];
        }
        if ($this->formType === 'tax') {
            return [
                'taxName' => 'required|string|min:3|max:255',
                'taxRate' => 'required|numeric|min:0|max:100',
            ];
        }
        return [];
    }

    // ========== CATEGORY METHODS ==========

    public function createCategory()
    {
        $this->resetForm();
        $this->formType = 'category';
        $this->editingCategory = new Category();
        $this->dispatch('open-form');
    }

    public function editCategory(Category $category)
    {
        $this->resetForm();
        $this->formType = 'category';
        $this->editingCategory = $category;
        $this->categoryName = $category->name;
        $this->categoryParentId = $category->parent_id;
        $this->categoryDescription = $category->description;
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
        $this->editingTax = new Tax();
        $this->dispatch('open-form');
    }

    public function editTax(Tax $tax)
    {
        $this->resetForm();
        $this->formType = 'tax';
        $this->editingTax = $tax;
        $this->taxName = $tax->name;
        $this->taxRate = (string)$tax->rate;
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

            DB::transaction(function() use ($data, $companyId) {
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

    public function render()
    {
        $companyId = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)->with('children')->whereNull('parent_id')->orderBy('name')->get();
        $taxes = Tax::where('company_id', $companyId)->orderBy('rate')->get();
        $categoryOptions = Category::where('company_id', $companyId)->pluck('name', 'id');

        return view('livewire.products.product-settings', [
            'categories' => $categories,
            'taxes' => $taxes,
            'categoryOptions' => $categoryOptions,
        ]);
    }
}
