<?php

namespace App\Livewire\StockPurchases;

use App\Enums\DeliveryTripStatus;
use App\Models\Driver;
use App\Models\StockPurchaseTrip;
use App\Models\Store;
use App\Models\Supplier;
use App\Models\Vehicle;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Voyage d\'achat - WondoStock')]
class TripForm extends Component
{
    use AuthorizesLivewireActions;

    public StockPurchaseTrip $trip;

    public ?int $driver_id = null;

    public ?int $vehicle_id = null;

    public ?int $store_id = null;

    public ?int $supplier_id = null;

    public string $trip_date = '';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'store_id' => 'required|exists:stores,id',
            'supplier_id' => 'nullable|exists:suppliers,id',
            'trip_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function mount(StockPurchaseTrip $trip): void
    {
        $this->requirePermission($trip->exists ? 'edit_stock_purchases' : 'create_stock_purchases');

        $this->trip = $trip;

        if ($trip->exists) {
            $this->driver_id = $trip->driver_id;
            $this->vehicle_id = $trip->vehicle_id;
            $this->store_id = $trip->store_id;
            $this->supplier_id = $trip->supplier_id;
            $this->trip_date = $trip->trip_date->format('Y-m-d');
            $this->notes = $trip->notes ?? '';
        } else {
            $this->trip_date = now()->format('Y-m-d');
            $this->store_id = Auth::user()->company->stores()->first()?->id;
        }
    }

    public function save(): void
    {
        $this->validate();

        $companyId = Auth::user()->company_id;

        $this->trip->fill([
            'company_id' => $companyId,
            'driver_id' => $this->driver_id,
            'vehicle_id' => $this->vehicle_id,
            'store_id' => $this->store_id,
            'supplier_id' => $this->supplier_id,
            'trip_date' => $this->trip_date,
            'notes' => $this->notes,
        ]);

        if (! $this->trip->exists) {
            $this->trip->status = DeliveryTripStatus::Draft;
            $this->trip->mission_allowance_amount = StockPurchaseTrip::DEFAULT_MISSION_ALLOWANCE;
        }

        $this->trip->save();

        $this->dispatch('notify', message: 'Voyage d\'achat enregistré.', type: 'success');
        $this->redirectRoute('stock-purchases.show', $this->trip, navigate: true);
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;
        $drivers = Driver::where('company_id', $companyId)->active()->orderBy('name')->get();
        $vehicles = Vehicle::where('company_id', $companyId)->active()->orderBy('plate_number')->get();
        $stores = Store::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();
        $suppliers = Supplier::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get();

        return view('livewire.stock-purchases.trip-form', compact('drivers', 'vehicles', 'stores', 'suppliers'));
    }
}
