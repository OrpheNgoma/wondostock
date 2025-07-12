<?php

namespace App\Livewire\Stock;

use App\Models\Store;
use App\Models\Product;
use Livewire\Component;
use App\Models\StockMovement;
use App\Models\StockTransfer;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Enums\StockMovementType;
use App\Enums\StockTransferStatus;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

#[Layout('components.layouts.app')]
#[Title('Transfert de Stock - KaziFlow')]
class TransferForm extends Component
{
    // --- Form Properties ---
    public ?int $from_store_id = null;
    public ?int $to_store_id = null;
    public $transfer_date;
    public $notes = '';

    // --- Line Items ---
    public array $items = [];

    // --- Helpers ---
    public string $product_search = '';
    public $products_list = [];

    protected function rules()
    {
        return [
            'from_store_id' => 'required|exists:stores,id',
            'to_store_id' => 'required|exists:stores,id|different:from_store_id',
            'transfer_date' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
        ];
    }
    
    protected $messages = [
        'to_store_id.different' => 'Le magasin de destination doit être différent du magasin d\'origine.'
    ];

    public function mount()
    {
        $this->transfer_date = now()->format('Y-m-d');
        $stores = Auth::user()->company->stores;
        $this->from_store_id = $stores->first()?->id;
        $this->to_store_id = $stores->skip(1)->first()?->id;
    }

    // --- Real-time Search ---
    public function updatedProductSearch()
    {
        if (strlen($this->product_search) < 2 || !$this->from_store_id) {
            $this->products_list = [];
            return;
        }
        // On ne cherche que les produits qui ont du stock dans le magasin d'origine
        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where(function($query) {
                $query->where('name', 'like', '%' . $this->product_search . '%')
                      ->orWhere('sku', 'like', '%' . $this->product_search . '%');
            })
            ->whereHas('stores', function($query) {
                $query->where('stores.id', $this->from_store_id)->where('quantity', '>', 0);
            })
            ->with(['stores' => function($query) {
                $query->where('stores.id', $this->from_store_id);
            }])
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
        
        $stockInStore = $product->stores->first()->pivot->quantity;
        
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku,
            'quantity' => 1,
            'max_quantity' => $stockInStore, // Pour la validation
        ];
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save()
    {
        $this->validate();

        try {
            DB::transaction(function() {
                // 1. Valider le stock avant de continuer
                foreach ($this->items as $item) {
                    $stock = DB::table('product_store')
                        ->where('product_id', $item['product_id'])
                        ->where('store_id', $this->from_store_id)
                        ->value('quantity');
                    if ($stock < $item['quantity']) {
                        throw ValidationException::withMessages([
                            'items' => "Stock insuffisant pour le produit {$item['name']} dans le magasin d'origine."
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
                    'notes' => $this->notes,
                    'status' => StockTransferStatus::Completed,
                ]);

                // 3. Mettre à jour les stocks et créer les mouvements
                foreach($this->items as $item) {
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
                            'quantity' => $item['quantity']
                        ]);
                    }
                    StockMovement::create(['company_id' => Auth::user()->company_id, 'product_id' => $item['product_id'], 'store_id' => $this->to_store_id, 'user_id' => Auth::id(), 'type' => StockMovementType::TransferIn, 'quantity' => $item['quantity'], 'source_id' => $transfer->id, 'source_type' => StockTransfer::class]);
                }
            });
        } catch (ValidationException $e) {
            // Attrape l'exception de stock insuffisant
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
            return;
        }

        session()->flash('notify', ['message' => 'Transfert de stock enregistré avec succès.', 'type' => 'success']);
        $this->redirectRoute('stock.movements.index');
    }

    public function render()
    {
        $stores = Auth::user()->company->stores;
        return view('livewire.stock.transfer-form', [
            'stores' => $stores
        ]);
    }
}