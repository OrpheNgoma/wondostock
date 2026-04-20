<?php

namespace App\Livewire\Deliveries;

use App\Enums\DeliveryTripStatus;
use App\Models\DeliveryTrip;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Tournées de livraison - WondoStock')]
class Index extends Component
{
    public string $dateFilter = '';

    public string $statusFilter = '';

    public function mount(): void
    {
        $this->dateFilter = '';
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        $query = DeliveryTrip::with(['driver', 'vehicle', 'zone'])
            ->where('company_id', $companyId)
            ->when($this->dateFilter, fn ($q) => $q->whereDate('trip_date', $this->dateFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->orderBy('created_at', 'desc');

        $trips = $query->get();

        $stats = [
            'total' => $trips->count(),
            'in_progress' => $trips->where('status', DeliveryTripStatus::InProgress)->count(),
            'completed' => $trips->where('status', DeliveryTripStatus::Completed)->count(),
            'closed' => $trips->where('status', DeliveryTripStatus::Closed)->count(),
            'total_revenue' => $trips->whereNotNull('total_revenue')->sum('total_revenue'),
            'total_commissions' => $trips->whereNotNull('commission_amount')->sum('commission_amount'),
        ];

        return view('livewire.deliveries.index', compact('trips', 'stats'));
    }
}
