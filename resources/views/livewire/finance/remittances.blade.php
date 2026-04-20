@section('title', 'Versements DG')

<div class="space-y-8">
    {{-- En-tête gradient orange --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500 to-amber-600 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/20 to-amber-600/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Versements DG</h1>
                    <p class="text-orange-100 text-lg">Enregistrement des versements au directeur général</p>
                </div>
                <div>
                    <button
                        type="button"
                        wire:click="setShowForm(true)"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouveau versement
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="overflow-hidden rounded-2xl bg-orange-50 border border-orange-100 shadow-sm p-5">
            <p class="text-xs font-medium text-orange-700 uppercase tracking-wide">Total versé ce mois</p>
            <p class="mt-2 text-2xl font-bold text-orange-700">{{ number_format($monthStats['total'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-orange-300"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nb versements</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $monthStats['count'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-gray-200"></div>
        </div>
        @foreach ($monthStats['by_store']->take(2) as $storeStat)
            <div wire:key="stat-store-{{ $loop->index }}" class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide truncate">{{ $storeStat['store']?->name ?? 'Boutique' }}</p>
                <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($storeStat['total'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
                <div class="mt-2 h-1 w-8 rounded-full bg-amber-200"></div>
            </div>
        @endforeach
    </div>

    {{-- Formulaire --}}
    @if ($showForm)
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-orange-200">
            <div class="border-b border-gray-100 bg-gradient-to-r from-orange-50 to-white px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Enregistrer un versement DG</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Boutique <span class="text-red-500">*</span></label>
                        <select
                            wire:model="form_store_id"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                        >
                            <option value="">Sélectionner une boutique</option>
                            @foreach ($stores as $store)
                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                            @endforeach
                        </select>
                        @error('form_store_id')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Reçu par <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            wire:model="form_received_by"
                            placeholder="Nom du récepteur..."
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                        >
                        @error('form_received_by')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                        <input
                            type="number"
                            wire:model="form_amount"
                            min="1"
                            placeholder="0"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                        >
                        @error('form_amount')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input
                            type="date"
                            wire:model="form_remittance_date"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                        >
                        @error('form_remittance_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">N° Reçu</label>
                        <input
                            type="text"
                            wire:model="form_reference"
                            placeholder="Numéro de reçu..."
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                        >
                        @error('form_reference')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                        <textarea
                            wire:model="form_notes"
                            rows="1"
                            placeholder="Informations complémentaires..."
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                        ></textarea>
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="save"
                        class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors"
                    >
                        Enregistrer le versement
                    </button>
                    <button
                        type="button"
                        wire:click="setShowForm(false)"
                        class="px-4 py-2 bg-white text-gray-700 text-sm font-semibold rounded-lg ring-1 ring-gray-300 hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Filtres --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Nom récepteur..."
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Boutique</label>
                <select
                    wire:model.live="storeFilter"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                >
                    <option value="">Toutes</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Mois</label>
                <input
                    type="month"
                    wire:model.live="monthFilter"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-orange-500"
                >
            </div>
        </div>
    </div>

    {{-- Tableau versements --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Liste des versements</h3>
        </div>

        @if ($remittances->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">Aucun versement pour cette période.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Boutique</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Versé par</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Reçu par</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Reçu</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($remittances as $remittance)
                            <tr wire:key="rem-{{ $remittance->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $remittance->remittance_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $remittance->store?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $remittance->user?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $remittance->received_by }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-orange-700 text-right whitespace-nowrap">
                                    {{ number_format($remittance->amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $remittance->reference ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button
                                        type="button"
                                        wire:click="delete({{ $remittance->id }})"
                                        wire:confirm="Supprimer ce versement ?"
                                        class="rounded-lg p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                    >
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                        </svg>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-orange-50">
                            <td colspan="4" class="px-6 py-3 text-sm font-semibold text-gray-700">Total</td>
                            <td class="px-6 py-3 text-sm font-bold text-orange-700 text-right whitespace-nowrap">
                                {{ number_format($monthStats['total'], 0, ',', ' ') }} FCFA
                            </td>
                            <td colspan="2"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
