@section('title', 'Gestion de la Caisse')

<div class="space-y-8">
    {{-- En-tête gradient teal/emerald --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-teal-600/20 to-emerald-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Gestion de la Caisse</h1>
                    <p class="text-teal-100 text-lg">Suivi des sessions de caisse journalières par boutique</p>
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
                        Nouvelle session
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Solde moyen</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($monthStats['avg_closing'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-teal-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-teal-50 border border-teal-100 shadow-sm p-5">
            <p class="text-xs font-medium text-teal-700 uppercase tracking-wide">Total encaissements</p>
            <p class="mt-2 text-2xl font-bold text-teal-700">{{ number_format($monthStats['total_cash_in'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-teal-300"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-red-50 border border-red-100 shadow-sm p-5">
            <p class="text-xs font-medium text-red-700 uppercase tracking-wide">Total sorties</p>
            <p class="mt-2 text-2xl font-bold text-red-700">{{ number_format($monthStats['total_cash_out'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-red-300"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-orange-50 border border-orange-100 shadow-sm p-5">
            <p class="text-xs font-medium text-orange-700 uppercase tracking-wide">Total versements DG</p>
            <p class="mt-2 text-2xl font-bold text-orange-700">{{ number_format($monthStats['total_remittances'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-orange-300"></div>
        </div>
    </div>

    {{-- Formulaire nouvelle session --}}
    @if ($showForm)
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-teal-200">
            <div class="border-b border-gray-100 bg-gradient-to-r from-teal-50 to-white px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Ouvrir une nouvelle session de caisse</h3>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Boutique <span class="text-red-500">*</span></label>
                        <select
                            wire:model="form_store_id"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
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
                        <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                        <input
                            type="date"
                            wire:model="form_session_date"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                        >
                        @error('form_session_date')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Solde d'ouverture (FCFA)</label>
                        <input
                            type="number"
                            wire:model="form_opening_balance"
                            min="0"
                            placeholder="0"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                        >
                        @error('form_opening_balance')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
                <div class="mt-6 flex items-center gap-3">
                    <button
                        type="button"
                        wire:click="openSession"
                        class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition-colors"
                    >
                        Ouvrir la session
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
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Boutique</label>
                <select
                    wire:model.live="selectedStore"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                >
                    <option value="">Toutes les boutiques</option>
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
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                >
            </div>
        </div>
    </div>

    {{-- Tableau sessions --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Sessions de caisse</h3>
        </div>

        @if ($sessions->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">Aucune session de caisse pour cette période.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Boutique</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Ouverture</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Encaissements</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Sorties</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Versements DG</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Solde fermeture</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($sessions as $session)
                            <tr wire:key="session-{{ $session->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $session->session_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $session->store?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right whitespace-nowrap">
                                    {{ number_format($session->opening_balance, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-teal-700 text-right whitespace-nowrap">
                                    {{ number_format($session->cash_in, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-red-700 text-right whitespace-nowrap">
                                    {{ number_format($session->cash_out, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-orange-700 text-right whitespace-nowrap">
                                    {{ number_format($session->remittances, 0, ',', ' ') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right whitespace-nowrap">
                                    {{ number_format($session->closing_balance, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4">
                                    @if ($session->isOpen())
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ouverte</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">Clôturée</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('caisse.session.show', $session) }}"
                                            class="rounded-lg p-1.5 text-gray-400 hover:text-teal-600 hover:bg-teal-50 transition-colors"
                                            title="Voir le détail"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.964-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </a>
                                        @if ($session->isOpen())
                                            <button
                                                type="button"
                                                wire:click="closeSession({{ $session->id }})"
                                                wire:confirm="Clôturer cette session de caisse ?"
                                                class="rounded-lg p-1.5 text-gray-400 hover:text-orange-600 hover:bg-orange-50 transition-colors"
                                                title="Clôturer"
                                            >
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                                                </svg>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
