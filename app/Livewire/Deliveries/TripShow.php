<?php

namespace App\Livewire\Deliveries;

use App\Enums\DeliveryTripStatus;
use App\Models\DeliveryExpense;
use App\Models\DeliveryExpenseCategory;
use App\Models\DeliveryTrip;
use App\Models\Product;
use App\Models\ZoneProductPrice;
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

    // ── Chargement par produit (statut : draft) ───────────────────────────────
    public string $loading_search = '';

    /** @var array<int, array<string, mixed>> */
    public array $loading_list = [];

    /**
     * Liste des produits à charger, indexée numériquement.
     *
     * @var array<int, array{product_id: int|null, name: string, sku: string|null, unit_price: int, margin_per_unit: int, qty: int}>
     */
    public array $loadingRows = [];

    // ── Retour par produit (statut : in_progress) ────────────────────────────
    /**
     * Quantités retournées par item, clé = item_id (string).
     *
     * @var array<string, int>
     */
    public array $returnQties = [];

    // ── Formulaire dépense ────────────────────────────────────────────────────
    public bool $showExpenseForm = false;

    public ?int $expense_category_id = null;

    public string $expense_label = '';

    public int $expense_amount = 0;

    public function mount(DeliveryTrip $trip): void
    {
        $this->trip = $trip;
        $trip->load('items');

        // Pré-remplir le tableau de chargement si des items existent déjà en brouillon
        if ($trip->status === DeliveryTripStatus::Draft && $trip->items->isNotEmpty()) {
            $this->loadingRows = $trip->items->map(fn ($item): array => [
                'product_id' => $item->product_id,
                'name' => $item->product_designation,
                'sku' => $item->product_ref,
                'unit_price' => $item->unit_price,
                'margin_per_unit' => $item->margin_per_unit,
                'qty' => $item->qty_delivered,
            ])->values()->toArray();
        }

        // Pré-remplir les quantités retournées
        if ($trip->status === DeliveryTripStatus::InProgress) {
            $this->returnQties = $trip->items
                ->mapWithKeys(fn ($item): array => [(string) $item->id => $item->qty_returned])
                ->toArray();
        }
    }

    // ── Chargement par produit ────────────────────────────────────────────────

    public function updatedLoadingSearch(): void
    {
        if (strlen($this->loading_search) < 1) {
            $this->loading_list = [];

            return;
        }

        $products = Product::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%'.$this->loading_search.'%')
            ->limit(8)
            ->get(['id', 'name', 'sku', 'selling_price', 'purchase_price']);

        $zoneId = $this->trip->zone_id;
        $zonePrices = $zoneId
            ? ZoneProductPrice::where('zone_id', $zoneId)
                ->whereIn('product_id', $products->pluck('id'))
                ->where('is_active', true)
                ->get()
                ->keyBy('product_id')
            : collect();

        $this->loading_list = $products->map(function (Product $p) use ($zonePrices): array {
            $zp = $zonePrices->get($p->id);

            return [
                'id' => $p->id,
                'name' => $p->name,
                'sku' => $p->sku,
                'unit_price' => $zp?->selling_price ?? $p->selling_price ?? 0,
                'margin' => $zp?->effective_margin ?? 0,
                'has_zone_price' => $zp !== null,
            ];
        })->toArray();
    }

    public function addToLoading(int $id): void
    {
        foreach ($this->loadingRows as $row) {
            if ((int) ($row['product_id'] ?? 0) === $id) {
                $product = Product::where('company_id', Auth::user()->company_id)->find($id);
                $label = $product?->name ?? 'ce produit';
                $this->dispatch('notify', message: "{$label} est déjà dans la liste.", type: 'warning');
                $this->loading_search = '';
                $this->loading_list = [];

                return;
            }
        }

        $product = Product::where('company_id', Auth::user()->company_id)
            ->find($id, ['id', 'name', 'sku', 'selling_price', 'purchase_price']);

        if (! $product) {
            return;
        }

        $zoneId = $this->trip->zone_id;
        $zonePrice = $zoneId
            ? ZoneProductPrice::where('zone_id', $zoneId)
                ->where('product_id', $id)
                ->where('is_active', true)
                ->first()
            : null;

        $unitPrice = $zonePrice?->selling_price ?? $product->selling_price ?? 0;
        $margin = $zonePrice?->effective_margin ?? 0;

        $this->loadingRows[] = [
            'product_id' => $id,
            'name' => $product->name,
            'sku' => $product->sku ?? '',
            'unit_price' => $unitPrice,
            'margin_per_unit' => $margin,
            'qty' => 1,
        ];

        $this->loading_search = '';
        $this->loading_list = [];
    }

    public function removeLoadingRow(int $index): void
    {
        array_splice($this->loadingRows, $index, 1);
        $this->loadingRows = array_values($this->loadingRows);
    }

    public function loadProducts(): void
    {
        if (! $this->checkPermission('edit_deliveries', "Vous n'avez pas la permission de valider le départ.")) {
            return;
        }

        if (empty($this->loadingRows)) {
            $this->addError('loadingRows', 'Ajoutez au moins un produit avant de valider le départ.');

            return;
        }

        $rules = [];
        foreach (array_keys($this->loadingRows) as $i) {
            $rules["loadingRows.{$i}.qty"] = 'required|integer|min:1';
        }
        $this->validate($rules, [], array_fill_keys(
            array_map(fn (int $i): string => "loadingRows.{$i}.qty", array_keys($this->loadingRows)),
            'quantité'
        ));

        try {
            $this->trip = app(DeliveryService::class)->loadWithItems($this->trip, $this->loadingRows);
            $this->loadingRows = [];
            $this->loading_search = '';
            $this->loading_list = [];
            $this->dispatch('notify', message: 'Chargement enregistré. Le chauffeur peut partir.', type: 'success');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
    }

    // ── Retour par produit ────────────────────────────────────────────────────

    public function recordReturnFromProducts(): void
    {
        if (! $this->checkPermission('edit_deliveries', "Vous n'avez pas la permission d'enregistrer le retour.")) {
            return;
        }

        $rules = [];
        foreach (array_keys($this->returnQties) as $itemId) {
            $rules["returnQties.{$itemId}"] = 'required|integer|min:0';
        }
        $this->validate($rules);

        try {
            $this->trip = app(DeliveryService::class)->recordReturnFromItems($this->trip, $this->returnQties);
            $this->returnQties = [];
            $this->dispatch('notify', message: 'Retour enregistré. Vérifiez le résumé puis clôturez.', type: 'success');
        } catch (ValidationException $e) {
            foreach ($e->errors() as $field => $messages) {
                $this->addError($field, $messages[0]);
            }
        }
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
        $this->trip->loadMissing([
            'driver', 'vehicle', 'zone',
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

        $itemsData = $this->trip->canReturn()
            ? $this->trip->items->map(fn ($i): array => [
                'id' => $i->id,
                'qty_delivered' => $i->qty_delivered,
                'unit_price' => $i->unit_price,
                'margin_per_unit' => $i->margin_per_unit,
            ])->values()->toArray()
            : [];

        return view('livewire.deliveries.trip-show', compact('expenseCategories', 'itemsData'));
    }
}
