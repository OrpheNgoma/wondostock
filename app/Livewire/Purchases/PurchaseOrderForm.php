<?php

namespace App\Livewire\Purchases;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use App\Models\Product;
use App\Models\Supplier;
use App\Services\DocumentNumberService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Bon de Commande Fournisseur - WondoStock')]
class PurchaseOrderForm extends Component
{
    public Document $document;

    // --- Form Properties ---
    public ?int $supplier_id = null;

    public ?int $store_id = null;

    public $document_date;

    public string $reference = '';

    public string $notes = '';

    public string $delivery_date = '';

    public string $priority = 'normal';

    // --- Line Items ---
    public array $items = [];

    public float $sub_total = 0;

    public float $tax_amount = 0;

    public float $total_amount = 0;

    public int $total_items = 0;

    // --- UI State ---
    public string $supplier_search = '';

    public $suppliers_list = [];

    public string $product_search = '';

    public $products_list = [];

    public bool $isSearchingSuppliers = false;

    public bool $isSearchingProducts = false;

    public bool $isSaving = false;

    public string $current_step = 'basic_info'; // basic_info, products, review

    public bool $showProductModal = false;

    public bool $showConfirmModal = false;

    // --- Selected Product for Modal ---
    public ?Product $selectedProduct = null;

    public int $modalQuantity = 1;

    public float $modalUnitPrice = 0;

    public string $modalDescription = '';

    // --- Supplier Info ---
    // Supprimé - nous utiliserons une propriété computed à la place

    protected function rules()
    {
        $rules = [
            'supplier_id' => 'required|exists:suppliers,id',
            'store_id' => 'required|exists:stores,id',
            'document_date' => 'required|date|after_or_equal:today',
            'delivery_date' => 'nullable|date|after_or_equal:document_date',
            'reference' => 'nullable|string|max:255',
            'notes' => 'nullable|string|max:1000',
            'priority' => 'required|in:low,normal,high,urgent',
        ];

        if ($this->current_step === 'review' || $this->isSaving) {
            $rules['items'] = 'required|array|min:1';
            $rules['items.*.product_id'] = 'required|exists:products,id';
            $rules['items.*.quantity'] = 'required|numeric|min:1';
            $rules['items.*.unit_price'] = 'required|numeric|min:0';
        }

        return $rules;
    }

    public function mount(Document $document)
    {
        $this->document = $document;
        $this->document_date = now()->format('Y-m-d');
        $this->delivery_date = now()->addDays(7)->format('Y-m-d');
        $this->reference = 'BC-'.strtoupper(substr(uniqid(), -6));

        // Pré-remplir le premier magasin par défaut
        $this->store_id = Auth::user()->company->stores()->first()?->id;

        // Si c'est une édition, charger les données existantes
        if ($document->exists) {
            $this->loadDocumentData();
        }

        $this->calculateTotals();
    }

