<?php

namespace App\Livewire\Stock;

use App\Enums\StockMovementType;
use App\Enums\StockTransferStatus;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Transfert de Stock - WondoStock')]
class TransferForm extends Component
{
    // --- Form Properties ---
    public ?int $from_store_id = null;
    public ?int $to_store_id = null;
    public $transfer_date;
    public $notes = '';
    public string $reference = '';
    public string $transfer_reason = 'Réapprovisionnement';

    // --- Line Items ---
    public array $items = [];
    public float $total_items = 0;
    public float $estimated_value = 0;

    // --- UI State ---
    public string $product_search = '';
    public $products_list = [];
    public bool $isSearching = false;
    public bool $isSaving = false;
    public string $current_step = 'transfer_info'; // transfer_info, products, review
    public bool $showProductModal = false;
    public bool $showConfirmModal = false;

    // --- Selected Product for Modal ---
    public ?Product $selectedProduct = null;
    public int $modalQuantity = 1;

    protected function rules()
    {
        $rules = [
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'transfer_date' => 'required|date|before_or_equal:today',
            'reference' => 'nullable|string|max:255',
            'transfer_reason' => 'required|string|max:255',
            'notes' => 'nullable|string|max:1000',
        ];

        if ($this->current_step === 'review' || $this->isSaving) {
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.product_id'] = 'required|exists:products,id';
            $rules['items.*.quantity'] = 'required|numeric|min:1';
        }

        return $rules;
    }

    protected $messages = [
        'to_store_id.different' => 'Le magasin de destination doit être différent du magasin d\'origine.',
        'transfer_date.before_or_equal' => 'La date de transfert ne peut pas être dans le futur.',
        'items.required' => 'Vous devez ajouter au moins un produit au transfert.',
        'items.min' => 'Vous devez ajouter au moins un produit au transfert.',
    ];

    public function mount()
    {
        $this->transfer_date = now()->format('Y-m-d');
        $this->reference = 'TRF-' . strtoupper(substr(uniqid(), -6));
        $stores = Auth::user()->company->stores;
        $this->from_store_id = $stores->first()?->id;
        $this->to_store_id = $stores->skip(1)->first()?->id;
        $this->calculateTotals();
    }

