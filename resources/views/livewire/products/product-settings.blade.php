<div>
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Paramètres des Produits</h2>
            <p class="mt-1 text-sm text-gray-500">Gérez les catégories et les taxes pour votre catalogue de produits.</p>
        </div>
    </div>

    <div class="mt-8 grid grid-cols-1 gap-12 lg:grid-cols-2">
        <!-- Section Catégories -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Catégories</h3>
                <button wire:click="createCategory" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Nouvelle Catégorie
                </button>
            </div>
            <div class="flow-root">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <ul role="list" class="divide-y divide-gray-200 bg-white">
                        @forelse ($categories as $category)
                            <li class="p-4">
                                <div class="flex items-center justify-between">
                                    <span class="font-medium text-gray-800">{{ $category->name }}</span>
                                    <div class="space-x-4">
                                        <button wire:click="editCategory({{ $category->id }})" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Modifier</button>
                                        <button wire:click="deleteCategory({{ $category->id }})" wire:confirm="Êtes-vous sûr ? Supprimer une catégorie supprimera aussi toutes ses sous-catégories." class="text-sm font-medium text-red-600 hover:text-red-800">Supprimer</button>
                                    </div>
                                </div>
                                @if($category->children->isNotEmpty())
                                    <ul role="list" class="mt-2 ml-4 space-y-2 border-l border-gray-200 pl-4">
                                        @foreach($category->children as $child)
                                             <li class="flex items-center justify-between">
                                                <span class="text-gray-600">{{ $child->name }}</span>
                                                <div class="space-x-4">
                                                    <button wire:click="editCategory({{ $child->id }})" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">Modifier</button>
                                                    <button wire:click="deleteCategory({{ $child->id }})" wire:confirm="Êtes-vous sûr ?" class="text-sm font-medium text-red-600 hover:text-red-800">Supprimer</button>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @endif
                            </li>
                        @empty
                            <li class="py-10 text-center text-gray-500">Aucune catégorie créée.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Section Taxes -->
        <div class="space-y-4">
            <div class="flex items-center justify-between">
                <h3 class="text-lg font-medium text-gray-900">Taxes</h3>
                 <button wire:click="createTax" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Nouvelle Taxe
                </button>
            </div>
            <div class="flow-root">
                 <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50"><tr><th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nom</th><th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Taux</th><th class="relative py-3.5 pl-3 pr-4 sm:pr-6"></th></tr></thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse($taxes as $tax)
                                <tr>
                                    <td class="py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">
                                        {{ $tax->name }}
                                        @if($tax->is_default)
                                            <span class="ml-2 inline-flex items-center rounded-md bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700">Défaut</span>
                                        @endif
                                    </td>
                                    <td class="px-3 py-4 text-sm text-gray-500">{{ $tax->rate }}%</td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <button wire:click="editTax({{ $tax->id }})" class="text-indigo-600 hover:text-indigo-900">Modifier</button>
                                        <button wire:click="deleteTax({{ $tax->id }})" wire:confirm="Êtes-vous sûr ?" class="ml-4 text-red-600 hover:text-red-900">Supprimer</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-10 text-gray-500">Aucune taxe configurée.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                 </div>
            </div>
        </div>
    </div>
    
    <!-- Panneau latéral pour les formulaires -->
    <div x-data="{ showForm: false }" @open-form.window="showForm = true" @close-form.window="showForm = false" x-show="showForm" x-cloak class="relative z-10">
        <div x-show="showForm" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showForm = false" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showForm" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" class="pointer-events-auto w-screen max-w-md">
                        <form wire:submit.prevent="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll py-6">
                                <!-- Formulaire Catégorie -->
                                <div x-show="$wire.formType === 'category'">
                                    <div class="px-4 sm:px-6"><div class="flex items-start justify-between"><h2 class="text-base font-semibold leading-6 text-gray-900">@if($editingCategory?->exists) Modifier la Catégorie @else Nouvelle Catégorie @endif</h2><div class="ml-3 flex h-7 items-center"><button @click="showForm = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button></div></div></div>
                                    <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-6">
                                        <div>
                                            <label for="category_name" class="block text-sm font-medium text-gray-900">Nom *</label>
                                            <input type="text" wire:model="categoryName" id="category_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                                            @error('categoryName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="category_parent" class="block text-sm font-medium text-gray-900">Catégorie Parente (Optionnel)</label>
                                            <select wire:model="categoryParentId" id="category_parent" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                                                <option value="">Aucune</option>
                                                @foreach($categoryOptions as $id => $name)
                                                    <option value="{{ $id }}">{{ $name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div>
                                            <label for="category_description" class="block text-sm font-medium text-gray-900">Description</label>
                                            <textarea wire:model="categoryDescription" id="category_description" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                                        </div>
                                    </div>
                                </div>
                                <!-- Formulaire Taxe -->
                                 <div x-show="$wire.formType === 'tax'">
                                    <div class="px-4 sm:px-6"><div class="flex items-start justify-between"><h2 class="text-base font-semibold leading-6 text-gray-900">@if($editingTax?->exists) Modifier la Taxe @else Nouvelle Taxe @endif</h2><div class="ml-3 flex h-7 items-center"><button @click="showForm = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button></div></div></div>
                                    <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-6">
                                        <div>
                                            <label for="tax_name" class="block text-sm font-medium text-gray-900">Nom *</label>
                                            <input type="text" wire:model="taxName" id="tax_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                                            @error('taxName') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div>
                                            <label for="tax_rate" class="block text-sm font-medium text-gray-900">Taux (%) *</label>
                                            <input type="number" step="0.01" wire:model="taxRate" id="tax_rate" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                                            @error('taxRate') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                        </div>
                                        <div class="relative flex items-start">
                                            <div class="flex h-6 items-center"><input id="tax_is_default" wire:model="taxIsDefault" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></div>
                                            <div class="ml-3 text-sm leading-6"><label for="tax_is_default" class="font-medium text-gray-900">Définir comme taxe par défaut</label></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end px-4 py-4"><button @click="showForm = false" type="button" class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</button><button type="submit" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Enregistrer</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
