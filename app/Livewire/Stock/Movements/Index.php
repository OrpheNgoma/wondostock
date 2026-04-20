<?php

namespace App\Livewire\Stock\Movements;

use App\Enums\StockMovementType;
use App\Models\StockMovement;
use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.saas')]
#[Title('Mouvements de Stock - KaziFlow')]
class Index extends Component
{
    use WithPagination;

    // --- Filters ---
    public string $search = '';

    public ?int $storeFilter = null;

    public string $typeFilter = '';

    public ?string $dateFrom = null;

    public ?string $dateTo = null;

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $companyId = Auth::user()->company_id;

        $movements = StockMovement::where('company_id', $companyId)
            ->with(['product', 'store', 'user'])
            ->when($this->search, fn ($query) => $query->whereHas('product', fn ($sq) => $sq
                ->where('name', 'like', '%'.$this->search.'%')
                ->orWhere('sku', 'like', '%'.$this->search.'%')))
            ->when($this->storeFilter, fn ($q) => $q->where('store_id', $this->storeFilter))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->latest()
            ->get();

        $filename = 'mouvements_stock_'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($movements) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['Date', 'Produit', 'SKU', 'Type', 'Quantité', 'Magasin', 'Utilisateur'], ';');

            foreach ($movements as $mvt) {
                fputcsv($handle, [
                    $mvt->created_at->format('d/m/Y H:i'),
                    $mvt->product?->name ?? '',
                    $mvt->product?->sku ?? '',
                    $mvt->type->value,
                    $mvt->quantity,
                    $mvt->store?->name ?? '',
                    $mvt->user?->name ?? '',
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Nouvelle méthode pour réinitialiser tous les filtres.
     */
    public function resetFilters()
    {
        $this->reset('search', 'storeFilter', 'typeFilter', 'dateFrom', 'dateTo');
        // Réinitialise la pagination pour revenir à la première page
        $this->resetPage();
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;

        $movements = StockMovement::where('company_id', $companyId)
            ->with(['product', 'store', 'user', 'source']) // Eager loading pour la performance
            ->when($this->search, function ($query) {
                $query->whereHas('product', function ($subQuery) {
                    $subQuery->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('sku', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->storeFilter, function ($query) {
                $query->where('store_id', $this->storeFilter);
            })
            ->when($this->typeFilter, function ($query) {
                $query->where('type', $this->typeFilter);
            })
            ->when($this->dateFrom, function ($query) {
                $query->whereDate('created_at', '>=', $this->dateFrom);
            })
            ->when($this->dateTo, function ($query) {
                $query->whereDate('created_at', '<=', $this->dateTo);
            })
            ->latest() // Les plus récents en premier
            ->paginate(15);

        $stores = Store::where('company_id', $companyId)->get();
        $movementTypes = StockMovementType::cases();

        return view('livewire.saas.stock.movements.index', [
            'movements' => $movements,
            'stores' => $stores,
            'movementTypes' => $movementTypes,
        ]);
    }
}
