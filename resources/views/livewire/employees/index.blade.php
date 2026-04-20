@section('title', 'Employés')

<div x-data="{ showPanel: @entangle('showPanel') }" class="space-y-8">
    {{-- En-tête gradient indigo/violet --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-purple-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Gestion des employés</h1>
                    <p class="text-indigo-100 text-lg">Personnel sédentaire — caissières, responsables, agents</p>
                </div>
                <button
                    wire:click="openCreate"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nouvel employé
                </button>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Total employés</p>
            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $stats['total'] }}</p>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Actifs</p>
            <p class="mt-1 text-3xl font-bold text-indigo-700">{{ $stats['active'] }}</p>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200 p-6">
            <p class="text-sm font-medium text-gray-500">Masse salariale mensuelle</p>
            <p class="mt-1 text-3xl font-bold text-purple-700">{{ number_format($stats['total_base'], 0, ',', ' ') }} FCFA</p>
        </div>
    </div>

    {{-- Filtres --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200 p-4">
        <div class="flex flex-col sm:flex-row gap-4 items-center">
            <div class="relative flex-1">
                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher par nom ou poste..."
                    class="block w-full rounded-lg border-0 py-2 pl-10 pr-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
            </div>
            <label class="flex items-center gap-2 text-sm text-gray-700 cursor-pointer select-none">
                <input type="checkbox" wire:model.live="showInactive" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                Afficher les inactifs
            </label>
        </div>
    </div>

    {{-- Tableau --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        @if ($employees->isEmpty())
            <div class="px-6 py-16 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">Aucun employé trouvé.</p>
                <button wire:click="openCreate" type="button" class="mt-4 inline-flex items-center gap-1 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Ajouter le premier employé
                </button>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Nom</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Poste</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Contact</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date d'embauche</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Salaire de base</th>
                            <th class="px-6 py-3 text-center text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                            <th class="px-6 py-3"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @foreach ($employees as $employee)
                            <tr wire:key="emp-{{ $employee->id }}" class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }} hover:bg-indigo-50/40 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-9 w-9 items-center justify-center rounded-full bg-indigo-100 text-indigo-700 font-semibold text-sm shrink-0">
                                            {{ strtoupper(substr($employee->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-semibold text-gray-900">{{ $employee->name }}</div>
                                            @if ($employee->notes)
                                                <div class="text-xs text-gray-400 truncate max-w-xs">{{ $employee->notes }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">{{ $employee->position ?: '—' }}</td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    @if ($employee->phone)
                                        <div>{{ $employee->phone }}</div>
                                    @endif
                                    @if ($employee->email)
                                        <div class="text-xs text-gray-400">{{ $employee->email }}</div>
                                    @endif
                                    @if (! $employee->phone && ! $employee->email)
                                        —
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700">
                                    {{ $employee->hire_date?->format('d/m/Y') ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right whitespace-nowrap">
                                    {{ number_format($employee->base_salary, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if ($employee->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Actif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-600">Inactif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            wire:click="openEdit({{ $employee->id }})"
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-indigo-700 bg-indigo-50 hover:bg-indigo-100 transition-colors"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125" />
                                            </svg>
                                            Modifier
                                        </button>
                                        <button
                                            wire:click="toggleActive({{ $employee->id }})"
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium {{ $employee->is_active ? 'text-amber-700 bg-amber-50 hover:bg-amber-100' : 'text-green-700 bg-green-50 hover:bg-green-100' }} transition-colors"
                                        >
                                            {{ $employee->is_active ? 'Désactiver' : 'Réactiver' }}
                                        </button>
                                        <button
                                            wire:click="delete({{ $employee->id }})"
                                            wire:confirm="Supprimer cet employé ?"
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-medium text-red-700 bg-red-50 hover:bg-red-100 transition-colors"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
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
                        class="pointer-events-auto w-screen max-w-md"
                    >
                        <form wire:submit="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            {{-- En-tête --}}
                            <div class="flex items-start justify-between px-6 py-5">
                                <h2 class="text-base font-semibold text-gray-900">
                                    {{ $editingEmployeeId ? 'Modifier l\'employé' : 'Nouvel employé' }}
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
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                                        <input
                                            type="text"
                                            wire:model="name"
                                            placeholder="Prénom et nom..."
                                            class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                        >
                                        @error('name') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Poste</label>
                                        <input
                                            type="text"
                                            wire:model="position"
                                            placeholder="Ex: Caissière, Responsable..."
                                            class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                        >
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                                            <input
                                                type="text"
                                                wire:model="phone"
                                                placeholder="Ex: +242 06..."
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                            @error('phone') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                                            <input
                                                type="email"
                                                wire:model="email"
                                                placeholder="email@exemple.com"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                            @error('email') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                                            <input
                                                type="date"
                                                wire:model="hire_date"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                            @error('hire_date') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-1">Salaire de base (FCFA) <span class="text-red-500">*</span></label>
                                            <input
                                                type="number"
                                                wire:model="base_salary"
                                                min="0"
                                                placeholder="0"
                                                class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                                            >
                                            @error('base_salary') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                                        </div>
                                    </div>

                                    <div>
                                        <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer select-none">
                                            <input
                                                type="checkbox"
                                                wire:model="is_active"
                                                class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                            >
                                            Employé actif
                                        </label>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                                        <textarea
                                            wire:model="notes"
                                            rows="3"
                                            placeholder="Informations complémentaires..."
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
                                        {{ $editingEmployeeId ? 'Mettre à jour' : 'Enregistrer' }}
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
