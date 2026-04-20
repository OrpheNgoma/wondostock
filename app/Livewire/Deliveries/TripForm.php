<?php

namespace App\Livewire\Deliveries;

use App\Enums\DeliveryTripStatus;
use App\Models\DeliveryTrip;
use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Tournée - WondoStock')]
class TripForm extends Component
{
    public DeliveryTrip $trip;

    public ?int $driver_id = null;

    public ?int $vehicle_id = null;

    public ?int $zone_id = null;

    public string $trip_date = '';

    public string $notes = '';

    protected function rules(): array
    {
        return [
            'driver_id' => 'required|exists:drivers,id',
            'vehicle_id' => 'nullable|exists:vehicles,id',
            'zone_id' => 'required|exists:zones,id',
            'trip_date' => 'required|date',
            'notes' => 'nullable|string|max:500',
        ];
    }

    public function mount(DeliveryTrip $trip): void
    {
        $this->trip = $trip;

        if ($trip->exists) {
            $this->driver_id = $trip->driver_id;
            $this->vehicle_id = $trip->vehicle_id;
            $this->zone_id = $trip->zone_id;
            $this->trip_date = $trip->trip_date->format('Y-m-d');
            $this->notes = $trip->notes ?? '';
        } else {
            $this->trip_date = now()->format('Y-m-d');
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
            'zone_id' => $this->zone_id,
            'trip_date' => $this->trip_date,
            'notes' => $this->notes,
        ]);

        if (! $this->trip->exists) {
            $this->trip->status = DeliveryTripStatus::Draft;
        }

        $this->trip->save();

        $this->dispatch('notify', message: 'Tournée enregistrée.', type: 'success');
        $this->redirectRoute('deliveries.show', $this->trip, navigate: true);
    }

    public function render(): View
    {
        $companyId = Auth::user()->company_id;
        $drivers = Driver::where('company_id', $companyId)->active()->orderBy('name')->get();
        $vehicles = Vehicle::where('company_id', $companyId)->active()->orderBy('plate_number')->get();
        $zones = Zone::where('company_id', $companyId)->orderBy('name')->get();

        return view('livewire.deliveries.trip-form', compact('drivers', 'vehicles', 'zones'));
    }
}