    // --- Real-time Search ---
    public function updatedProductSearch()
    {
        $this->isSearching = true;
        
        if (strlen($this->product_search) < 2 || ! $this->from_store_id) {
            $this->products_list = [];
            $this->isSearching = false;
            return;
        }
        
        // On ne cherche que les produits qui ont du stock dans le magasin d'origine
        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->product_search.'%')
                    ->orWhere('sku', 'like', '%'.$this->product_search.'%')
                    ->orWhere('description', 'like', '%'.$this->product_search.'%');
            })
            ->whereHas('stores', function ($query) {
                $query->where('stores.id', $this->from_store_id)->where('quantity', '>', 0);
            })
            ->with(['stores' => function ($query) {
                $query->where('stores.id', $this->from_store_id);
            }, 'category', 'unit'])
            ->limit(8)->get();
            
        $this->isSearching = false;
    }

    // --- Step Navigation ---
    public function nextStep()
    {
        if ($this->current_step === 'transfer_info') {
            $this->validate([
                'from_store_id' => 'required|exists:stores,id',
                'to_store_id' => 'required|exists:stores,id|different:from_store_id',
                'transfer_date' => 'required|date|before_or_equal:today',
                'transfer_reason' => 'required|string|max:255',
            ]);
            $this->current_step = 'products';
        } elseif ($this->current_step === 'products') {
            if (empty($this->items)) {
                $this->dispatch('notify', [
                    'type' => 'error',
                    'message' => 'Vous devez ajouter au moins un produit au transfert.'
                ]);
                return;
            }
            $this->current_step = 'review';
        }
    }

    public function previousStep()
    {
        if ($this->current_step === 'products') {
            $this->current_step = 'transfer_info';
        } elseif ($this->current_step === 'review') {
            $this->current_step = 'products';
        }
    }

    // --- Product Actions ---
    public function openProductModal(Product $product)
    {
        $this->selectedProduct = $product;
        $this->modalQuantity = 1;
        $this->showProductModal = true;
        $this->product_search = '';
        $this->products_list = [];
    }

    public function addProductFromModal()
    {
        if (!$this->selectedProduct) return;

        $stockInStore = $this->selectedProduct->stores->first()->pivot->quantity;
        
        if ($this->modalQuantity > $stockInStore) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => 'Quantité demandée supérieure au stock disponible.'
            ]);
            return;
        }

        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $this->selectedProduct->id) {
                $this->items[$key]['quantity'] += $this->modalQuantity;
                $this->calculateTotals();
                $this->showProductModal = false;
                $this->dispatch('notify', [
                    'type' => 'success',
                    'message' => 'Quantité mise à jour avec succès.'
                ]);
                return;
            }
        }

        $this->items[] = [
            'product_id' => $this->selectedProduct->id,
            'name' => $this->selectedProduct->name,
            'sku' => $this->selectedProduct->sku,
            'category' => $this->selectedProduct->category?->name ?? 'Sans catégorie',
            'unit' => $this->selectedProduct->unit?->name ?? 'Unité',
            'price' => $this->selectedProduct->selling_price ?? 0,
            'quantity' => $this->modalQuantity,
            'max_quantity' => $stockInStore,
        ];
        
        $this->calculateTotals();
        $this->showProductModal = false;
        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Produit ajouté avec succès.'
        ]);
    }

    public function addProduct(Product $product)
    {
        $this->openProductModal($product);
    }

    public function removeItem($index)
    {
        if (isset($this->items[$index])) {
            unset($this->items[$index]);
            $this->items = array_values($this->items);
            $this->calculateTotals();
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Produit retiré du transfert.'
            ]);
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if (isset($this->items[$index])) {
            $quantity = max(1, (int) $quantity);
            $maxQuantity = $this->items[$index]['max_quantity'];
            
            if ($quantity > $maxQuantity) {
                $this->items[$index]['quantity'] = $maxQuantity;
                $this->dispatch('notify', [
                    'type' => 'warning',
                    'message' => "Quantité limitée au stock disponible ({$maxQuantity})."
                ]);
            } else {
                $this->items[$index]['quantity'] = $quantity;
            }
            
            $this->calculateTotals();
        }
    }

    public function calculateTotals()
    {
        $this->total_items = array_sum(array_column($this->items, 'quantity'));
        $this->estimated_value = array_sum(array_map(function($item) {
            return ($item['price'] ?? 0) * $item['quantity'];
        }, $this->items));
    }

    public function updatedItems()
    {
        $this->calculateTotals();
    }

    public function confirmTransfer()
    {
        $this->showConfirmModal = true;
    }

    public function save()
    {
        $this->isSaving = true;
        $this->current_step = 'review'; // Force validation rules
        $this->validate();

        try {
            DB::transaction(function () {
                // 1. Valider le stock avant de continuer
                foreach ($this->items as $item) {
                    $stock = DB::table('product_store')
                        ->where('product_id', $item['product_id'])
                        ->where('store_id', $this->from_store_id)
                        ->value('quantity');
                    if ($stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Stock insuffisant pour le produit {$item['name']} dans le magasin d'origine.",
                        ]);
                    }
                }

                // 2. Créer l'enregistrement du transfert
                $transfer = StockTransfer::create([
                    'company_id' => Auth::user()->company_id,
                    'from_store_id' => $this->from_store_id,
                    'to_store_id' => $this->to_store_id,
                    'user_id' => Auth::id(),
                    'transfer_date' => $this->transfer_date,
                    'reference' => $this->reference,
                    'notes' => $this->notes,
                    'transfer_reason' => $this->transfer_reason,
                    'status' => StockTransferStatus::Completed,
                ]);

                // 3. Mettre à jour les stocks et créer les mouvements
                foreach ($this->items as $item) {
                    $transfer->items()->create($item);

                    // Sortie du magasin d'origine
                    DB::table('product_store')->where('product_id', $item['product_id'])->where('store_id', $this->from_store_id)->decrement('quantity', $item['quantity']);
                    StockMovement::create(['company_id' => Auth::user()->company_id, 'product_id' => $item['product_id'], 'store_id' => $this->from_store_id, 'user_id' => Auth::id(), 'type' => StockMovementType::TransferOut, 'quantity' => -$item['quantity'], 'source_id' => $transfer->id, 'source_type' => StockTransfer::class]);

                    // Entrée dans le magasin de destination
                    $existing = DB::table('product_store')
                        ->where('product_id', $item['product_id'])
                        ->where('store_id', $this->to_store_id)
                        ->first();

                    if ($existing) {
                        DB::table('product_store')
                            ->where('product_id', $item['product_id'])
                            ->where('store_id', $this->to_store_id)
                            ->update(['quantity' => $existing->quantity + $item['quantity']]);
                    } else {
                        DB::table('product_store')->insert([
                            'product_id' => $item['product_id'],
                            'store_id' => $this->to_store_id,
                            'quantity' => $item['quantity'],
                        ]);
                    }
                    StockMovement::create(['company_id' => Auth::user()->company_id, 'product_id' => $item['product_id'], 'store_id' => $this->to_store_id, 'user_id' => Auth::id(), 'type' => StockMovementType::TransferIn, 'quantity' => $item['quantity'], 'source_id' => $transfer->id, 'source_type' => StockTransfer::class]);
                }
            });
        } catch (ValidationException $e) {
            // Attrape l'exception de stock insuffisant
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => $e->getMessage()
            ]);
            return;
        } finally {
            $this->isSaving = false;
            $this->showConfirmModal = false;
        }

        $this->dispatch('notify', [
            'type' => 'success',
            'message' => 'Transfert de stock enregistré avec succès.'
        ]);
        
        return $this->redirectRoute('stock.movements.index');
    }

    public function getStoresProperty()
    {
        return Auth::user()->company->stores;
    }

    public function getFromStoreProperty()
    {
        return $this->stores->find($this->from_store_id);
    }

    public function getToStoreProperty()
    {
        return $this->stores->find($this->to_store_id);
    }

    public function getTransferReasonsProperty()
    {
        return [
            'Réapprovisionnement' => 'Réapprovisionnement',
            'Réorganisation' => 'Réorganisation',
            'Retour produit' => 'Retour produit',
            'Équilibrage stock' => 'Équilibrage stock',
            'Promotion' => 'Promotion',
            'Autre' => 'Autre'
        ];
    }

    public function render()
    {
        return view('livewire.saas.stock.transfer-form', [
            'stores' => $this->stores,
            'transferReasons' => $this->transferReasons,
        ]);
    }
}
