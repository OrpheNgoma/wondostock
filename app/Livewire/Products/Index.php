<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.app')]
#[Title('Liste des Produits - WondoStock')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public bool $showForm = false;

    public ?Product $editingProduct = null;

    protected $listeners = ['productSaved' => 'closeForm'];

    public function create()
    {
        $this->editingProduct = new Product;
        $this->showForm = true;
        // On demande au composant enfant de se charger avec un produit vide
        $this->dispatch('loadProduct', null);
    }

    public function edit(Product $product)
    {
        $this->editingProduct = $product;
        $this->showForm = true;
        // On demande au composant enfant de charger ce produit
        $this->dispatch('loadProduct', $product);
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->editingProduct = null;
    }

    public function delete(Product $product)
    {
        try {
            // Vérifier que le produit appartient à la même entreprise
            if ($product->company_id !== Auth::user()->company_id) {
                $this->dispatch('notify', message: 'Accès non autorisé.', type: 'error');

                return;
            }

            // Vérifier s'il y a des éléments de document liés
            if ($product->documentItems()->exists()) {
                $this->dispatch('notify', message: 'Ce produit ne peut pas être supprimé car il est utilisé dans des documents.', type: 'error');

                return;
            }

            $product->delete();
            $this->dispatch('notify', message: 'Produit supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du produit: '.$e->getMessage());
            $this->dispatch('notify', message: 'Erreur lors de la suppression du produit.', type: 'error');
        }
    }

    public function render()
    {
        $user = Auth::user();

        // Vérification de sécurité
        if (! $user || ! $user->company_id) {
            return view('livewire.products.index', ['products' => collect()->paginate(10)]);
        }

        try {
            $products = Product::where('company_id', $user->company_id)
                ->when($this->search, function ($query) {
                    $query->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                })
                ->with(['category', 'unit', 'stores' => function ($query) {
                    $query->select('stores.id', 'stores.name', 'product_store.quantity', 'product_store.low_stock_threshold');
                }])
                ->latest()
                ->paginate(10);

            return view('livewire.products.index', [
                'products' => $products,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des produits: '.$e->getMessage());

            return view('livewire.products.index', ['products' => collect()->paginate(10)]);
        }
    }
}
