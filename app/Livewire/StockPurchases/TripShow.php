<?php

namespace App\Livewire\StockPurchases;

use App\Models\DeliveryExpenseCategory;
use App\Models\Product;
use App\Models\StockPurchaseExpense;
use App\Models\StockPurchaseTrip;
use App\Services\StockPurchaseService;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Détail achat de stock - WondoStock')]
class TripShow extends Component
{
    use AuthorizesLivewireActions;

    public StockPurchaseTrip $trip;

    // ── Départ (statut : draft) ───────────────────────────────────────────────
    public ?int $empty_crates_out = null;

    // ── Retour par produit (statut : in_progress) ─────────────────────────────
    public string $purchase_search = '';

    /** @var array<int, array<string, mixed>> */
    public array $purchase_list = [];

    /**
     * Produits achetés à enregistrer.
     *
     * @var array<int, array{product_id: int|null, name: string, sku: string|null, unit_cost: int, qty: int}>
     */
    public array $purchaseRows = [];

    public ?int $full_crates_in = null;

    // ── Formulaire dépense ────────────────────────────────────────────────────
    public bool $showExpenseForm = false;

    public ?int $expense_category_id = null;

    public string $expense_label = '';

    public int $expense_amount = 0;

    public function mount(StockPurchaseTrip $trip): void
    {
        $this->requirePermission('view_stock_purchases');

        $this->trip = $trip;
        $trip->load('items');

        // Pré-remplir le tableau d'achat si des items existent déjà (retour partiellement saisi)
        if ($trip->canReturn() && $trip->items->isNotEmpty()) {
            $this->purchaseRows = $trip->items->map(fn ($item): array => [
                'product_id' => $item->product_id,
                'name' => $item->product_designation,
                'sku' => $item->product_ref,
                'unit_cost' => $item->unit_cost,
                'qty' => $item->qty_purchased,
            ])->values()->toArray();
            $this->full_crates_in = $trip->full_crates_in;
        }
    }

    // ── Étape 1 : départ avec casiers vides ───────────────────────────────────

    public function startTrip(): void
    {
        if (! $this->checkPermission('edit_stock_purchases', "Vous n'avez pas la permission de valider le départ.")) {
            return;
        }

        $this->validate(
            ['empty_crates_out' => 'required|integer|min:1'],
            [],
            ['empty_crates_out' => 'casiers vides']
        );

        try {
            $this->trip = app(StockPurchaseService::class)->startTrip($this->trip, $this->empty_crates_out);
            $this->dispatch('notify', message: 'Départ enregistré. Le chauffeur part chercher le stock.', type: 'success');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    // ── Étape 2 : retour avec produits achetés ────────────────────────────────

    public function updatedPurchaseSearch(): void
    {
        if (strlen($this->purchase_search) < 1) {
            $this->purchase_list = [];

            return;
        }

        $this->purchase_list = Product::where('company_id', Auth::user()->company_id)
            ->where(function ($q) {
                $q->where('name', 'like', '%'.$this->purchase_search.'%')
                    ->orWhere('sku', 'like', '%'.$this->purchase_search.'%');
            })
            ->limit(8)
            ->get(['id', 'name', 'sku', 'purchase_price'])
            ->map(fn (Product $p): array => [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'purchase_price' => $p->purchase_price ?? 0,
            ])->toArray();
    }

    public function addToPurchase(int $id): void
    {
        foreach ($this->purchaseRows as $row) {
            if ((int) ($row['product_id'] ?? 0) === $id) {
                $this->dispatch('notify', message: 'Ce produit est déjà dans la liste.', type: 'warning');
                $this->purchase_search = '';
                $this->purchase_list = [];

                return;
            }
        }

        $product = Product::where('company_id', Auth::user()->company_id)
            ->find($id, ['id', 'name', 'sku', 'purchase_price']);

        if (! $product) {
            return;
        }

        $this->purchaseRows[] = [
            'product_id' => $product->id,
            'name' => $product->name,
            'sku' => $product->sku ?? '',
            'unit_cost' => $product->purchase_price ?? 0,
            'qty' => 1,
        ];

        $this->purchase_search = '';
        $this->purchase_list = [];
    }

    public function removePurchaseRow(int $index): void
    {
        array_splice($this->purchaseRows, $index, 1);
        $this->purchaseRows = array_values($this->purchaseRows);
    }

    public function recordReturn(): void
    {
        if (! $this->checkPermission('edit_stock_purchases', "Vous n'avez pas la permission d'enregistrer le retour.")) {
            return;
        }

        if (empty($this->purchaseRows)) {
            $this->addError('purchaseRows', 'Ajoutez au moins un produit acheté avant de valider le retour.');

            return;
        }

        $rules = ['full_crates_in' => 'required|integer|min:0'];
        $attributes = ['full_crates_in' => 'casiers pleins'];
        foreach (array_keys($this->purchaseRows) as $i) {
            $rules["purchaseRows.{$i}.qty"] = 'required|integer|min:1';
            $rules["purchaseRows.{$i}.unit_cost"] = 'required|integer|min:0';
            $attributes["purchaseRows.{$i}.qty"] = 'quantité';
            $attributes["purchaseRows.{$i}.unit_cost"] = 'coût unitaire';
        }
        $this->validate($rules, [], $attributes);

        try {
            $this->trip = app(StockPurchaseService::class)
                ->recordReturnWithItems($this->trip, $this->purchaseRows, $this->full_crates_in);
            $this->purchaseRows = [];
            $this->purchase_search = '';
            $this->purchase_list = [];
            $this->dispatch('notify', message: 'Retour enregistré. Vérifiez puis clôturez pour entrer le stock.', type: 'success');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    // ── Étape 3 : clôture + entrée en stock ───────────────────────────────────

    public function close(): void
    {
        if (! $this->checkPermission('close_stock_purchases', "Vous n'avez pas la permission de clôturer un voyage.")) {
            return;
        }

        try {
            $this->trip = app(StockPurchaseService::class)->close($this->trip);
            $this->dispatch('notify', message: 'Voyage clôturé. Le stock a été ajouté à l\'inventaire.', type: 'success');
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    // ── Dépenses ──────────────────────────────────────────────────────────────

    public function addExpense(): void
    {
        if (! $this->checkPermission('edit_stock_purchases', "Vous n'avez pas la permission d'ajouter une dépense.")) {
            return;
        }

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
        if (! $this->checkPermission('edit_stock_purchases', "Vous n'avez pas la permission de supprimer une dépense.")) {
            return;
        }

        StockPurchaseExpense::where('trip_id', $this->trip->id)->where('id', $expenseId)->delete();
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
        $this->trip->loadMissing([
            'driver', 'vehicle', 'store', 'supplier',
            'items',
            'expenses.category',
            'closedBy',
        ]);

        $expenseCategories = collect();
        if ($this->showExpenseForm) {
            $expenseCategories = DeliveryExpenseCategory::where('company_id', Auth::user()->company_id)
                ->orderBy('sort_order')
                ->orderBy('name')
                ->get();
        }

        return view('livewire.stock-purchases.trip-show', compact('expenseCategories'));
    }
}
