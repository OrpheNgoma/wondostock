<?php

namespace App\Livewire\Deliveries;

use App\Enums\DeliveryTripStatus;
use App\Models\Customer;
use App\Models\DeliveryExpense;
use App\Models\DeliveryExpenseCategory;
use App\Models\DeliveryTrip;
use App\Models\Product;
use App\Services\DeliveryService;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Détail tournée - WondoStock')]
class TripShow extends Component
{
    use AuthorizesLivewireActions;

    public DeliveryTrip $trip;

    // ── Chargement ────────────────────────────────────────────────────────────
    public int $loaded_crates = 0;

    // ── Retour ────────────────────────────────────────────────────────────────
    public int $returned_crates = 0;

    public int $total_revenue = 0;

    // ── Formulaire item ───────────────────────────────────────────────────────
    public bool $showItemForm = false;

    public ?int $item_customer_id = null;

    public ?int $item_product_id = null;

    public string $item_product_designation = '';

    public int $item_qty_delivered = 0;

    public int $item_qty_returned = 0;

    public int $item_unit_price = 0;

    public int $item_margin_per_unit = 0;

    public string $item_notes = '';

    public string $customer_search = '';

    public string $product_search = '';

    /** @var array<int, array<string, mixed>> */
    public array $customers_list = [];

    /** @var array<int, array<string, mixed>> */
    public array $products_list = [];

    // ── Formulaire dépense ────────────────────────────────────────────────────
    public bool $showExpenseForm = false;

    public ?int $expense_category_id = null;

    public string $expense_label = '';

    public int $expense_amount = 0;

    public function mount(DeliveryTrip $trip): void
    {
        $this->trip = $trip;
        $this->loaded_crates = $trip->loaded_crates ?? 0;
        $this->returned_crates = $trip->returned_crates ?? 0;
        $this->total_revenue = $trip->total_revenue ?? 0;
    }

    /**
     * Sauvegarde les valeurs du formulaire de l'étape courante
     * sans avancer le statut de la tournée.
     */
    public function saveProgress(): void
    {
        $data = match ($this->trip->status) {
            DeliveryTripStatus::Draft => [
                'loaded_crates' => $this->loaded_crates,
            ],
            DeliveryTripStatus::InProgress => [
                'returned_crates' => $this->returned_crates,
                'total_revenue' => $this->total_revenue,
            ],
            default => null,
        };

        if ($data === null) {
            return;
        }

        $this->trip->update($data);
        $this->trip = $this->trip->fresh();
        $this->dispatch('notify', message: 'Progression sauvegardée.', type: 'success');
    }

    // ── Workflow ──────────────────────────────────────────────────────────────

