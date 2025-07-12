<?php

namespace App\Livewire\Stock\Movements;

use App\Models\Store;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\StockMovement;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use App\Enums\StockMovementType;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
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
                    $subQuery->where('name', 'like', '%' . $this->search . '%')
                             ->orWhere('sku', 'like', '%' . $this->search . '%');
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
            
        return view('livewire.stock.movements.index', [
            'movements' => $movements,
            'stores' => $stores,
            'movementTypes' => $movementTypes,
        ]);
    }
}