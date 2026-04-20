@section('title', $trip->exists ? 'Modifier la tournée' : 'Nouvelle tournée')

<div class="space-y-8">
    {{-- En-tête avec gradient indigo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        {{ $trip->exists ? 'Modifier la tournée' : 'Nouvelle tournée' }}
                    </h1>
                    <p class="text-indigo-100 text-lg">
                        {{ $trip->exists ? 'Mettre à jour les informations de la tournée' : 'Planifier une nouvelle tournée de livraison' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('deliveries.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Retour
                    </a>
                    <button
                        type="button"
                        wire:click="save"
                        class="inline-flex items-center gap-2 rounded-xl bg-white text-indigo-600 px-6 py-3 text-sm font-semibold shadow-lg hover:bg-gray-50 transition-all duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $trip->exists ? 'Mettre à jour' : 'Créer la tournée' }}
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
    </div>

    {{-- Corps du formulaire --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">

        {{-- Colonne principale (2/3) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Section Chauffeur --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Chauffeur</h3>
                            <p class="text-sm text-gray-600">Sélectionnez le chauffeur pour cette tournée</p>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    @if ($drivers->isEmpty())
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 flex items-start gap-3">
                            <svg class="h-5 w-5 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                            <div>
                                <p class="text-sm font-medium text-amber-800">Aucun chauffeur disponible</p>
                                <p class="text-sm text-amber-700 mt-1">
                                    Veuillez d'abord créer des chauffeurs dans les
                                    <a href="{{ route('deliveries.settings') }}" class="underline font-medium hover:text-amber-900">paramètres de livraison</a>.
                                </p>
                            </div>
                        </div>
                    @else
                        <div>
                            <label for="driver_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                Chauffeur <span class="text-red-500">*</span>
                            </label>
                            <select
                                wire:model="driver_id"
                                id="driver_id"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                            >
                                <option value="">-- Sélectionner un chauffeur --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                            @error('driver_id')
                                <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>
            </div>

            {{-- Section Détails --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                    <div class="flex items-center gap-3">
                        <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                            <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Détails de la tournée</h3>
                            <p class="text-sm text-gray-600">Véhicule, zone et planification</p>
                        </div>
                    </div>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Véhicule --}}
                    <div>
                        <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">Véhicule</label>
                        <select
                            wire:model="vehicle_id"
                            id="vehicle_id"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                        >
                            <option value="">-- Aucun véhicule --</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->display_name }}</option>
                            @endforeach
                        </select>
                        @error('vehicle_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Zone --}}
                    <div>
                        <label for="zone_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Zone <span class="text-red-500">*</span>
                        </label>
                        <select
                            wire:model.live="zone_id"
                            id="zone_id"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                        >
                            <option value="">-- Sélectionner une zone --</option>
                            @foreach ($zones as $zone)
                                <option value="{{ $zone->id }}">
                                    {{ $zone->name }} — {{ number_format($zone->mission_allowance, 0, ',', ' ') }} FCFA/voyage
                                </option>
                            @endforeach
                        </select>
                        @error('zone_id')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="trip_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Date de la tournée <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            wire:model="trip_date"
                            id="trip_date"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200"
                        >
                        @error('trip_date')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea
                            wire:model="notes"
                            id="notes"
                            rows="3"
                            placeholder="Informations supplémentaires sur la tournée..."
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 transition-all duration-200 resize-none"
                        ></textarea>
                        @error('notes')
                            <p class="mt-2 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne latérale (1/3) --}}
        <div class="lg:col-span-4 space-y-6">

            {{-- Résumé zone --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Résumé zone</h3>
                </div>
                <div class="p-6">
                    @php
                        $selectedZone = $zones->firstWhere('id', $zone_id);
                    @endphp
                    @if ($selectedZone)
                        <dl class="space-y-4">
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Ville</dt>
                                <dd class="mt-1 text-sm font-semibold text-gray-900">{{ $selectedZone->city }}</dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium text-gray-500 uppercase tracking-wide">Prime de mission</dt>
                                <dd class="mt-1 text-lg font-bold text-indigo-700">
                                    {{ number_format($selectedZone->mission_allowance, 0, ',', ' ') }} FCFA/voyage
                                </dd>
                            </div>
                        </dl>
                    @else
                        <p class="text-sm text-gray-500 text-center py-4">Sélectionnez une zone pour voir les détails</p>
                    @endif
                </div>
            </div>

            {{-- Notes / Statut --}}
            @if ($trip->exists)
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Statut actuel</h3>
                    </div>
                    <div class="p-6">
                        @php $color = $trip->status->color(); @endphp
                        <span class="inline-flex items-center rounded-full px-3 py-1.5 text-sm font-medium
                            {{ $color === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}
                            {{ $color === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                            {{ $color === 'amber' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $color === 'green' ? 'bg-green-100 text-green-700' : '' }}
                        ">
                            {{ $trip->status->label() }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
