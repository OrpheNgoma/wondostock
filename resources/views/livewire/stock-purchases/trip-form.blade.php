@section('title', $trip->exists ? 'Modifier le voyage' : 'Nouveau voyage d\'achat')

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        {{ $trip->exists ? 'Modifier le voyage' : 'Nouveau voyage d\'achat' }}
                    </h1>
                    <p class="text-indigo-100 text-lg">
                        {{ $trip->exists ? 'Mettre à jour les informations du voyage' : 'Planifier un voyage d\'approvisionnement en stock' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('stock-purchases.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Retour
                    </a>
                    <button type="button" wire:click="save"
                        class="inline-flex items-center gap-2 rounded-xl bg-white text-indigo-600 px-6 py-3 text-sm font-semibold shadow-lg hover:bg-gray-50 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $trip->exists ? 'Mettre à jour' : 'Créer le voyage' }}
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
    </div>

    {{-- Bannière workflow --}}
    <div class="rounded-xl border border-indigo-200 bg-indigo-50 px-5 py-4">
        <div class="flex items-start gap-3">
            <svg class="h-5 w-5 text-indigo-500 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
            </svg>
            <div>
                <p class="text-sm font-semibold text-indigo-800 mb-2">Comment fonctionne un voyage d'achat ?</p>
                <ol class="flex flex-col sm:flex-row gap-2 sm:gap-0 sm:items-center">
                    @foreach([
                        ['Créer',   'Chauffeur, dépôt, fournisseur'],
                        ['Départ',  'Casiers vides emportés'],
                        ['Retour',  'Produits achetés + casiers pleins'],
                        ['Clôture', 'Entrée en stock confirmée'],
                    ] as $i => [$titre, $desc])
                    <li class="flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full shrink-0 text-xs font-bold
                            {{ $i === 0 ? 'bg-indigo-600 text-white' : 'bg-white text-indigo-700 ring-1 ring-indigo-300' }}">
                            {{ $i + 1 }}
                        </span>
                        <span class="text-xs text-indigo-800">
                            <strong>{{ $titre }}</strong>
                            <span class="text-indigo-500 hidden sm:inline"> — {{ $desc }}</span>
                        </span>
                    </li>
                    @if(!$loop->last)
                    <svg class="h-4 w-4 text-indigo-300 shrink-0 mx-2 hidden sm:block" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                    </svg>
                    @endif
                    @endforeach
                </ol>
            </div>
        </div>
    </div>

    {{-- Corps du formulaire --}}
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <div class="lg:col-span-8 space-y-6">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Informations du voyage</h3>
                    <p class="text-sm text-gray-600">Chauffeur, dépôt de destination et fournisseur</p>
                </div>
                <div class="p-6 space-y-6">
                    {{-- Chauffeur --}}
                    <div>
                        <label for="driver_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Chauffeur <span class="text-red-500">*</span>
                        </label>
                        @if ($drivers->isEmpty())
                            <div class="rounded-xl bg-amber-50 border border-amber-200 p-4 text-sm text-amber-700">
                                Aucun chauffeur disponible. Créez-en d'abord dans les
                                <a href="{{ route('deliveries.settings') }}" class="underline font-medium">paramètres de livraison</a>.
                            </div>
                        @else
                            <select wire:model="driver_id" id="driver_id"
                                class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
                                <option value="">-- Sélectionner un chauffeur --</option>
                                @foreach ($drivers as $driver)
                                    <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                                @endforeach
                            </select>
                        @endif
                        @error('driver_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Dépôt de destination --}}
                    <div>
                        <label for="store_id" class="block text-sm font-semibold text-gray-700 mb-2">
                            Dépôt de destination <span class="text-red-500">*</span>
                        </label>
                        <select wire:model="store_id" id="store_id"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
                            <option value="">-- Sélectionner un dépôt --</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                        <p class="mt-1.5 text-xs text-gray-500">Le stock acheté sera ajouté à ce dépôt lors de la clôture.</p>
                        @error('store_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Fournisseur --}}
                    <div>
                        <label for="supplier_id" class="block text-sm font-semibold text-gray-700 mb-2">Fournisseur</label>
                        <select wire:model="supplier_id" id="supplier_id"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
                            <option value="">-- Aucun fournisseur --</option>
                            @foreach ($suppliers as $supplier)
                                <option value="{{ $supplier->id }}">{{ $supplier->name }}</option>
                            @endforeach
                        </select>
                        @error('supplier_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Véhicule --}}
                    <div>
                        <label for="vehicle_id" class="block text-sm font-semibold text-gray-700 mb-2">Véhicule</label>
                        <select wire:model="vehicle_id" id="vehicle_id"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
                            <option value="">-- Aucun véhicule --</option>
                            @foreach ($vehicles as $vehicle)
                                <option value="{{ $vehicle->id }}">{{ $vehicle->display_name }}</option>
                            @endforeach
                        </select>
                        @error('vehicle_id') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Date --}}
                    <div>
                        <label for="trip_date" class="block text-sm font-semibold text-gray-700 mb-2">
                            Date du voyage <span class="text-red-500">*</span>
                        </label>
                        <input type="date" wire:model="trip_date" id="trip_date"
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-indigo-500 transition-all duration-200">
                        @error('trip_date') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Notes --}}
                    <div>
                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">Notes</label>
                        <textarea wire:model="notes" id="notes" rows="3"
                            placeholder="Informations supplémentaires sur le voyage..."
                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-indigo-500 transition-all duration-200 resize-none"></textarea>
                        @error('notes') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Colonne latérale --}}
        <div class="lg:col-span-4 space-y-6">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                <div class="border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Prime de déplacement</h3>
                </div>
                <div class="p-6">
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Montant fixe par voyage</p>
                    <p class="mt-1 text-2xl font-bold text-indigo-700">
                        {{ number_format($trip->mission_allowance_amount ?? \App\Models\StockPurchaseTrip::DEFAULT_MISSION_ALLOWANCE, 0, ',', ' ') }} FCFA
                    </p>
                    <p class="mt-2 text-xs text-gray-500">
                        Versée au chauffeur pour chaque voyage d'achat de stock effectué.
                    </p>
                </div>
            </div>

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
                            {{ $color === 'green' ? 'bg-green-100 text-green-700' : '' }}">
                            {{ $trip->status->label() }}
                        </span>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
