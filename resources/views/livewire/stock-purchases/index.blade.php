@section('title', 'Achats de stock')

<div class="space-y-8">
    {{-- En-tête avec gradient indigo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Achats de stock</h1>
                    <p class="text-indigo-100 text-lg">Voyages d'approvisionnement : casiers vides à l'aller, pleins au retour</p>
                </div>
                <div class="flex items-center gap-3">
                    <input
                        wire:model.live="dateFilter"
                        type="date"
                        class="rounded-xl bg-white/10 backdrop-blur-sm px-4 py-3 text-sm font-medium text-white shadow-lg ring-1 ring-white/20 placeholder:text-white/60 focus:bg-white/20 focus:outline-none transition-all duration-200"
                    >
                    @can('create_stock_purchases')
                    <a href="{{ route('stock-purchases.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouveau voyage
                    </a>
                    @endcan
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
    </div>

    {{-- Cartes de statistiques --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total voyages</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-indigo-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En route</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-blue-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Revenus (pleins)</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $stats['completed'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-amber-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Clôturés</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['closed'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-green-200"></div>
        </div>
        <div class="col-span-2 sm:col-span-1 overflow-hidden rounded-2xl bg-orange-50 border border-orange-100 shadow-sm p-5">
            <p class="text-xs font-medium text-orange-700 uppercase tracking-wide">Coût d'achat total</p>
            <p class="mt-2 text-xl font-bold text-orange-700">{{ number_format($stats['total_purchase_cost'], 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-orange-300"></div>
        </div>
        <div class="col-span-2 sm:col-span-1 overflow-hidden rounded-2xl bg-purple-50 border border-purple-100 shadow-sm p-5">
            <p class="text-xs font-medium text-purple-700 uppercase tracking-wide">Primes versées</p>
            <p class="mt-2 text-xl font-bold text-purple-700">{{ number_format($stats['total_allowances'], 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-purple-300"></div>
        </div>
    </div>

    {{-- Filtres de statut --}}
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $statusTabs = [
                '' => 'Tous',
                'draft' => 'Brouillon',
                'in_progress' => 'En route',
                'completed' => 'Revenu (pleins)',
                'closed' => 'Clôturé',
            ];
        @endphp
        @foreach ($statusTabs as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                type="button"
                class="inline-flex items-center rounded-xl px-4 py-2 text-sm font-medium transition-all duration-200 {{ $statusFilter === $value ? 'bg-indigo-600 text-white shadow-sm' : 'bg-white text-gray-600 ring-1 ring-gray-200 hover:bg-gray-50' }}"
            >
                {{ $label }}
            </button>
        @endforeach
    </div>

    {{-- Tableau des voyages --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Liste des voyages d'achat</h3>
            <p class="text-sm text-gray-600">{{ $trips->count() }} voyage(s) trouvé(s)</p>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Chauffeur</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Dépôt</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Fournisseur</th>
                        <th scope="col" class="px-3 py-4 text-center text-sm font-semibold text-gray-700">Vides</th>
                        <th scope="col" class="px-3 py-4 text-center text-sm font-semibold text-gray-700">Pleins</th>
                        <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Coût achat</th>
                        <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Prime</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($trips as $trip)
                        <tr wire:key="sptrip-{{ $trip->id }}" class="group hover:bg-indigo-50/40 transition-all duration-200">
                            <td class="py-4 pl-6 pr-3">
                                <span class="text-sm font-medium text-gray-900">{{ $trip->trip_date->format('d/m/Y') }}</span>
                            </td>
                            <td class="px-3 py-4"><span class="text-sm text-gray-900">{{ $trip->driver?->name ?? '—' }}</span></td>
                            <td class="px-3 py-4"><span class="text-sm text-gray-600">{{ $trip->store?->name ?? '—' }}</span></td>
                            <td class="px-3 py-4"><span class="text-sm text-gray-600">{{ $trip->supplier?->name ?? '—' }}</span></td>
                            <td class="px-3 py-4 text-center"><span class="text-sm font-medium text-gray-700">{{ $trip->empty_crates_out ?? '—' }}</span></td>
                            <td class="px-3 py-4 text-center"><span class="text-sm font-medium text-indigo-700">{{ $trip->full_crates_in ?? '—' }}</span></td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $trip->total_purchase_cost !== null ? number_format($trip->total_purchase_cost, 0, ',', ' ') . ' FCFA' : '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-sm text-gray-600">{{ number_format($trip->mission_allowance_amount ?? 0, 0, ',', ' ') }} FCFA</span>
                            </td>
                            <td class="px-3 py-4">
                                @php $color = $trip->status->color(); @endphp
                                <span class="inline-flex items-center rounded-full px-2.5 py-1 text-xs font-medium
                                    {{ $color === 'gray' ? 'bg-gray-100 text-gray-700' : '' }}
                                    {{ $color === 'blue' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ $color === 'amber' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $color === 'green' ? 'bg-green-100 text-green-700' : '' }}
                                ">
                                    {{ $trip->status->label() }}
                                </span>
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right">
                                <a href="{{ route('stock-purchases.show', $trip) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200">
                                    Gérer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 1 0-7.5 0v4.5m11.356-1.993 1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 0 1-1.12-1.243l1.264-12A1.125 1.125 0 0 1 5.513 7.5h12.974c.576 0 1.059.435 1.119 1.007Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Aucun voyage d'achat</p>
                                        <p class="text-sm text-gray-500 mt-1">Commencez par créer votre premier voyage d'approvisionnement</p>
                                    </div>
                                    @can('create_stock_purchases')
                                    <a href="{{ route('stock-purchases.create') }}"
                                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Nouveau voyage
                                    </a>
                                    @endcan
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
