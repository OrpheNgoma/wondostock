@section('title', 'Paramètres Livraisons')

<div class="space-y-8">
    {{-- En-tête avec gradient indigo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Paramètres des livraisons</h1>
                    <p class="text-indigo-100 text-lg">Gérez les chauffeurs, véhicules et zones de livraison</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="setTab('drivers')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200
                            {{ $activeTab === 'drivers' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                        </svg>
                        Chauffeurs
                    </button>
                    <button
                        wire:click="setTab('vehicles')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200
                            {{ $activeTab === 'vehicles' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                        </svg>
                        Véhicules
                    </button>
                    <button
                        wire:click="setTab('zones')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200
                            {{ $activeTab === 'zones' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                        </svg>
                        Zones
                    </button>
                    <button
                        wire:click="setTab('zone_prices')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200
                            {{ $activeTab === 'zone_prices' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20' }}"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        Tarification
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- ═══════════════════════════════════════ TAB CHAUFFEURS ══════════════════════════════════════ --}}
    @if ($activeTab === 'drivers')
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Chauffeurs</h3>
                        <p class="text-sm text-gray-600">{{ $drivers->count() }} chauffeur(s) enregistré(s)</p>
                    </div>
                    <button
                        wire:click="$toggle('showDriverForm')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $showDriverForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4.5v15m7.5-7.5h-15' }}" />
                        </svg>
                        {{ $showDriverForm ? 'Annuler' : 'Nouveau chauffeur' }}
                    </button>
                </div>
            </div>

            {{-- Formulaire nouveau/édition chauffeur --}}
            @if ($showDriverForm)
                <div class="border-b border-indigo-100 bg-indigo-50/50 p-6">
                    <h4 class="text-sm font-semibold text-gray-800 mb-4">
                        {{ $editingDriverId ? 'Modifier le chauffeur' : 'Nouveau chauffeur' }}
                    </h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nom <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="driverForm.name"
                                placeholder="Nom complet"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('driverForm.name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                            <input
                                type="text"
                                wire:model="driverForm.phone"
                                placeholder="Ex: +242 06 000 0000"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('driverForm.phone')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">N° de permis</label>
                            <input
                                type="text"
                                wire:model="driverForm.license_number"
                                placeholder="Ex: ABC-123456"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('driverForm.license_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Salaire de base (FCFA) <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="number"
                                wire:model="driverForm.base_salary"
                                min="0"
                                placeholder="Ex: 150000"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('driverForm.base_salary')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <button
                            wire:click="saveDriver"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $editingDriverId ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                        @if ($editingDriverId)
                            <button
                                wire:click="resetDriverForm"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-all duration-200"
                            >
                                Annuler la modification
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Tableau des chauffeurs --}}
            @if ($drivers->isEmpty())
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Aucun chauffeur</p>
                            <p class="text-sm text-gray-500 mt-1">Créez votre premier chauffeur pour commencer</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">Nom</th>
                                <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Téléphone</th>
                                <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Permis</th>
                                <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Salaire de base</th>
                                <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                                <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($drivers as $driver)
                                <tr wire:key="driver-{{ $driver->id }}" class="group hover:bg-gray-50/50 transition-colors duration-150 {{ $driver->trashed() ? 'opacity-60' : '' }}">
                                    <td class="py-4 pl-6 pr-3">
                                        <span class="text-sm font-semibold text-gray-900">{{ $driver->name }}</span>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="text-sm text-gray-600">{{ $driver->phone ?? '—' }}</span>
                                    </td>
                                    <td class="px-3 py-4">
                                        @if ($driver->license_number)
                                            <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">{{ $driver->license_number }}</code>
                                        @else
                                            <span class="text-sm text-gray-400">—</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-right">
                                        <span class="text-sm font-medium text-gray-900">{{ number_format($driver->base_salary, 0, ',', ' ') }} FCFA</span>
                                    </td>
                                    <td class="px-3 py-4">
                                        @if ($driver->trashed())
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">
                                                Supprimé
                                            </span>
                                        @elseif ($driver->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">
                                                Actif
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">
                                                Inactif
                                            </span>
                                        @endif
                                    </td>
                                    <td class="relative py-4 pl-3 pr-6 text-right">
                                        @if (!$driver->trashed())
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    wire:click="editDriver({{ $driver->id }})"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                                                >
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                    Modifier
                                                </button>
                                                <button
                                                    wire:click="toggleDriverActive({{ $driver->id }})"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-medium transition-colors duration-200
                                                        {{ $driver->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}"
                                                >
                                                    {{ $driver->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════ TAB VÉHICULES ═══════════════════════════════════════ --}}
    @if ($activeTab === 'vehicles')
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Véhicules</h3>
                        <p class="text-sm text-gray-600">{{ $vehicles->count() }} véhicule(s) enregistré(s)</p>
                    </div>
                    <button
                        wire:click="$toggle('showVehicleForm')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $showVehicleForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4.5v15m7.5-7.5h-15' }}" />
                        </svg>
                        {{ $showVehicleForm ? 'Annuler' : 'Nouveau véhicule' }}
                    </button>
                </div>
            </div>

            {{-- Formulaire véhicule --}}
            @if ($showVehicleForm)
                <div class="border-b border-indigo-100 bg-indigo-50/50 p-6">
                    <h4 class="text-sm font-semibold text-gray-800 mb-4">
                        {{ $editingVehicleId ? 'Modifier le véhicule' : 'Nouveau véhicule' }}
                    </h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Immatriculation <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="vehicleForm.plate_number"
                                placeholder="Ex: AB 123 CG"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('vehicleForm.plate_number')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Marque</label>
                            <input
                                type="text"
                                wire:model="vehicleForm.brand"
                                placeholder="Ex: Toyota"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('vehicleForm.brand')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Modèle</label>
                            <input
                                type="text"
                                wire:model="vehicleForm.model"
                                placeholder="Ex: Hiace"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('vehicleForm.model')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <button
                            wire:click="saveVehicle"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $editingVehicleId ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                        @if ($editingVehicleId)
                            <button
                                wire:click="resetVehicleForm"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-all duration-200"
                            >
                                Annuler la modification
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Tableau des véhicules --}}
            @if ($vehicles->isEmpty())
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Aucun véhicule</p>
                            <p class="text-sm text-gray-500 mt-1">Créez votre premier véhicule pour commencer</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">Immatriculation</th>
                                <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Marque / Modèle</th>
                                <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                                <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($vehicles as $vehicle)
                                <tr wire:key="vehicle-{{ $vehicle->id }}" class="group hover:bg-gray-50/50 transition-colors duration-150 {{ $vehicle->trashed() ? 'opacity-60' : '' }}">
                                    <td class="py-4 pl-6 pr-3">
                                        <code class="bg-gray-100 text-gray-900 px-2.5 py-1 rounded text-sm font-mono font-semibold">{{ $vehicle->plate_number }}</code>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="text-sm text-gray-700">
                                            {{ trim($vehicle->brand . ' ' . $vehicle->model) ?: '—' }}
                                        </span>
                                    </td>
                                    <td class="px-3 py-4">
                                        @if ($vehicle->trashed())
                                            <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-1 text-xs font-medium text-gray-600">Supprimé</span>
                                        @elseif ($vehicle->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-1 text-xs font-medium text-green-700">Actif</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-medium text-red-700">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="relative py-4 pl-3 pr-6 text-right">
                                        @if (!$vehicle->trashed())
                                            <div class="flex items-center justify-end gap-2">
                                                <button
                                                    wire:click="editVehicle({{ $vehicle->id }})"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                                                >
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                    </svg>
                                                    Modifier
                                                </button>
                                                <button
                                                    wire:click="toggleVehicleActive({{ $vehicle->id }})"
                                                    type="button"
                                                    class="inline-flex items-center gap-1 rounded-lg px-3 py-2 text-xs font-medium transition-colors duration-200
                                                        {{ $vehicle->is_active ? 'bg-red-50 text-red-700 hover:bg-red-100' : 'bg-green-50 text-green-700 hover:bg-green-100' }}"
                                                >
                                                    {{ $vehicle->is_active ? 'Désactiver' : 'Activer' }}
                                                </button>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ═══════════════════════════════════════ TAB ZONES ═══════════════════════════════════════════ --}}
    @if ($activeTab === 'zones')
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Zones de livraison</h3>
                        <p class="text-sm text-gray-600">{{ $zones->count() }} zone(s) enregistrée(s)</p>
                    </div>
                    <button
                        wire:click="$toggle('showZoneForm')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="{{ $showZoneForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4.5v15m7.5-7.5h-15' }}" />
                        </svg>
                        {{ $showZoneForm ? 'Annuler' : 'Nouvelle zone' }}
                    </button>
                </div>
            </div>

            {{-- Formulaire zone --}}
            @if ($showZoneForm)
                <div class="border-b border-indigo-100 bg-indigo-50/50 p-6">
                    <h4 class="text-sm font-semibold text-gray-800 mb-4">
                        {{ $editingZoneId ? 'Modifier la zone' : 'Nouvelle zone' }}
                    </h4>
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Nom de la zone <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="zoneForm.name"
                                placeholder="Ex: Zone Nord"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('zoneForm.name')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Ville <span class="text-red-500">*</span>
                            </label>
                            <input
                                type="text"
                                wire:model="zoneForm.city"
                                placeholder="Ex: Brazzaville"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('zoneForm.city')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">
                                Prime de mission (FCFA) <span class="text-red-500">*</span>
                            </label>
                            {{-- Presets rapides --}}
                            <div class="flex items-center gap-2 mb-2">
                                @foreach ([5000, 10000, 15000] as $preset)
                                    <button
                                        wire:click="$set('zoneForm.mission_allowance', {{ $preset }})"
                                        type="button"
                                        class="rounded-lg px-2.5 py-1 text-xs font-medium transition-colors duration-150
                                            {{ (int) $zoneForm['mission_allowance'] === $preset ? 'bg-indigo-600 text-white' : 'bg-indigo-100 text-indigo-700 hover:bg-indigo-200' }}"
                                    >
                                        {{ number_format($preset, 0, ',', ' ') }}
                                    </button>
                                @endforeach
                                <span class="text-xs text-gray-400">ou saisir :</span>
                            </div>
                            <input
                                type="number"
                                wire:model="zoneForm.mission_allowance"
                                min="0"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                            @error('zoneForm.mission_allowance')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <button
                            wire:click="saveZone"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200"
                        >
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            {{ $editingZoneId ? 'Mettre à jour' : 'Enregistrer' }}
                        </button>
                        @if ($editingZoneId)
                            <button
                                wire:click="resetZoneForm"
                                type="button"
                                class="inline-flex items-center gap-2 rounded-xl bg-gray-100 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-200 transition-all duration-200"
                            >
                                Annuler la modification
                            </button>
                        @endif
                    </div>
                </div>
            @endif

            {{-- Tableau des zones --}}
            @if ($zones->isEmpty())
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Aucune zone</p>
                            <p class="text-sm text-gray-500 mt-1">Créez votre première zone de livraison</p>
                        </div>
                    </div>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">Nom</th>
                                <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Ville</th>
                                <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Prime de mission</th>
                                <th scope="col" class="px-3 py-4 text-center text-sm font-semibold text-gray-700">Tournées</th>
                                <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($zones as $zone)
                                <tr wire:key="zone-{{ $zone->id }}" class="group hover:bg-gray-50/50 transition-colors duration-150">
                                    <td class="py-4 pl-6 pr-3">
                                        <span class="text-sm font-semibold text-gray-900">{{ $zone->name }}</span>
                                    </td>
                                    <td class="px-3 py-4">
                                        <span class="text-sm text-gray-600">{{ $zone->city }}</span>
                                    </td>
                                    <td class="px-3 py-4 text-right">
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-1 text-xs font-semibold text-indigo-700">
                                            {{ number_format($zone->mission_allowance, 0, ',', ' ') }} FCFA/voyage
                                        </span>
                                    </td>
                                    <td class="px-3 py-4 text-center">
                                        <span class="text-sm font-medium text-gray-700">{{ $zone->delivery_trips_count }}</span>
                                    </td>
                                    <td class="relative py-4 pl-3 pr-6 text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <button
                                                wire:click="editZone({{ $zone->id }})"
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                                            >
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Modifier
                                            </button>
                                            <button
                                                wire:click="deleteZone({{ $zone->id }})"
                                                wire:confirm="Supprimer cette zone ? Cette action est irréversible."
                                                type="button"
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors duration-200"
                                            >
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Supprimer
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════════════════════════ TAB TARIFICATION ═══════════════════════════════════════ --}}
    @if ($activeTab === 'zone_prices')
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Prix de vente & marges par zone</h3>
                        <p class="text-sm text-gray-500 mt-0.5">
                            La marge est calculée automatiquement (prix vente − prix achat) sauf si vous la définissez manuellement.
                        </p>
                    </div>
                    @if ($zones->isNotEmpty())
                        <div class="shrink-0">
                            <select
                                wire:model.live="selectedZonePriceZoneId"
                                class="block rounded-xl border-0 py-2.5 pl-4 pr-10 text-sm font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                                <option value="">-- Sélectionner une zone --</option>
                                @foreach ($zones as $zone)
                                    <option value="{{ $zone->id }}">{{ $zone->name }} ({{ $zone->city }})</option>
                                @endforeach
                            </select>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Pas de zones --}}
            @if ($zones->isEmpty())
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 6.75V15m6-6v8.25m.503 3.498l4.875-2.437c.381-.19.622-.58.622-1.006V4.82c0-.836-.88-1.38-1.628-1.006l-3.869 1.934c-.317.159-.69.159-1.006 0L9.503 3.252a1.125 1.125 0 00-1.006 0L3.622 5.689C3.24 5.88 3 6.27 3 6.695V19.18c0 .836.88 1.38 1.628 1.006l3.869-1.934c.317-.159.69-.159 1.006 0l4.994 2.497c.317.158.69.158 1.006 0z" />
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Aucune zone configurée</p>
                            <p class="text-sm text-gray-500 mt-1">
                                Créez d'abord vos zones dans l'onglet <strong>Zones</strong> avant de configurer les prix.
                            </p>
                        </div>
                        <button
                            wire:click="setTab('zones')"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-all duration-200"
                        >
                            Aller à l'onglet Zones
                        </button>
                    </div>
                </div>

            {{-- Zone sélectionnée : aucun produit --}}
            @elseif ($selectedZonePriceZoneId && $zonePriceProducts->isEmpty())
                <div class="py-16 text-center">
                    <p class="text-gray-500">Aucun produit actif trouvé. Ajoutez des produits depuis le catalogue.</p>
                </div>

            {{-- Zone sélectionnée : tableau de tarification --}}
            @elseif ($selectedZonePriceZoneId && $zonePriceProducts->isNotEmpty())
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead class="bg-gray-50/50">
                            <tr>
                                <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">Produit</th>
                                <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Prix achat</th>
                                <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">
                                    Prix vente <span class="text-red-500">*</span>
                                </th>
                                <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Marge auto</th>
                                <th scope="col" class="px-4 py-4 text-left text-sm font-semibold text-gray-700">Marge manuelle</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-50 bg-white">
                            @foreach ($zonePriceProducts as $product)
                                @php
                                    $pid = (string) $product->id;
                                    $row = $zonePriceValues[$pid] ?? ['selling_price' => $product->selling_price ?? 0, 'margin_override' => null, 'use_override' => false];
                                    $autoMargin = max(0, (int)($row['selling_price'] ?? 0) - ($product->purchase_price ?? 0));
                                @endphp
                                <tr
                                    wire:key="zpp-{{ $product->id }}"
                                    x-data="{
                                        sellingPrice: {{ (int)($row['selling_price'] ?? 0) }},
                                        purchasePrice: {{ $product->purchase_price ?? 0 }},
                                        get autoMargin() {
                                            return Math.max(0, parseInt(this.sellingPrice) - this.purchasePrice);
                                        },
                                        formatFcfa(n) {
                                            return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA';
                                        }
                                    }"
                                    class="group hover:bg-indigo-50/30 transition-colors duration-150"
                                >
                                    {{-- Produit --}}
                                    <td class="py-3 pl-6 pr-3">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                            @if ($product->sku)
                                                <code class="text-xs text-gray-400">{{ $product->sku }}</code>
                                            @endif
                                        </div>
                                    </td>

                                    {{-- Prix achat --}}
                                    <td class="px-3 py-3 text-right">
                                        <span class="text-sm text-gray-500">
                                            {{ $product->purchase_price ? number_format($product->purchase_price, 0, ',', ' ').' FCFA' : '—' }}
                                        </span>
                                    </td>

                                    {{-- Prix vente --}}
                                    <td class="px-3 py-3 text-right">
                                        <input
                                            type="number"
                                            wire:model="zonePriceValues.{{ $pid }}.selling_price"
                                            @input="sellingPrice = parseInt($event.target.value) || 0"
                                            min="0"
                                            class="w-32 rounded-lg border-0 py-2 px-3 text-right text-sm font-medium text-gray-900 shadow-sm ring-1 ring-inset ring-indigo-200 bg-white focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                        >
                                        @error("zonePriceValues.{$pid}.selling_price")
                                            <p class="mt-1 text-xs text-red-600 text-right">{{ $message }}</p>
                                        @enderror
                                    </td>

                                    {{-- Marge auto (réactive via Alpine) --}}
                                    <td class="px-3 py-3 text-right">
                                        <span
                                            x-text="formatFcfa(autoMargin)"
                                            class="text-sm font-medium"
                                            :class="autoMargin > 0 ? 'text-emerald-600' : 'text-gray-400'"
                                        ></span>
                                    </td>

                                    {{-- Marge manuelle --}}
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            {{-- Toggle --}}
                                            <label class="flex items-center gap-2 cursor-pointer select-none">
                                                <input
                                                    type="checkbox"
                                                    wire:model.live="zonePriceValues.{{ $pid }}.use_override"
                                                    class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                                >
                                                <span class="text-xs text-gray-500">Manuel</span>
                                            </label>

                                            {{-- Input override --}}
                                            @if ($row['use_override'] === true)
                                                <div class="flex flex-col">
                                                    <input
                                                        type="number"
                                                        wire:model="zonePriceValues.{{ $pid }}.margin_override"
                                                        min="0"
                                                        placeholder="Ex: 1 200"
                                                        class="w-32 rounded-lg border-0 py-2 px-3 text-right text-sm font-semibold text-indigo-700 shadow-sm ring-1 ring-inset ring-indigo-300 bg-indigo-50 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                                                    >
                                                    @error("zonePriceValues.{$pid}.margin_override")
                                                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                                                    @enderror
                                                </div>
                                            @else
                                                <span class="text-xs text-gray-400 italic">Automatique</span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                {{-- Footer avec bouton save --}}
                <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4 flex items-center justify-between gap-4">
                    <p class="text-xs text-gray-400">
                        Les prix sont enregistrés par zone et utilisés automatiquement lors de la création d'une tournée.
                    </p>
                    <button
                        wire:click="saveZonePrices"
                        wire:loading.attr="disabled"
                        wire:target="saveZonePrices"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-60 transition-all duration-200"
                    >
                        <svg wire:loading.remove wire:target="saveZonePrices" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <svg wire:loading wire:target="saveZonePrices" class="h-4 w-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 14.627 0 12 0v4a8 8 0 00-8 8h4z"></path>
                        </svg>
                        Enregistrer les prix
                    </button>
                </div>

            {{-- Aucune zone sélectionnée --}}
            @else
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-3">
                        <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                            <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                            </svg>
                        </div>
                        <p class="text-gray-600 font-medium">Sélectionnez une zone pour configurer ses prix</p>
                        <p class="text-sm text-gray-400">Les prix de vente et marges sont définis par zone de livraison.</p>
                    </div>
                </div>
            @endif
        </div>
    @endif
</div>
