@section('title', 'Avances sur salaire')

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-violet-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-violet-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('salaries.index') }}"
                       class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">Avances sur salaire</h1>
                        <p class="text-purple-100">Chauffeurs et employés</p>
                    </div>
                </div>
                <button
                    wire:click="$toggle('showForm')"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                >
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4.5v15m7.5-7.5h-15' }}" />
                    </svg>
                    {{ $showForm ? 'Annuler' : 'Nouvelle avance' }}
                </button>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Formulaire nouvelle avance --}}
    @if ($showForm)
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Nouvelle avance</h3>
        </div>
        <form wire:submit="addAdvance" class="p-6 space-y-4">
            {{-- Type de personne --}}
            <div class="flex gap-4">
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="radio" wire:model.live="formPersonType" value="driver" class="text-purple-600 focus:ring-purple-500">
                    Chauffeur
                </label>
                <label class="flex items-center gap-2 text-sm font-medium text-gray-700 cursor-pointer">
                    <input type="radio" wire:model.live="formPersonType" value="employee" class="text-indigo-600 focus:ring-indigo-500">
                    Employé
                </label>
            </div>

            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                {{-- Sélection chauffeur ou employé --}}
                @if ($formPersonType === 'driver')
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Chauffeur <span class="text-red-500">*</span></label>
                    <select
                        wire:model="formDriverId"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <option value="">Sélectionner...</option>
                        @foreach ($drivers as $driver)
                            <option value="{{ $driver->id }}">{{ $driver->name }}</option>
                        @endforeach
                    </select>
                    @error('formDriverId')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @else
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Employé <span class="text-red-500">*</span></label>
                    <select
                        wire:model="formEmployeeId"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <option value="">Sélectionner...</option>
                        @foreach ($employees as $employee)
                            <option value="{{ $employee->id }}">{{ $employee->name }} @if($employee->position)({{ $employee->position }})@endif</option>
                        @endforeach
                    </select>
                    @error('formEmployeeId')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                @endif

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        wire:model="formAmount"
                        min="1"
                        placeholder="0"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('formAmount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date <span class="text-red-500">*</span></label>
                    <input
                        type="date"
                        wire:model="formDate"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Motif</label>
                    <input
                        type="text"
                        wire:model="formReason"
                        placeholder="Raison de l'avance..."
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                </div>
            </div>
            <div class="flex gap-2">
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    Enregistrer l'avance
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- Filtres --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Statut</label>
                <select
                    wire:model.live="filterStatus"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Tous</option>
                    @foreach ($statuses as $status)
                        <option value="{{ $status->value }}">{{ $status->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Mois</label>
                <input
                    type="month"
                    wire:model.live="filterMonth"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
            </div>
        </div>
    </div>

    {{-- Liste des avances --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Avances ({{ $advances->count() }})</h3>
        </div>

        @if ($advances->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-500">Aucune avance enregistrée.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Personne</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Motif</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($advances as $advance)
                            <tr wire:key="adv-{{ $advance->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4 text-sm font-medium text-gray-900">
                                    {{ $advance->driver?->name ?? $advance->employee?->name ?? '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    @if ($advance->driver_id)
                                        <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700">Chauffeur</span>
                                    @elseif ($advance->employee_id)
                                        <span class="inline-flex items-center rounded-full bg-indigo-50 px-2 py-0.5 text-xs font-medium text-indigo-700">
                                            {{ $advance->employee?->position ?: 'Employé' }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $advance->advance_date->format('d/m/Y') }}
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600">
                                    {{ $advance->reason ?? '—' }}
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $statusColorMap = [
                                            'yellow' => 'bg-yellow-100 text-yellow-700',
                                            'blue' => 'bg-blue-100 text-blue-700',
                                            'green' => 'bg-green-100 text-green-700',
                                            'purple' => 'bg-purple-100 text-purple-700',
                                        ];
                                        $cls = $statusColorMap[$advance->status->color()] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $cls }}">
                                        {{ $advance->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($advance->amount, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($advance->status->value === 'pending')
                                        <button
                                            wire:click="approve({{ $advance->id }})"
                                            wire:confirm="Approuver cette avance ?"
                                            type="button"
                                            class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-green-600 bg-green-50 hover:bg-green-100 transition-colors"
                                        >
                                            Approuver
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
