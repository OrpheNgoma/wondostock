<?php

namespace App\Livewire\Deliveries;

use App\Models\Driver;
use App\Models\Product;
use App\Models\Vehicle;
use App\Models\Zone;
use App\Models\ZoneProductPrice;
use App\Traits\AuthorizesLivewireActions;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Paramètres livraisons - WondoStock')]
class Settings extends Component
{
    use AuthorizesLivewireActions;

    public string $activeTab = 'drivers';

    public function mount(): void
    {
        $this->requirePermission('edit_deliveries');
    }

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

    // ── TARIFICATION PAR ZONE ─────────────────────────────────────────────────

    public ?int $selectedZonePriceZoneId = null;

    /**
     * État éditable indexé par product_id (string pour compatibilité wire:model).
     *
     * @var array<string, array{selling_price: int, margin_override: int|null, use_override: bool}>
     */
    public array $zonePriceValues = [];

    public function updatedSelectedZonePriceZoneId(): void
    {
        $this->loadZonePriceValues();
    }

    private function loadZonePriceValues(): void
    {
        $this->zonePriceValues = [];

        if (! $this->selectedZonePriceZoneId) {
            return;
        }

        $companyId = Auth::user()->company_id;
        $zone = Zone::where('company_id', $companyId)->find($this->selectedZonePriceZoneId);

        if (! $zone) {
            return;
        }

        $products = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        $existingPrices = ZoneProductPrice::where('zone_id', $zone->id)
            ->where('company_id', $companyId)
            ->get()
            ->keyBy('product_id');

        foreach ($products as $product) {
            $existing = $existingPrices->get($product->id);

            $this->zonePriceValues[(string) $product->id] = [
                'selling_price' => $existing?->selling_price ?? $product->selling_price ?? 0,
                'margin_override' => $existing?->margin_override,
                'use_override' => $existing !== null && $existing->margin_override !== null,
            ];
        }
    }

    public function saveZonePrices(): void
    {
        if (! $this->checkPermission('edit_deliveries', 'Vous n\'avez pas la permission de modifier la tarification.')) {
            return;
        }

        if (! $this->selectedZonePriceZoneId) {
            return;
        }

        $companyId = Auth::user()->company_id;
        $zone = Zone::where('company_id', $companyId)->findOrFail($this->selectedZonePriceZoneId);

        // Whitelist : seuls les produits appartenant à cette entreprise sont traités.
        // Protège contre l'injection de product_id cross-tenant via l'état Livewire.
        $validProductIds = Product::where('company_id', $companyId)
            ->where('is_active', true)
            ->pluck('id')
            ->map(fn (int $id): string => (string) $id)
            ->flip()
            ->all();

        $safeValues = array_intersect_key($this->zonePriceValues, $validProductIds);

        $rules = [];
        $attributes = [];
        foreach (array_keys($safeValues) as $productId) {
            $rules["zonePriceValues.{$productId}.selling_price"] = 'required|integer|min:0';
            $rules["zonePriceValues.{$productId}.margin_override"] = [
                'nullable',
                'integer',
                'min:0',
                "required_if:zonePriceValues.{$productId}.use_override,1",
            ];
            $attributes["zonePriceValues.{$productId}.selling_price"] = 'prix de vente';
            $attributes["zonePriceValues.{$productId}.margin_override"] = 'marge manuelle';
        }

        $this->validate($rules, [], $attributes);

        DB::transaction(function () use ($zone, $companyId, $safeValues): void {
            foreach ($safeValues as $productId => $row) {
                $useOverride = (bool) ($row['use_override'] ?? false);
                $marginOverride = ($useOverride && isset($row['margin_override']))
                    ? (int) $row['margin_override']
                    : null;

                ZoneProductPrice::updateOrCreate(
                    ['zone_id' => $zone->id, 'product_id' => (int) $productId],
                    [
                        'company_id' => $companyId,
                        'selling_price' => (int) $row['selling_price'],
                        'margin_override' => $marginOverride,
                        'is_active' => true,
                    ]
                );
            }
        });

        $this->dispatch('notify', message: "Tarification de « {$zone->name} » enregistrée.", type: 'success');
    }

    // ── RENDER ────────────────────────────────────────────────────────────────

    public function render(): View
    {
        $companyId = Auth::user()->company_id;

        // Chaque collection n'est chargée que si l'onglet correspondant est actif.
        $drivers = $this->activeTab === 'drivers'
            ? Driver::where('company_id', $companyId)->withTrashed()->orderBy('name')->get()
            : collect();

        $vehicles = $this->activeTab === 'vehicles'
            ? Vehicle::where('company_id', $companyId)->withTrashed()->orderBy('plate_number')->get()
            : collect();

        // Les zones sont toujours chargées : elles alimentent le sélecteur de l'onglet Tarification.
        $zones = Zone::where('company_id', $companyId)
            ->withCount('deliveryTrips')
            ->orderBy('name')
            ->get();

        $zonePriceProducts = ($this->activeTab === 'zone_prices' && $this->selectedZonePriceZoneId)
            ? Product::where('company_id', $companyId)->where('is_active', true)->orderBy('name')->get()
            : collect();

        return view('livewire.deliveries.settings', compact('drivers', 'vehicles', 'zones', 'zonePriceProducts'));
    }
}
