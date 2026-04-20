<?php

namespace App\Livewire\Deliveries;

use App\Models\Driver;
use App\Models\Vehicle;
use App\Models\Zone;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Paramètres livraisons - WondoStock')]
class Settings extends Component
{
    public string $activeTab = 'drivers';

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    // Driver form
    public bool $showDriverForm = false;

    public ?int $editingDriverId = null;

    /** @var array{name: string, phone: string, license_number: string, base_salary: int} */
    public array $driverForm = ['name' => '', 'phone' => '', 'license_number' => '', 'base_salary' => 0];

    // Vehicle form
    public bool $showVehicleForm = false;

    public ?int $editingVehicleId = null;

    /** @var array{plate_number: string, brand: string, model: string} */
    public array $vehicleForm = ['plate_number' => '', 'brand' => '', 'model' => ''];

    // Zone form
    public bool $showZoneForm = false;

    public ?int $editingZoneId = null;

    /** @var array{name: string, city: string, mission_allowance: int} */
    public array $zoneForm = ['name' => '', 'city' => '', 'mission_allowance' => 5000];

    // ── DRIVERS ──────────────────────────────────────────────────────────────

    protected function driverRules(): array
    {
        return [
            'driverForm.name' => 'required|string|max:255',
            'driverForm.phone' => 'nullable|string|max:50',
            'driverForm.license_number' => 'nullable|string|max:50',
            'driverForm.base_salary' => 'required|integer|min:0',
        ];
    }

    public function saveDriver(): void
    {
        $this->validate($this->driverRules());

        $companyId = Auth::user()->company_id;

        if ($this->editingDriverId) {
            Driver::where('company_id', $companyId)->findOrFail($this->editingDriverId)->update($this->driverForm);
            $message = 'Chauffeur mis à jour.';
        } else {
            Driver::create(array_merge($this->driverForm, ['company_id' => $companyId]));
            $message = 'Chauffeur créé.';
        }

        $this->resetDriverForm();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    public function editDriver(int $id): void
    {
        $driver = Driver::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $this->editingDriverId = $id;
        $this->driverForm = $driver->only(['name', 'phone', 'license_number', 'base_salary']);
        $this->showDriverForm = true;
    }

    public function toggleDriverActive(int $id): void
    {
        $driver = Driver::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $driver->update(['is_active' => ! $driver->is_active]);
        $this->dispatch('notify', message: $driver->is_active ? 'Chauffeur désactivé.' : 'Chauffeur activé.', type: 'success');
    }

    public function resetDriverForm(): void
    {
        $this->editingDriverId = null;
        $this->driverForm = ['name' => '', 'phone' => '', 'license_number' => '', 'base_salary' => 0];
        $this->showDriverForm = false;
    }

    // ── VEHICLES ─────────────────────────────────────────────────────────────

    protected function vehicleRules(): array
    {
        return [
            'vehicleForm.plate_number' => 'required|string|max:20',
            'vehicleForm.brand' => 'nullable|string|max:100',
            'vehicleForm.model' => 'nullable|string|max:100',
        ];
    }

    public function saveVehicle(): void
    {
        $this->validate($this->vehicleRules());

        $companyId = Auth::user()->company_id;

        if ($this->editingVehicleId) {
            Vehicle::where('company_id', $companyId)->findOrFail($this->editingVehicleId)->update($this->vehicleForm);
            $message = 'Véhicule mis à jour.';
        } else {
            Vehicle::create(array_merge($this->vehicleForm, ['company_id' => $companyId]));
            $message = 'Véhicule créé.';
        }

        $this->resetVehicleForm();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    public function editVehicle(int $id): void
    {
        $vehicle = Vehicle::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $this->editingVehicleId = $id;
        $this->vehicleForm = $vehicle->only(['plate_number', 'brand', 'model']);
        $this->showVehicleForm = true;
    }

    public function toggleVehicleActive(int $id): void
    {
        $vehicle = Vehicle::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $vehicle->update(['is_active' => ! $vehicle->is_active]);
        $this->dispatch('notify', message: $vehicle->is_active ? 'Véhicule désactivé.' : 'Véhicule activé.', type: 'success');
    }

    public function resetVehicleForm(): void
    {
        $this->editingVehicleId = null;
        $this->vehicleForm = ['plate_number' => '', 'brand' => '', 'model' => ''];
        $this->showVehicleForm = false;
    }

    // ── ZONES ─────────────────────────────────────────────────────────────────

    protected function zoneRules(): array
    {
        return [
            'zoneForm.name' => 'required|string|max:100',
            'zoneForm.city' => 'required|string|max:100',
            'zoneForm.mission_allowance' => 'required|integer|min:0',
        ];
    }

    public function saveZone(): void
    {
        $this->validate($this->zoneRules());

        $companyId = Auth::user()->company_id;

        if ($this->editingZoneId) {
            Zone::where('company_id', $companyId)->findOrFail($this->editingZoneId)->update($this->zoneForm);
            $message = 'Zone mise à jour.';
        } else {
            Zone::create(array_merge($this->zoneForm, ['company_id' => $companyId]));
            $message = 'Zone créée.';
        }

        $this->resetZoneForm();
        $this->dispatch('notify', message: $message, type: 'success');
    }

    public function editZone(int $id): void
    {
        $zone = Zone::where('company_id', Auth::user()->company_id)->findOrFail($id);
        $this->editingZoneId = $id;
        $this->zoneForm = $zone->only(['name', 'city', 'mission_allowance']);
        $this->showZoneForm = true;
    }

    public function resetZoneForm(): void
    {
        $this->editingZoneId = null;
        $this->zoneForm = ['name' => '', 'city' => '', 'mission_allowance' => 5000];
        $this->showZoneForm = false;
    }

    public function deleteZone(int $id): void
    {
        Zone::where('company_id', Auth::user()->company_id)->findOrFail($id)->delete();
        $this->dispatch('notify', message: 'Zone supprimée.', type: 'success');
    }

    // ── RENDER ────────────────────────────────────────────────────────────────

    public function render(): View
    {
        $companyId = Auth::user()->company_id;
        $drivers = Driver::where('company_id', $companyId)->withTrashed()->orderBy('name')->get();
        $vehicles = Vehicle::where('company_id', $companyId)->withTrashed()->orderBy('plate_number')->get();
        $zones = Zone::where('company_id', $companyId)->orderBy('name')->get();

        return view('livewire.deliveries.settings', compact('drivers', 'vehicles', 'zones'));
    }
}