    public function updatedSupplierSearch()
    {
        $this->isSearchingSuppliers = true;

        if (strlen($this->supplier_search) < 2) {
            $this->suppliers_list = [];
            $this->isSearchingSuppliers = false;

            return;
        }

        $this->suppliers_list = Supplier::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->supplier_search.'%')
                    ->orWhere('email', 'like', '%'.$this->supplier_search.'%')
                    ->orWhere('phone_number', 'like', '%'.$this->supplier_search.'%');
            })
            ->limit(8)->get();

        $this->isSearchingSuppliers = false;
    }

    public function selectSupplier(Supplier $supplier)
    {
        $this->supplier_id = $supplier->id;
        $this->supplier_search = $supplier->name;
        $this->suppliers_list = [];

        $this->dispatch('notify', type: 'success', message: 'Fournisseur sélectionné: '.$supplier->name);
    }

    public function updatedProductSearch()
    {
        $this->isSearchingProducts = true;

        if (strlen($this->product_search) < 2) {
            $this->products_list = [];
            $this->isSearchingProducts = false;

            return;
        }

        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where(function ($query) {
                $query->where('name', 'like', '%'.$this->product_search.'%')
                    ->orWhere('sku', 'like', '%'.$this->product_search.'%')
                    ->orWhere('description', 'like', '%'.$this->product_search.'%');
            })
            ->with(['category', 'unit'])
            ->limit(8)->get();

        $this->isSearchingProducts = false;
    }

    // --- Step Navigation ---
    public function nextStep()
    {
        if ($this->current_step === 'basic_info') {
            $this->validate([
                'supplier_id' => 'required|exists:suppliers,id',
                'store_id' => 'required|exists:stores,id',
                'document_date' => 'required|date|after_or_equal:today',
                'priority' => 'required|in:low,normal,high,urgent',
            ]);
            $this->current_step = 'products';
        } elseif ($this->current_step === 'products') {
            if (empty($this->items)) {
                $this->dispatch('notify', type: 'error', message: 'Vous devez ajouter au moins un produit au bon de commande.');

                return;
            }
            $this->current_step = 'review';
        }
    }

    public function previousStep()
    {
        if ($this->current_step === 'products') {
            $this->current_step = 'basic_info';
        } elseif ($this->current_step === 'review') {
            $this->current_step = 'products';
        }
    }

    // --- Product Actions ---
    public function openProductModal(Product $product)
    {
        $this->selectedProduct = $product;
        $this->modalQuantity = 1;
        $this->modalUnitPrice = $product->purchase_price ?? 0;
        $this->modalDescription = $product->name;
        $this->showProductModal = true;
        $this->product_search = '';
        $this->products_list = [];
    }

    public function addProductFromModal()
    {
        if (! $this->selectedProduct) {
            return;
        }

        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $this->selectedProduct->id) {
                $this->items[$key]['quantity'] += $this->modalQuantity;
                $this->calculateTotals();
                $this->showProductModal = false;
                $this->dispatch('notify', type: 'success', message: 'Quantité mise à jour avec succès.');

                return;
            }
        }

        $this->items[] = [
            'product_id' => $this->selectedProduct->id,
            'name' => $this->selectedProduct->name,
            'description' => $this->modalDescription,
            'sku' => $this->selectedProduct->sku,
            'category' => $this->selectedProduct->category?->name ?? 'Sans catégorie',
            'unit' => $this->selectedProduct->unit?->name ?? 'Unité',
            'quantity' => $this->modalQuantity,
            'unit_price' => $this->modalUnitPrice,
            'tax_rate' => 0,
        ];

        $this->calculateTotals();
        $this->showProductModal = false;
        $this->dispatch('notify', type: 'success', message: 'Produit ajouté avec succès.');
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
            $this->dispatch('notify', type: 'success', message: 'Produit retiré du bon de commande.');
        }
    }

    public function updateQuantity($index, $quantity)
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['quantity'] = max(1, (int) $quantity);
            $this->calculateTotals();
        }
    }

    public function updateUnitPrice($index, $price)
    {
        if (isset($this->items[$index])) {
            $this->items[$index]['unit_price'] = max(0, (float) $price);
            $this->calculateTotals();
        }
    }

    public function updatedItems()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
        $this->sub_total = 0;
        $this->tax_amount = 0;
        $this->total_items = 0;

        foreach ($this->items as $item) {
            $lineTotal = $item['quantity'] * $item['unit_price'];
            $this->sub_total += $lineTotal;
            $this->tax_amount += $lineTotal * (($item['tax_rate'] ?? 0) / 100);
            $this->total_items += $item['quantity'];
        }

        $this->total_amount = $this->sub_total + $this->tax_amount;
    }

    private function loadDocumentData()
    {
        $this->supplier_id = $this->document->supplier_id;
        $this->store_id = $this->document->store_id;
        $this->document_date = $this->document->document_date->format('Y-m-d');
        $this->notes = $this->document->notes ?? '';
        $this->reference = $this->document->document_number;

        if ($this->document->supplier) {
            $this->supplier_id = $this->document->supplier->id;
            $this->supplier_search = $this->document->supplier->name;
        }

        // Charger les items
        $this->items = $this->document->items->map(function ($item) {
            return [
                'product_id' => $item->product_id,
                'name' => $item->description,
                'description' => $item->description,
                'sku' => $item->product?->sku ?? '',
                'quantity' => $item->quantity,
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate ?? 0,
            ];
        })->toArray();
    }

    public function confirmSave()
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
                    'notes' => $this->notes,
                    'status' => DocumentStatus::Draft,
                ]);

                if (! $this->document->exists) {
                    $this->document->document_number = DocumentNumberService::generate(Auth::user()->company_id, 'purchase_order');
                } else {
                    $this->document->document_number = $this->reference;
                }

                $this->document->save();

                $this->document->items()->delete();

                foreach ($this->items as $item) {
                    $item['total_amount'] = $item['quantity'] * $item['unit_price'];
                    $this->document->items()->create($item);
                }
            });

            $this->dispatch('notify', type: 'success', message: 'Bon de commande sauvegardé avec succès.');

            return $this->redirectRoute('purchases.index');

        } catch (\Exception $e) {
            $this->dispatch('notify', type: 'error', message: 'Erreur lors de la sauvegarde du bon de commande.');
        } finally {
            $this->isSaving = false;
            $this->showConfirmModal = false;
        }
    }

    public function getStoresProperty()
    {
        return Auth::user()->company->stores;
    }

    public function getPrioritiesProperty()
    {
        return [
            'low' => 'Faible',
            'normal' => 'Normale',
            'high' => 'Élevée',
            'urgent' => 'Urgente',
        ];
    }

    public function getSelectedSupplierProperty()
    {
        return $this->supplier_id ? Supplier::with('company')->find($this->supplier_id) : null;
    }

    public function render()
    {
        return view('livewire.saas.purchases.purchase-order-form', [
            'stores' => $this->stores,
            'priorities' => $this->priorities,
        ]);
    }
}
