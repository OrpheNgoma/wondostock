@section('title', 'Tournées de livraison')

<div class="space-y-8">
    {{-- En-tête avec gradient indigo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Tournées de livraison</h1>
                    <p class="text-indigo-100 text-lg">Suivez et gérez toutes vos tournées de livraison</p>
                </div>
                <div class="flex items-center gap-3">
                    <input
                        wire:model.live="dateFilter"
                        type="date"
                        class="rounded-xl bg-white/10 backdrop-blur-sm px-4 py-3 text-sm font-medium text-white shadow-lg ring-1 ring-white/20 placeholder:text-white/60 focus:bg-white/20 focus:outline-none transition-all duration-200"
                    >
                    <a href="{{ route('deliveries.create') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouvelle tournée
                    </a>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
    </div>

    {{-- Cartes de statistiques --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        {{-- Total tournées --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total tournées</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-indigo-200"></div>
        </div>

        {{-- En route --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">En route</p>
            <p class="mt-2 text-3xl font-bold text-blue-600">{{ $stats['in_progress'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-blue-200"></div>
        </div>

        {{-- Retournés --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Retournés</p>
            <p class="mt-2 text-3xl font-bold text-amber-600">{{ $stats['completed'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-amber-200"></div>
        </div>

        {{-- Clôturées --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Clôturées</p>
            <p class="mt-2 text-3xl font-bold text-green-600">{{ $stats['closed'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-green-200"></div>
        </div>

        {{-- Recette totale --}}
        <div class="col-span-2 sm:col-span-1 overflow-hidden rounded-2xl bg-green-50 border border-green-100 shadow-sm p-5">
            <p class="text-xs font-medium text-green-700 uppercase tracking-wide">Recette totale</p>
            <p class="mt-2 text-xl font-bold text-green-700">{{ number_format($stats['total_revenue'], 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-green-300"></div>
        </div>

        {{-- Commissions --}}
        <div class="col-span-2 sm:col-span-1 overflow-hidden rounded-2xl bg-purple-50 border border-purple-100 shadow-sm p-5">
            <p class="text-xs font-medium text-purple-700 uppercase tracking-wide">Commissions</p>
            <p class="mt-2 text-xl font-bold text-purple-700">{{ number_format($stats['total_commissions'], 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-purple-300"></div>
        </div>
    </div>

    {{-- Filtres de statut --}}
    <div class="flex items-center gap-2 flex-wrap">
        @php
            $statusTabs = [
                '' => 'Toutes',
                'draft' => 'Brouillon',
                'in_progress' => 'En route',
                'completed' => 'Retourné',
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

    {{-- Tableau des tournées --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Liste des tournées</h3>
                    <p class="text-sm text-gray-600">{{ $trips->count() }} tournée(s) trouvée(s)</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">Date</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Chauffeur</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Véhicule</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Zone</th>
                        <th scope="col" class="px-3 py-4 text-center text-sm font-semibold text-gray-700">Chargés</th>
                        <th scope="col" class="px-3 py-4 text-center text-sm font-semibold text-gray-700">Retournés</th>
                        <th scope="col" class="px-3 py-4 text-center text-sm font-semibold text-gray-700">Vendus</th>
                        <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Recette</th>
                        <th scope="col" class="px-3 py-4 text-right text-sm font-semibold text-gray-700">Commission</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6"><span class="sr-only">Actions</span></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($trips as $trip)
                        <tr wire:key="trip-{{ $trip->id }}" class="group hover:bg-indigo-50/40 transition-all duration-200">
                            <td class="py-4 pl-6 pr-3">
                                <span class="text-sm font-medium text-gray-900">
                                    {{ $trip->trip_date->format('d/m/Y') }}
                                </span>
                            </td>
                            <td class="px-3 py-4">
                                <span class="text-sm text-gray-900">{{ $trip->driver?->name ?? '—' }}</span>
                            </td>
                            <td class="px-3 py-4">
                                <span class="text-sm text-gray-600">{{ $trip->vehicle?->plate_number ?? '—' }}</span>
                            </td>
                            <td class="px-3 py-4">
                                <span class="text-sm text-gray-600">{{ $trip->zone?->name ?? '—' }}</span>
                            </td>
                            <td class="px-3 py-4 text-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $trip->loaded_crates ?? '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-center">
                                <span class="text-sm font-medium text-gray-700">
                                    {{ $trip->returned_crates ?? '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-center">
                                @if ($trip->returned_crates !== null)
                                    <span class="text-sm font-medium text-indigo-700">
                                        {{ $trip->sold_crates }}
                                    </span>
                                @else
                                    <span class="text-sm text-gray-400">—</span>
                                @endif
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-sm font-semibold text-gray-900">
                                    {{ $trip->total_revenue !== null ? number_format($trip->total_revenue, 0, ',', ' ') . ' FCFA' : '—' }}
                                </span>
                            </td>
                            <td class="px-3 py-4 text-right">
                                <span class="text-sm text-gray-600">
                                    {{ $trip->commission_amount !== null ? number_format($trip->commission_amount, 0, ',', ' ') . ' FCFA' : '—' }}
                                </span>
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
                                <a href="{{ route('deliveries.show', $trip) }}"
                                   class="inline-flex items-center gap-1 rounded-lg bg-indigo-50 px-3 py-2 text-xs font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200">
                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Gérer
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="h-16 w-16 rounded-full bg-indigo-50 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-indigo-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 00-3.213-9.193 2.056 2.056 0 00-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 00-10.026 0 1.106 1.106 0 00-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Aucune tournée</p>
                                        <p class="text-sm text-gray-500 mt-1">Commencez par créer votre première tournée de livraison</p>
                                    </div>
                                    <a href="{{ route('deliveries.create') }}"
                                       class="inline-flex items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Nouvelle tournée
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
