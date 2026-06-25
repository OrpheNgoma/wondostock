<?php

namespace App\Livewire\StockPurchases;

use App\Enums\DeliveryTripStatus;
use App\Models\StockPurchaseTrip;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Achats de stock - WondoStock')]
class Index extends Component
{
    use AuthorizesLivewireActions;

    public string $dateFilter = '';

    public string $statusFilter = '';

    public function mount(): void
    {
        $this->requirePermission('view_stock_purchases');
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $trips = StockPurchaseTrip::with(['driver', 'vehicle', 'store', 'supplier'])
            ->where('company_id', $companyId)
            ->when($this->dateFilter, fn ($q) => $q->whereDate('trip_date', $this->dateFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc')
            ->get();

        $stats = [
            'total' => $trips->count(),
            'in_progress' => $trips->where('status', DeliveryTripStatus::InProgress)->count(),
            'completed' => $trips->where('status', DeliveryTripStatus::Completed)->count(),
            'closed' => $trips->where('status', DeliveryTripStatus::Closed)->count(),
            'total_purchase_cost' => $trips->whereNotNull('total_purchase_cost')->sum('total_purchase_cost'),
            'total_allowances' => $trips->where('status', DeliveryTripStatus::Closed)->sum('mission_allowance_amount'),
        ];

        return view('livewire.stock-purchases.index', compact('trips', 'stats'));
    }
}
