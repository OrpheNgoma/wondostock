<?php

namespace App\Livewire\Stock;

use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Entrée de Stock - KaziFlow')]
class StockEntry extends Component
{
    // --- Form Properties ---
    public ?int $store_id = null;

    public $entry_date;

    public $notes = '';

    public $type = 'purchase'; // 'purchase', 'adjustment', 'transfer_in', 'return', 'production'

    // --- Line Items ---
    public array $items = [];

    // --- Helpers ---
    public string $product_search = '';

    public $products_list = [];

    protected function rules()
    {
        $companyId = Auth::user()->company_id;

        return [
            'store_id' => [
                'required',
                'exists:stores,id,company_id,'.$companyId,
            ],
            'entry_date' => [
                'required',
                'date',
                'before_or_equal:today',
            ],
            'items' => 'required|array|min:1|max:50', // Limite à 50 produits par entrée
            'items.*.product_id' => [
                'required',
                'exists:products,id,company_id,'.$companyId,
            ],
            'items.*.quantity' => [
                'required',
                'numeric',
                'min:1',
                'max:99999', // Limite raisonnable
            ],
            'notes' => 'nullable|string|max:500',
            'type' => 'required|in:purchase,adjustment,transfer_in,return,production',
        ];
    }

    protected $messages = [
        'store_id.required' => 'Veuillez sélectionner un magasin.',
        'entry_date.required' => 'La date d\'entrée est obligatoire.',
        'entry_date.before_or_equal' => 'La date d\'entrée ne peut pas être dans le futur.',
        'items.required' => 'Veuillez ajouter au moins un produit.',
        'items.min' => 'Veuillez ajouter au moins un produit.',
        'items.max' => 'Maximum 50 produits par entrée de stock.',
        'items.*.quantity.min' => 'La quantité doit être au moins de 1.',
        'items.*.quantity.max' => 'Quantité maximale autorisée : 99,999.',
        'notes.max' => 'Les notes ne peuvent pas dépasser 500 caractères.',
    ];

    public function mount()
    {
        $this->entry_date = now()->format('Y-m-d');
        // Pré-remplir le premier magasin par défaut
        $this->store_id = Auth::user()->company->stores()->first()?->id;
    }

    public function updated($propertyName)
    {
        // S'assurer que les quantités sont toujours des entiers
        if (strpos($propertyName, 'items.') === 0 && strpos($propertyName, '.quantity') !== false) {
            $index = explode('.', $propertyName)[1];
            if (isset($this->items[$index]['quantity'])) {
                $this->items[$index]['quantity'] = max(1, (int) $this->items[$index]['quantity']);
            }
        }
    }

    // --- Real-time Search ---
    public function updatedProductSearch()
    {
        if (strlen($this->product_search) < 2) {
            $this->products_list = [];

            return;
        }

        $this->products_list = Product::with(['category'])
            ->where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->product_search.'%')
                    ->orWhere('sku', 'like', '%'.$this->product_search.'%');
            })
            ->limit(10)
            ->get()
            ->map(function ($product) {
                // Ajouter les informations de stock pour le magasin sélectionné
                if ($this->store_id) {
                    $product->stock_quantity = DB::table('product_store')
                        ->where('product_id', $product->id)
                        ->where('store_id', $this->store_id)
                        ->value('quantity') ?? 0;
                }

                return $product;
            });
    }

    // --- Actions ---
    public function addProduct(Product $product)
    {
        // Vérification de sécurité multi-tenant
        if ($product->company_id !== Auth::user()->company_id) {
            $this->dispatch('notify', [
                'message' => 'Produit non autorisé.',
                'type' => 'error',
            ]);

            return;
        }

        $this->product_search = '';
        $this->products_list = [];

        // Vérifier si déjà dans la liste
        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $product->id) {
                $this->items[$key]['quantity'] = (int) $this->items[$key]['quantity'] + 1;
                $this->dispatch('notify', [
                    'message' => "Quantité mise à jour pour {$product->name}",
                    'type' => 'success',
                ]);

                return;
            }
        }

        // Vérifier la limite de produits
        if (count($this->items) >= 50) {
            $this->dispatch('notify', [
                'message' => 'Maximum 50 produits par entrée de stock.',
                'type' => 'error',
            ]);

            return;
        }

        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
        ];

        $this->dispatch('notify', [
            'message' => "Produit {$product->name} ajouté avec succès",
            'type' => 'success',
        ]);
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
    }

    public function incrementQuantity($index)
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['quantity'] = (int) $this->items[$index]['quantity'] + 1;
        }
    }

    public function decrementQuantity($index)
    {
        if (isset($this->items[$index])) {
            $currentQuantity = (int) $this->items[$index]['quantity'];
            if ($currentQuantity > 1) {
                $this->items[$index]['quantity'] = $currentQuantity - 1;
            }
        }
    }

    public function getCurrentStock($productId)
    {
        if (! $this->store_id) {
            return 0;
        }

        return DB::table('product_store')
            ->where('product_id', $productId)
            ->where('store_id', $this->store_id)
            ->value('quantity') ?? 0;
    }

    public function save()
    {
        $this->validate();

        DB::transaction(function () {
            $store = Store::find($this->store_id);

            foreach ($this->items as $item) {
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
                        'quantity' => $item['quantity'],
                    ]);
                }

                // 2. Enregistrer le mouvement de stock pour la traçabilité
                $previousStock = $existing ? $existing->quantity : 0;
                $newStock = $previousStock + $item['quantity'];

                $store->stockMovements()->create([
                    'company_id' => Auth::user()->company_id,
                    'product_id' => $item['product_id'],
                    'user_id' => Auth::id(),
                    'type' => match ($this->type) {
                        'purchase' => StockMovementType::Purchase,
                        'adjustment' => StockMovementType::Adjustment,
                        'transfer_in' => StockMovementType::TransferIn,
                        'return' => StockMovementType::Return,
                        'production' => StockMovementType::Production,
                        default => StockMovementType::Purchase
                    },
                    'quantity' => $item['quantity'], // Positif car c'est une entrée
                    'source_type' => 'App\\Models\\User', // Type de source = utilisateur qui fait l'entrée
                    'source_id' => Auth::id(), // ID de l'utilisateur comme source
                ]);
            }
        });

        session()->flash('notify', [
            'message' => 'Entrée de stock enregistrée avec succès ! Le stock a été mis à jour.',
            'type' => 'success',
        ]);
        $this->redirectRoute('dashboard');
    }

    public function render()
    {
        $stores = Auth::user()->company->stores;

        return view('livewire.saas.stock.stock-entry', [
            'stores' => $stores,
        ]);
    }
}
