<?php
namespace App\Livewire\Documents;

use App\Models\Store;
use App\Models\Product;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Services\DocumentNumberService;

#[Layout('components.layouts.app')]
#[Title('Nouveau Document - KaziFlow')]
class DocumentForm extends Component
{
    public Document $document;

    // --- Form Properties ---
    public $type = 'quote';
    public ?int $customer_id = null;
    public ?int $store_id = null;
    public $document_date;
    public $due_date;
    public $notes = '';
    
    // --- Line Items & Totals ---
    public array $items = [];
    public float $sub_total = 0;
    public float $tax_amount = 0;
    public float $total_amount = 0;

    // --- Helpers ---
    public string $customer_search = '';
    public $customers_list = [];
    public string $product_search = '';
    public $products_list = [];

    protected function rules()
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'store_id' => 'required|exists:stores,id',
            'document_date' => 'required|date',
            'due_date' => 'nullable|date|after_or_equal:document_date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|numeric|min:1',
            'items.*.unit_price' => 'required|numeric|min:0',
        ];
    }

    public function mount(Document $document)
    {
        $this->document = $document;

        // Si on édite un document existant, on charge ses données
        if ($this->document->exists) {
            $this->fill($this->document->toArray());
            $this->customer_search = $this->document->customer->name;
            $this->document_date = $this->document->document_date->format('Y-m-d');
            if ($this->document->due_date) {
                $this->due_date = $this->document->due_date->format('Y-m-d');
            }
            
            // On charge les lignes d'articles
            foreach ($this->document->items as $item) {
                $this->items[] = [
                    'product_id' => $item->product_id,
                    'name' => $item->product->name,
                    'description' => $item->description,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'tax_rate' => $item->tax_rate,
                ];
            }
        } else {
            // Sinon, c'est un nouveau document
            $this->document_date = now()->format('Y-m-d');
            $this->due_date = now()->addDays(30)->format('Y-m-d'); // Échéance par défaut à 30 jours
            // Pré-remplir le premier magasin par défaut
            $this->store_id = Auth::user()->company->stores()->first()?->id;
        }
    }

    // --- Real-time Search ---

    public function updatedCustomerSearch()
    {
        if (strlen($this->customer_search) < 2) {
            $this->customers_list = [];
            return;
        }
        $this->customers_list = Customer::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $this->customer_search . '%')
            ->limit(5)->get();
    }

    public function updatedProductSearch()
    {
        if (strlen($this->product_search) < 2) {
            $this->products_list = [];
            return;
        }
        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $this->product_search . '%')
            ->limit(5)->get();
    }

    // --- Actions ---

    public function selectCustomer(Customer $customer)
    {
        $this->customer_id = $customer->id;
        $this->customer_search = $customer->name;
        $this->customers_list = [];
    }

    public function addProduct(Product $product)
    {
        $this->product_search = '';
        $this->products_list = [];

        // Vérifier si le produit est déjà dans le tableau
        foreach ($this->items as $key => $item) {
            if ($item['product_id'] === $product->id) {
                $this->items[$key]['quantity']++;
                $this->calculateTotals();
                return;
            }
        }
        
        $this->items[] = [
            'product_id' => $product->id,
            'name' => $product->name, // Le nom est utilisé pour l'affichage
            'description' => $product->name, // On utilise le nom du produit comme description de la ligne
            'quantity' => 1,
            'unit_price' => $product->selling_price,
            'tax_rate' => $product->tax?->rate ?? 0,
        ];
        $this->calculateTotals();
    }

    public function removeItem($index)
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items); // Re-index array
        $this->calculateTotals();
    }
    
    public function updatedItems()
    {
        $this->calculateTotals();
    }

    public function calculateTotals()
    {
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
                'customer_id' => $this->customer_id,
                'store_id' => $this->store_id,
                'user_id' => Auth::id(),
                'type' => $this->type,
                'document_date' => $this->document_date,
                'due_date' => $this->due_date,
                'notes' => $this->notes,
                'sub_total' => $this->sub_total,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                'document_number' => 'INV-'.now()->timestamp, // A améliorer
                'status' => DocumentStatus::Draft,
            ]);

            // Si c'est un nouveau document, on génère un numéro
            if (!$this->document->exists) {
                // On utilise le service pour générer le numéro
                $this->document->document_number = DocumentNumberService::generate(Auth::user()->company_id, $this->type);
            }

            $this->document->save();
            
            $this->document->items()->delete(); // A améliorer pour l'édition
            
            foreach($this->items as $item) {
                // On s'assure que le tableau passé à create() contient bien les champs
                // attendus par la table `document_items`, notamment `description`.
                $this->document->items()->create([
                    'product_id' => $item['product_id'],
                    'description' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'tax_rate' => $item['tax_rate'],
                    'total_amount' => $item['quantity'] * $item['unit_price'],
                ]);
            }
        });

        $this->dispatch('notify', message: 'Document sauvegardé avec succès.');
        // Rediriger vers la page de détails du document (à créer)
        $this->redirectRoute('documents.show', $this->document);
    }

    public function render()
    {
        $stores = Auth::user()->company->stores;
        return view('livewire.documents.document-form', [
            'stores' => $stores,
            'documentTypes' => DocumentType::cases(), // On passe les types de documents à la vue
        ]);
    }
}