<?php

namespace App\Livewire\Stock;

use App\Models\Store;
use App\Models\Product;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Entrée de Stock - KaziFlow')]
class StockEntry extends Component
{
    // --- Form Properties ---
    public ?int $store_id = null;
    public $entry_date;
    public $notes = '';
    public $type = 'purchase'; // 'purchase' or 'adjustment'

    // --- Line Items ---
    public array $items = [];

    // --- Helpers ---
    public string $product_search = '';
    public $products_list = [];

    protected function rules()
    {
        return [
            'store_id' => 'required|exists:stores,id',
            'entry_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ];
    }

    public function mount()
    {
        $this->entry_date = now()->format('Y-m-d');
        // Pré-remplir le premier magasin par défaut
        $this->store_id = Auth::user()->company->stores()->first()?->id;
    }

    // --- Real-time Search ---
    public function updatedProductSearch()
    {
        if (strlen($this->product_search) < 2) {
            $this->products_list = [];
            return;
        }
        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->product_search . '%')
                      ->orWhere('sku', 'like', '%' . $this->product_search . '%');
            })
            ->limit(5)->get();
    }

    // --- Actions ---
    public function addProduct(Product $product)
    {
        $this->product_search = '';
        $this->products_list = [];

        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $product->id) {
                $this->items[$key]['quantity']++;
                return;
            }
        }
        
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function() {
            $store = Store::find($this->store_id);

            foreach($this->items as $item) {
                // 1. Mettre à jour (ou insérer) le stock dans le magasin
                $existing = DB::table('product_store')
                    ->where('product_id', $item['product_id'])
                    ->where('store_id', $this->store_id)
                    ->first();
                
                if ($existing) {
                    DB::table('product_store')
                        ->where('product_id', $item['product_id'])
                        ->where('store_id', $this->store_id)
                        ->update(['quantity' => $existing->quantity + $item['quantity']]);
                } else {
                    DB::table('product_store')->insert([
                        'product_id' => $item['product_id'],
                        'store_id' => $this->store_id,
                        'quantity' => $item['quantity']
                    ]);
                }

                // 2. Enregistrer le mouvement de stock pour la traçabilité
                $store->stockMovements()->create([
                    'company_id' => Auth::user()->company_id,
                    'product_id' => $item['product_id'],
                    'user_id' => Auth::id(),
                    'type' => $this->type === 'purchase' ? StockMovementType::Purchase : StockMovementType::Adjustment,
                    'quantity' => $item['quantity'], // Positif car c'est une entrée
                    // 'notes' => $this->notes,
                ]);
            }
        });

        session()->flash('notify', [
            'message' => 'Stock mis à jour avec succès.',
            'type' => 'success'
        ]);
        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        $stores = Auth::user()->company->stores;
        return view('livewire.stock.stock-entry', [
            'stores' => $stores
        ]);
    }
}