<?php

namespace App\Livewire\Products;

use App\Models\Product;
use Livewire\Component;
use Milon\Barcode\DNS1D;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Imprimer des Étiquettes - WondoStock')]
class PrintLabels extends Component
{
    public string $search = '';
    public $searchResults = [];
    public array $productsToPrint = [];

    public function updatedSearch()
    {
        if (strlen($this->search) < 2) {
            $this->searchResults = [];
            return;
        }
        $this->searchResults = Product::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('sku', 'like', '%' . $this->search . '%');
            })
            ->limit(10)->get();
    }

    public function addProduct(Product $product)
    {
        // Vérifier que le produit appartient à la bonne entreprise
        if ($product->company_id !== Auth::user()->company_id) {
            $this->dispatch('notify', message: 'Produit non autorisé.', type: 'error');
            return;
        }

        // Vérifier si le produit n'est pas déjà dans la liste
        if (!collect($this->productsToPrint)->pluck('id')->contains($product->id)) {
            // Valider que le SKU est valide pour un code-barres
            if (empty($product->sku) || strlen($product->sku) < 3) {
                $this->dispatch('notify', message: 'Le SKU du produit est trop court pour générer un code-barres.', type: 'error');
                return;
            }

            $this->productsToPrint[] = [
                'id' => $product->id,
                'name' => $product->name,
                'sku' => $product->sku,
                'price' => $product->selling_price,
                'quantity' => 1, // Quantité d'étiquettes à imprimer
            ];
            
            $this->dispatch('notify', message: 'Produit ajouté à la liste d\'impression.', type: 'success');
        } else {
            $this->dispatch('notify', message: 'Ce produit est déjà dans la liste.', type: 'warning');
        }
        
        $this->search = '';
        $this->searchResults = [];
    }

    public function removeProduct($index)
    {
        unset($this->productsToPrint[$index]);
        $this->productsToPrint = array_values($this->productsToPrint);
        $this->dispatch('notify', message: 'Produit retiré de la liste.', type: 'info');
    }

    public function clearAll()
    {
        $this->productsToPrint = [];
        $this->dispatch('notify', message: 'Liste vidée.', type: 'info');
    }

    public function updateQuantity($index, $quantity)
    {
        if ($quantity > 0 && $quantity <= 100) { // Limite raisonnable
            $this->productsToPrint[$index]['quantity'] = (int) $quantity;
        }
    }

    public function getTotalLabels()
    {
        return collect($this->productsToPrint)->sum('quantity');
    }

    public function render()
    {
        return view('livewire.products.print-labels');
    }
}