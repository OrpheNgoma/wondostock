@section('title', 'Dépenses')

<div x-data="{ showPanel: @entangle('showPanel') }" class="space-y-8">
    {{-- En-tête avec gradient indigo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Dépenses</h1>
                    <p class="text-indigo-100 text-lg">Suivez et gérez toutes vos dépenses</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('expenses.settings') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Catégories
                    </a>
                    <button
                        wire:click="exportCsv"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        CSV
                    </button>
                    <button
                        wire:click="openCreate"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                    >
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouvelle dépense
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Stats du mois --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Total mois</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ number_format($stats['month_total'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-indigo-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-red-50 border border-red-100 shadow-sm p-5">
            <p class="text-xs font-medium text-red-700 uppercase tracking-wide">Charges fixes</p>
            <p class="mt-2 text-2xl font-bold text-red-700">{{ number_format($stats['fixed_total'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-red-300"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-orange-50 border border-orange-100 shadow-sm p-5">
            <p class="text-xs font-medium text-orange-700 uppercase tracking-wide">Charges variables</p>
            <p class="mt-2 text-2xl font-bold text-orange-700">{{ number_format($stats['variable_total'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-orange-300"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Nb dépenses</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ $stats['count'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-gray-200"></div>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-6">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Recherche</label>
                <input
                    type="text"
                    wire:model.live="search"
                    placeholder="Libellé..."
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Du</label>
                <input
                    type="date"
                    wire:model.live="dateFrom"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Au</label>
                <input
                    type="date"
                    wire:model.live="dateTo"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Boutique</label>
                <select
                    wire:model.live="storeFilter"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Toutes</option>
                    @foreach ($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Catégorie</label>
                <select
                    wire:model.live="categoryFilter"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Toutes</option>
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Type</label>
                <select
                    wire:model.live="typeFilter"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Tous</option>
                    <option value="fixed">Fixe</option>
                    <option value="variable">Variable</option>
                </select>
            </div>
        </div>
    </div>

    {{-- Table des dépenses --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Liste des dépenses</h3>
        </div>

        @if ($expenses->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">Aucune dépense trouvée.</p>
                <button wire:click="openCreate" type="button" class="mt-3 inline-block text-sm font-medium text-indigo-600 hover:text-indigo-700">
                    Ajouter une dépense
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Libellé</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Agent</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Boutique</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catégorie</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Récurrence</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($expenses as $expense)
                            <tr wire:key="exp-{{ $expense->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm text-gray-900 whitespace-nowrap">
                                    {{ $expense->expense_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $expense->label }}
                                    @if ($expense->receipt_number)
                                        <p class="text-xs text-gray-400">Reçu: {{ $expense->receipt_number }}</p>
                                    @endif
                                    @if ($expense->notes)
                                        <p class="text-xs text-gray-500 truncate max-w-xs">{{ $expense->notes }}</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $expense->agent ?: '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $expense->store?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @if ($expense->category)
                                        <div class="flex items-center gap-2">
                                            @if ($expense->category->color)
                                                <div class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: {{ $expense->category->color }}"></div>
                                            @endif
                                            <span>{{ $expense->category->name }}</span>
                                        </div>
                                        <span class="text-xs text-gray-400">{{ $expense->category->type === 'fixed' ? 'Fixe' : 'Variable' }}</span>
                                    @else
                                        <span class="text-gray-400">—</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    @php
                                        $recurrenceLabels = ['once' => 'Ponctuel', 'monthly' => 'Mensuel', 'weekly' => 'Hebdomadaire'];
                                    @endphp
                                    {{ $recurrenceLabels[$expense->recurrence] ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">
                                    {{ number_format($expense->amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            wire:click="openEdit({{ $expense->id }})"
                                            type="button"
                                            class="rounded-lg p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                            </svg>
                                        </button>
                                        <button
                                            wire:click="delete({{ $expense->id }})"
                                            wire:confirm="Supprimer cette dépense ?"
                                            type="button"
                                            class="rounded-lg p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="px-6 py-4 border-t border-gray-100">
                {{ $expenses->links() }}
            </div>
        @endif
    </div>

    {{-- Panneau slide-over --}}
    <div x-show="showPanel" x-cloak class="relative z-50">
        <div
            x-show="showPanel"
            x-transition:enter="ease-in-out duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="ease-in-out duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 bg-gray-500/75 transition-opacity"
        ></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showPanel = false; $wire.closePanel()" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div
                        x-show="showPanel"
                        x-transition:enter="transform transition ease-in-out duration-500"
                        x-transition:enter-start="translate-x-full"
                        x-transition:enter-end="translate-x-0"
                        x-transition:leave="transform transition ease-in-out duration-500"
                        x-transition:leave-start="translate-x-0"
                        x-transition:leave-end="translate-x-full"
                        class="pointer-events-auto w-screen max-w-lg"
                    >
                        <form wire:submit="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            {{-- En-tête --}}
                            <div class="flex items-start justify-between px-6 py-5">
                                <h2 class="text-base font-semibold text-gray-900">
                                    {{ $editingExpenseId ? 'Modifier la dépense' : 'Nouvelle dépense' }}
                                </h2>
                                <button
                                    @click="showPanel = false; $wire.closePanel()"
                                    type="button"
                                    class="rounded-md text-gray-400 hover:text-gray-500 focus:outline-none"
                                >
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            {{-- Corps --}}
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-auto">
                                <div class="flex-1 px-6 py-6 space-y-5">

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            wire:model="label"
                                            placeholder="Ex: Loyer boutique..."
                                            class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                        >
                                        @error('label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                                            <input
                                                type="number"
                                                wire:model="amount"
                                                min="1"
                                                placeholder="0"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                            @error('amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                                            <input
                                                type="date"
                                                wire:model="expense_date"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                            @error('expense_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                                            <select
                                                wire:model="category_id"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                                <option value="">— Aucune —</option>
                                                @foreach ($categories as $cat)
                                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                                @endforeach
                                            </select>
                                            @error('category_id') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Récurrence</label>
                                            <select
                                                wire:model="recurrence"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                                <option value="once">Ponctuel</option>
                                                <option value="monthly">Mensuel</option>
                                                <option value="weekly">Hebdomadaire</option>
                                            </select>
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Boutique</label>
                                        <select
                                            wire:model="store_id"
                                            class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                        >
                                            <option value="">— Aucune —</option>
                                            @foreach ($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Agent</label>
                                            <select
                                                wire:model="agent"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                                <option value="">— Aucun —</option>
                                                @foreach ($employees as $employee)
                                                    <option value="{{ $employee->name }}">{{ $employee->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">N° reçu</label>
                                            <input
                                                type="text"
                                                wire:model="receipt_number"
                                                placeholder="Ex: REC-001..."
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                        </div>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea
                                            wire:model="notes"
                                            rows="3"
                                            placeholder="Détails supplémentaires..."
                                            class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                        ></textarea>
                                    </div>

                                </div>
                            </div>

                            {{-- Pied --}}
                            <div class="flex shrink-0 justify-end gap-3 px-6 py-4">
                                <button
                                    @click="showPanel = false; $wire.closePanel()"
                                    type="button"
                                    class="inline-flex items-center rounded-lg bg-white px-4 py-2.5 text-sm font-medium text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-colors"
                                >
                                    Annuler
                                </button>
                                <button
                                    type="submit"
                                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 transition-colors"
                                >
                                    <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="save">
                                        {{ $editingExpenseId ? 'Mettre à jour' : 'Enregistrer' }}
                                    </span>
                                    <span wire:loading wire:target="save">Enregistrement...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
