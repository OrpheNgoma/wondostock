<?php

namespace App\Livewire\Purchases;

use App\Models\Product;
use Livewire\Component;
use App\Models\Document;
use App\Models\Supplier;
use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\DocumentNumberService;

#[Layout('components.layouts.app')]
#[Title('Bon de Commande Fournisseur - WondoStock')]
class PurchaseOrderForm extends Component
{
    public Document $document;
    public ?int $supplier_id = null;
    public ?int $store_id = null;
    public $document_date;
    public array $items = [];
    public float $sub_total = 0;
    public float $tax_amount = 0;
    public float $total_amount = 0;

    public string $supplier_search = '';
    public $suppliers_list = [];
    public string $product_search = '';
    public $products_list = [];

    protected function rules() {
        return [
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'document_date' => 'required|date',
            'items' => 'required|array|min:1',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->document_date = now()->format('Y-m-d');
        // ... logique de pré-remplissage pour l'édition
        // Pré-remplir le premier magasin par défaut
        $this->store_id = Auth::user()->company->stores()->first()?->id;
    }
    
    public function updatedSupplierSearch() {
        if (strlen($this->supplier_search) < 2) {
            $this->suppliers_list = [];
            return;
        }
        $this->suppliers_list = Supplier::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $this->supplier_search . '%')
            ->limit(5)->get();
    }

    public function selectSupplier(Supplier $supplier) {
        $this->supplier_id = $supplier->id;
        $this->supplier_search = $supplier->name;
        $this->suppliers_list = [];
    }

    public function updatedProductSearch() {
        if (strlen($this->product_search) < 2) {
            $this->products_list = [];
            return;
        }
        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $this->product_search . '%')
            ->limit(5)->get();
    }

    public function addProduct(Product $product) {
        $this->product_search = '';
        $this->products_list = [];
        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $product->id) {
                $this->items[$key]['quantity']++;
                $this->calculateTotal();
                return;
            }
        }
        $this->items[] = [
            'product_id' => $product->id, 'name' => $product->name, 'description' => $product->name,
            'quantity' => 1, 'unit_price' => $product->purchase_price ?? 0, 'tax_rate' => 0,
        ];
        $this->calculateTotal();
    }

    public function removeItem($index) {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
        $this->calculateTotal();
    }

    public function updatedItems() {
        $this->calculateTotal();
    }

    public function calculateTotal() {
        $this->sub_total = 0;
        $this->tax_amount = 0;
        foreach ($this->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $this->sub_total += $lineTotal;
            $this->tax_amount += $lineTotal * ($item['tax_rate'] / 100);
        }
        $this->total_amount = $this->sub_total + $this->tax_amount;
    }

    public function save()
    {
        $this->validate();
        DB::transaction(function() {
            $this->document->fill([
                'company_id' => Auth::user()->company_id,
                'supplier_id' => $this->supplier_id,
                'store_id' => $this->store_id,
                'user_id' => Auth::id(),
                'type' => DocumentType::PurchaseOrder,
                'document_date' => $this->document_date,
                'sub_total' => $this->sub_total,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                'document_number' => 'BC-' . now()->timestamp,
                'status' => DocumentStatus::Draft,
            ]);

            if (!$this->document->exists) {
                // On utilise le service pour générer le numéro
                $this->document->document_number = DocumentNumberService::generate(Auth::user()->company_id, 'purchase_order');
            }
            $this->document->save();

            $this->document->items()->delete();
            
            foreach($this->items as $item) {
                $item['total_amount'] = $item['quantity'] * $item['unit_price'];
                $this->document->items()->create($item);
            }
        });
        $this->dispatch('notify', message: 'Bon de commande sauvegardé.');
        $this->redirectRoute('purchases.index');
    }

    public function render()
    {
         $stores = Auth::user()->company->stores;
        return view('livewire.purchases.purchase-order-form', [
            'stores' => $stores
            // 'stores' => $stores = Auth::user()->company->stores(),
            // 'document' => $this->document,
            // 'items' => $this->items,
            ]);
    }
}