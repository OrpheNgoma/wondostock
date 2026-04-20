@section('title', isset($employee) && $employee?->exists ? 'Modifier l\'employé' : 'Nouvel employé')

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-purple-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-purple-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-center gap-4">
                <a href="{{ route('employees.index') }}"
                   wire:navigate
                   class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">
                        {{ isset($employee) && $employee?->exists ? 'Modifier l\'employé' : 'Nouvel employé' }}
                    </h1>
                    <p class="text-indigo-100">Personnel sédentaire non-chauffeur</p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
    </div>

    {{-- Formulaire --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Informations de l'employé</h3>
        </div>
        <form wire:submit="save" class="p-6 space-y-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nom complet <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Prénom et nom"
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('name')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Poste / Fonction</label>
                    <input
                        type="text"
                        wire:model="position"
                        placeholder="Ex: Caissière, Responsable boutique, Agent..."
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('position')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Salaire de base <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input
                            type="number"
                            wire:model="base_salary"
                            placeholder="0"
                            min="0"
                            class="block w-full rounded-lg border-0 py-2.5 pl-3 pr-16 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        >
                        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-3">
                            <span class="text-xs text-gray-400">FCFA</span>
                        </div>
                    </div>
                    @error('base_salary')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Téléphone</label>
                    <input
                        type="text"
                        wire:model="phone"
                        placeholder="Ex: 074 00 00 00"
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('phone')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="nom@exemple.com"
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('email')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Date d'embauche</label>
                    <input
                        type="date"
                        wire:model="hire_date"
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('hire_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Informations complémentaires..."
                        class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    ></textarea>
                </div>

                <div class="sm:col-span-2">
                    <label class="flex items-center gap-3 cursor-pointer select-none">
                        <input type="checkbox" wire:model="is_active" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="text-sm font-medium text-gray-700">Employé actif</span>
                        <span class="text-xs text-gray-400">(seuls les actifs apparaissent dans la génération de bulletins)</span>
                    </label>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a
                    href="{{ route('employees.index') }}"
                    wire:navigate
                    class="px-4 py-2 text-sm font-medium text-gray-700 bg-white rounded-lg ring-1 ring-gray-300 hover:bg-gray-50 transition-colors"
                >
                    Annuler
                </a>
                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    class="px-6 py-2 text-sm font-semibold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-50"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ isset($employee) && $employee?->exists ? 'Enregistrer les modifications' : 'Créer l\'employé' }}
                    </span>
                    <span wire:loading wire:target="save">Enregistrement...</span>
                </button>
            </div>
        </form>
    </div>
</div>