    public function load(): void
    {
        $this->validate(['loaded_crates' => 'required|integer|min:1']);

        try {
            $this->trip = app(DeliveryService::class)->load($this->trip, $this->loaded_crates);
            $this->dispatch('notify', message: 'Chargement enregistré. Le chauffeur peut partir.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function recordReturn(): void
    {
        $this->validate([
            'returned_crates' => 'required|integer|min:0',
            'total_revenue' => 'required|integer|min:0',
        ]);

        try {
            $this->trip = app(DeliveryService::class)->recordReturn(
                $this->trip,
                $this->returned_crates,
                $this->total_revenue
            );
            $this->dispatch('notify', message: 'Retour enregistré.', type: 'success');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    public function syncRevenue(): void
    {
        $this->trip = app(DeliveryService::class)->syncRevenueFromItems($this->trip->load('items'));
        $this->total_revenue = $this->trip->total_revenue ?? 0;
        $this->dispatch('notify', message: 'Recette synchronisée depuis les lignes.', type: 'success');
    }

    public function close(): void
    {
        if (! $this->checkPermission('close_deliveries', 'Vous n\'avez pas la permission de clôturer une tournée.')) {
            return;
        }

        try {
            $this->trip = app(DeliveryService::class)->close($this->trip);
            $this->dispatch('notify', message: 'Tournée clôturée avec succès.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    // ── Recherche client / produit ────────────────────────────────────────────

    public function updatedCustomerSearch(): void
    {
        if (strlen($this->customer_search) < 2) {
            $this->customers_list = [];

            return;
        }

        $this->customers_list = Customer::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%'.$this->customer_search.'%')
            ->limit(5)
            ->get(['id', 'name'])
            ->toArray();
    }

    public function selectCustomer(int $id, string $name): void
    {
        $this->item_customer_id = $id;
        $this->customer_search = $name;
        $this->customers_list = [];
    }

    public function updatedProductSearch(): void
    {
        if (strlen($this->product_search) < 1) {
            $this->products_list = [];

            return;
        }

        $this->products_list = Product::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%'.$this->product_search.'%')
            ->limit(8)
            ->get(['id', 'name', 'sku', 'selling_price'])
            ->toArray();
    }

    public function selectProduct(int $id, string $name, int $sellingPrice): void
    {
        $this->item_product_id = $id;
        $this->item_product_designation = $name;
        $this->item_unit_price = $sellingPrice;
        $this->product_search = $name;
        $this->products_list = [];
    }

    // ── Items ─────────────────────────────────────────────────────────────────

    public function addItem(): void
    {
        $this->validate([
            'item_product_designation' => 'required|string|max:255',
            'item_customer_id' => 'nullable|exists:customers,id',
            'item_qty_delivered' => 'required|integer|min:1',
            'item_qty_returned' => 'required|integer|min:0',
            'item_unit_price' => 'required|integer|min:0',
            'item_margin_per_unit' => 'required|integer|min:0',
        ]);

        $product = $this->item_product_id ? Product::find($this->item_product_id) : null;

        $this->trip->items()->create([
            'customer_id' => $this->item_customer_id,
            'product_id' => $this->item_product_id,
            'product_ref' => $product?->sku,
            'product_designation' => $this->item_product_designation,
            'qty_delivered' => $this->item_qty_delivered,
            'qty_returned' => $this->item_qty_returned,
            'unit_price' => $this->item_unit_price,
            'margin_per_unit' => $this->item_margin_per_unit,
            'notes' => $this->item_notes ?: null,
        ]);

        $this->resetItemForm();
        $this->trip = $this->trip->fresh(['items.customer', 'items.product']);
        $this->dispatch('notify', message: 'Ligne ajoutée.', type: 'success');
    }

    public function removeItem(int $itemId): void
    {
        $this->trip->items()->where('id', $itemId)->delete();
        $this->trip = $this->trip->fresh(['items.customer', 'items.product']);
        $this->dispatch('notify', message: 'Ligne supprimée.', type: 'success');
    }

    private function resetItemForm(): void
    {
        $this->item_customer_id = null;
        $this->item_product_id = null;
        $this->item_product_designation = '';
        $this->item_qty_delivered = 0;
        $this->item_qty_returned = 0;
        $this->item_unit_price = 0;
        $this->item_margin_per_unit = 0;
        $this->item_notes = '';
        $this->customer_search = '';
        $this->product_search = '';
        $this->showItemForm = false;
    }

    // ── Dépenses ──────────────────────────────────────────────────────────────

    public function addExpense(): void
    {
        $this->validate([
            'expense_label' => 'required|string|max:255',
            'expense_amount' => 'required|integer|min:1',
            'expense_category_id' => 'nullable|exists:delivery_expense_categories,id',
        ]);

        $this->trip->expenses()->create([
            'category_id' => $this->expense_category_id,
            'label' => $this->expense_label,
            'amount' => $this->expense_amount,
        ]);

        $this->expense_category_id = null;
        $this->expense_label = '';
        $this->expense_amount = 0;
        $this->showExpenseForm = false;

        $this->trip = $this->trip->fresh(['expenses.category']);
        $this->dispatch('notify', message: 'Dépense ajoutée.', type: 'success');
    }

    public function removeExpense(int $expenseId): void
    {
        DeliveryExpense::where('trip_id', $this->trip->id)->where('id', $expenseId)->delete();
        $this->trip = $this->trip->fresh(['expenses.category']);
        $this->dispatch('notify', message: 'Dépense supprimée.', type: 'success');
    }

    public function selectExpenseCategory(int $id): void
    {
        $cat = DeliveryExpenseCategory::find($id);
        if ($cat) {
            $this->expense_category_id = $id;
            $this->expense_label = $cat->name;
        }
    }

    public function render(): View
    {
        $this->trip->load([
            'driver', 'vehicle', 'zone',
            'items.customer', 'items.product',
            'expenses.category',
            'closedBy',
        ]);

        $companyId = Auth::user()->company_id;
        $expenseCategories = DeliveryExpenseCategory::where('company_id', $companyId)
            ->orderBy('sort_order')
            ->orderBy('name')
            ->get();

        return view('livewire.deliveries.trip-show', compact('expenseCategories'));
    }
}
