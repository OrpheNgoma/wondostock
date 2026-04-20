@section('title', 'Paramètres Dépenses')

<div class="space-y-8">
    {{-- En-tête avec gradient indigo --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-indigo-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-indigo-600/20 to-indigo-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Catégories de dépenses</h1>
                    <p class="text-indigo-100 text-lg">Gérez vos catégories de dépenses fixes et variables</p>
                </div>
                <div class="flex items-center gap-2">
                    <button
                        wire:click="setTab('fixed')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200
                            {{ $activeTab === 'fixed' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20' }}"
                    >
                        Charges fixes
                    </button>
                    <button
                        wire:click="setTab('variable')"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl px-5 py-2.5 text-sm font-semibold transition-all duration-200
                            {{ $activeTab === 'variable' ? 'bg-white text-indigo-700 shadow-sm' : 'bg-white/10 text-white ring-1 ring-white/20 hover:bg-white/20' }}"
                    >
                        Charges variables
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Contenu principal --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $activeTab === 'fixed' ? 'Charges fixes' : 'Charges variables' }}
                    </h3>
                    <p class="text-sm text-gray-600">
                        {{ $activeTab === 'fixed' ? $fixedCategories->count() : $variableCategories->count() }} catégorie(s)
                    </p>
                </div>
                <button
                    wire:click="$toggle('showForm')"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-xl bg-indigo-50 px-4 py-2 text-sm font-medium text-indigo-700 hover:bg-indigo-100 transition-colors duration-200"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="{{ $showForm ? 'M6 18L18 6M6 6l12 12' : 'M12 4.5v15m7.5-7.5h-15' }}" />
                    </svg>
                    {{ $showForm ? 'Annuler' : 'Nouvelle catégorie' }}
                </button>
            </div>
        </div>

        {{-- Formulaire --}}
        @if ($showForm)
            <div class="border-b border-indigo-100 bg-indigo-50/50 p-6">
                <h4 class="text-sm font-semibold text-gray-800 mb-4">
                    {{ $editingId ? 'Modifier la catégorie' : 'Nouvelle catégorie' }}
                </h4>
                <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">
                            Nom <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="form.name"
                            placeholder="Ex: Loyer, Internet..."
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        >
                        @error('form.name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                        <select
                            wire:model="form.type"
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        >
                            <option value="fixed">Charge fixe</option>
                            <option value="variable">Charge variable</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Couleur</label>
                        <input
                            type="color"
                            wire:model="form.color"
                            class="block h-10 w-full rounded-lg border-0 py-1 px-2 text-sm bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        >
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Icône (optionnel)</label>
                        <input
                            type="text"
                            wire:model="form.icon"
                            placeholder="Ex: home, wifi..."
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                        >
                    </div>
                </div>
                <div class="mt-4 flex gap-2">
                    <button
                        wire:click="save"
                        type="button"
                        class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        Enregistrer
                    </button>
                    <button
                        wire:click="resetForm"
                        type="button"
                        class="px-4 py-2 bg-white text-gray-700 text-sm font-semibold rounded-lg ring-1 ring-gray-300 hover:bg-gray-50 transition-colors"
                    >
                        Annuler
                    </button>
                </div>
            </div>
        @endif

        {{-- Liste des catégories --}}
        @php $categories = $activeTab === 'fixed' ? $fixedCategories : $variableCategories; @endphp

        @if ($categories->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25z" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">Aucune catégorie de type {{ $activeTab === 'fixed' ? 'fixe' : 'variable' }}.</p>
                <button wire:click="$set('showForm', true)" type="button" class="mt-3 text-sm font-medium text-indigo-600 hover:text-indigo-700">
                    Ajouter une catégorie
                </button>
            </div>
        @else
            <ul class="divide-y divide-gray-100">
                @foreach ($categories as $category)
                    <li wire:key="cat-{{ $category->id }}" class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center gap-3">
                            @if ($category->color)
                                <div class="h-4 w-4 rounded-full shrink-0" style="background-color: {{ $category->color }}"></div>
                            @endif
                            <div>
                                <p class="text-sm font-medium text-gray-900">{{ $category->name }}</p>
                                <p class="text-xs text-gray-500">{{ $category->type === 'fixed' ? 'Charge fixe' : 'Charge variable' }}</p>
                            </div>
                        </div>
                        @if (!$category->is_system)
                            <div class="flex items-center gap-2">
                                <button
                                    wire:click="edit({{ $category->id }})"
                                    type="button"
                                    class="rounded-lg p-1.5 text-gray-400 hover:text-indigo-600 hover:bg-indigo-50 transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125"/>
                                    </svg>
                                </button>
                                <button
                                    wire:click="delete({{ $category->id }})"
                                    wire:confirm="Supprimer cette catégorie ?"
                                    type="button"
                                    class="rounded-lg p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                >
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0"/>
                                    </svg>
                                </button>
                            </div>
                        @else
                            <span class="text-xs text-gray-400 italic">Système</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
