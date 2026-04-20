@section('title', $expense ? 'Modifier la dépense' : 'Nouvelle dépense')

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-center gap-4">
                <a href="{{ route('expenses.index') }}"
                   class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">
                        {{ $expense ? 'Modifier la dépense' : 'Nouvelle dépense' }}
                    </h1>
                    <p class="text-indigo-100">{{ $expense ? 'Modifier les informations de la dépense' : 'Enregistrer une nouvelle dépense' }}</p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Formulaire --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Informations de la dépense</h3>
        </div>

        <form wire:submit="save" class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Libellé <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="text"
                        wire:model="label"
                        placeholder="Ex: Loyer janvier, Facture internet..."
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('label')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Montant (FCFA) <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="number"
                        wire:model="amount"
                        min="0"
                        placeholder="0"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('amount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">
                        Date <span class="text-red-500">*</span>
                    </label>
                    <input
                        type="date"
                        wire:model="expense_date"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('expense_date')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Catégorie</label>
                    <select
                        wire:model="category_id"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <option value="">Sans catégorie</option>
                        @foreach ($categories->groupBy('type') as $type => $catGroup)
                            <optgroup label="{{ $type === 'fixed' ? 'Charges fixes' : 'Charges variables' }}">
                                @foreach ($catGroup as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    @error('category_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Récurrence</label>
                    <select
                        wire:model="recurrence"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <option value="once">Ponctuel (une seule fois)</option>
                        <option value="monthly">Mensuel</option>
                        <option value="weekly">Hebdomadaire</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Boutique</label>
                    <select
                        wire:model="store_id"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <option value="">Toutes / Non spécifiée</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                    @error('store_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Agent / Responsable</label>
                    <input
                        type="text"
                        wire:model="agent"
                        placeholder="Nom de l'agent..."
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('agent')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">N° Reçu</label>
                    <input
                        type="text"
                        wire:model="receipt_number"
                        placeholder="Numéro du reçu..."
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('receipt_number')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Notes</label>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Informations complémentaires..."
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    ></textarea>
                </div>
            </div>

            <div class="mt-6 flex items-center gap-3">
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    {{ $expense ? 'Enregistrer les modifications' : 'Créer la dépense' }}
                </button>
                <a
                    href="{{ route('expenses.index') }}"
                    class="px-4 py-2 bg-white text-gray-700 text-sm font-semibold rounded-lg ring-1 ring-gray-300 hover:bg-gray-50 transition-colors"
                >
                    Annuler
                </a>
            </div>
        </form>
    </div>
</div>
