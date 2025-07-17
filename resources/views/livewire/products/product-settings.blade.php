<div class="min-h-screen bg-gray-50">
    {{-- Header Section --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Paramètres des Produits') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Gérez les catégories et taxes de vos produits') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    {{-- Quick Stats --}}
                    <div class="hidden sm:flex items-center space-x-4 text-sm text-gray-600">
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                            </svg>
                            {{ $categories->count() }} catégories
                        </div>
                        <div class="flex items-center">
                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                            {{ $taxes->count() }} taxes
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8" aria-label="Tabs">
                <button wire:click="switchTab('categories')"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'categories' 
                            ? 'border-blue-500 text-blue-600' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        {{ __('Catégories') }}
                        <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $categories->count() }}</span>
                    </div>
                </button>
                <button wire:click="switchTab('taxes')"
                        class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === 'taxes' 
                            ? 'border-blue-500 text-blue-600' 
                            : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                    <div class="flex items-center">
                        <svg class="h-5 w-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        {{ __('Taxes') }}
                        <span class="ml-2 bg-gray-100 text-gray-600 py-0.5 px-2 rounded-full text-xs">{{ $taxes->count() }}</span>
                    </div>
                </button>
            </nav>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-7xl mx-auto">
            @if($activeTab === 'categories')
                {{-- Categories Tab --}}
                <div class="space-y-6">
                    {{-- Controls Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                                {{-- Left side: Search and filters --}}
                                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                                    {{-- Search --}}
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               wire:model.live.debounce.300ms="searchCategories"
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Rechercher une catégorie...">
                                    </div>

                                    {{-- Filter --}}
                                    <select wire:model.live="categoryFilter"
                                            class="block w-full sm:w-auto border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="all">Toutes les catégories</option>
                                        <option value="parent">Catégories principales</option>
                                        <option value="child">Sous-catégories</option>
                                    </select>
                                </div>

                                {{-- Right side: Actions --}}
                                <div class="flex items-center space-x-3">
                                    @if(count($selectedCategories) > 0)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600">{{ count($selectedCategories) }} sélectionnée(s)</span>
                                            <button wire:click="bulkDeleteCategories"
                                                    wire:confirm="Êtes-vous sûr de vouloir supprimer {{ count($selectedCategories) }} catégorie(s) ?"
                                                    class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Supprimer
                                            </button>
                                        </div>
                                    @endif
                                    <button wire:click="createCategory"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        {{ __('Nouvelle Catégorie') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Categories List --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        @if($categories->count() > 0)
                            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="selectAllCategories"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Sélectionner tout</span>
                                </label>
                            </div>

                            <div class="divide-y divide-gray-200">
                                @foreach($categories as $category)
                                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <input type="checkbox" 
                                                       wire:model.live="selectedCategories" 
                                                       value="{{ $category->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                
                                                {{-- Category Color --}}
                                                <div class="h-3 w-3 rounded-full border border-gray-300" 
                                                     style="background-color: {{ $category->color ?? '#3B82F6' }}"></div>

                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        @if($category->icon)
                                                            <span class="text-lg">{{ $category->icon }}</span>
                                                        @endif
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $category->name }}</h4>
                                                        @if($category->parent_id)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-gray-100 text-gray-800">
                                                                Sous-catégorie
                                                            </span>
                                                        @endif
                                                    </div>
                                                    @if($category->description)
                                                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($category->description, 100) }}</p>
                                                    @endif
                                                    @if($category->children->count() > 0)
                                                        <p class="mt-1 text-xs text-blue-600">{{ $category->children->count() }} sous-catégorie(s)</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <button wire:click="editCategory({{ $category->id }})"
                                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Modifier
                                                </button>
                                                <button wire:click="deleteCategory({{ $category->id }})"
                                                        wire:confirm="Êtes-vous sûr de vouloir supprimer cette catégorie ?"
                                                        class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>

                                        {{-- Child Categories --}}
                                        @if($category->children->count() > 0)
                                            <div class="mt-4 ml-6 pl-4 border-l-2 border-gray-200">
                                                @foreach($category->children as $child)
                                                    <div class="flex items-center justify-between py-2">
                                                        <div class="flex items-center space-x-3">
                                                            <div class="h-2 w-2 rounded-full bg-gray-300"></div>
                                                            <div>
                                                                <h5 class="text-sm text-gray-700">{{ $child->name }}</h5>
                                                                @if($child->description)
                                                                    <p class="text-xs text-gray-500">{{ Str::limit($child->description, 80) }}</p>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="flex items-center space-x-1">
                                                            <button wire:click="editCategory({{ $child->id }})"
                                                                    class="p-1 text-gray-400 hover:text-gray-600 transition-colors">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                </svg>
                                                            </button>
                                                            <button wire:click="deleteCategory({{ $child->id }})"
                                                                    wire:confirm="Êtes-vous sûr ?"
                                                                    class="p-1 text-red-400 hover:text-red-600 transition-colors">
                                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                            </button>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune catégorie</h3>
                                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première catégorie de produits.</p>
                                <div class="mt-6">
                                    <button wire:click="createCategory"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Créer une catégorie
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            @else
                {{-- Taxes Tab --}}
                <div class="space-y-6">
                    {{-- Controls Section --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200">
                        <div class="px-6 py-4 border-b border-gray-200">
                            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between space-y-4 sm:space-y-0">
                                {{-- Left side: Search and filters --}}
                                <div class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-4">
                                    {{-- Search --}}
                                    <div class="relative">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        </div>
                                        <input type="text" 
                                               wire:model.live.debounce.300ms="searchTaxes"
                                               class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg text-sm placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                               placeholder="Rechercher une taxe...">
                                    </div>

                                    {{-- Filter --}}
                                    <select wire:model.live="taxFilter"
                                            class="block w-full sm:w-auto border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        <option value="all">Toutes les taxes</option>
                                        <option value="default">Taxe par défaut</option>
                                        <option value="custom">Taxes personnalisées</option>
                                    </select>
                                </div>

                                {{-- Right side: Actions --}}
                                <div class="flex items-center space-x-3">
                                    @if(count($selectedTaxes) > 0)
                                        <div class="flex items-center space-x-2">
                                            <span class="text-sm text-gray-600">{{ count($selectedTaxes) }} sélectionnée(s)</span>
                                            <button wire:click="bulkDeleteTaxes"
                                                    wire:confirm="Êtes-vous sûr de vouloir supprimer {{ count($selectedTaxes) }} taxe(s) ?"
                                                    class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                                <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                </svg>
                                                Supprimer
                                            </button>
                                        </div>
                                    @endif
                                    <button wire:click="createTax"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        {{ __('Nouvelle Taxe') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Taxes List --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        @if($taxes->count() > 0)
                            <div class="px-6 py-3 bg-gray-50 border-b border-gray-200">
                                <label class="flex items-center">
                                    <input type="checkbox" 
                                           wire:model.live="selectAllTaxes"
                                           class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                    <span class="ml-2 text-sm text-gray-600">Sélectionner tout</span>
                                </label>
                            </div>

                            <div class="divide-y divide-gray-200">
                                @foreach($taxes as $tax)
                                    <div class="px-6 py-4 hover:bg-gray-50 transition-colors">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center space-x-3">
                                                <input type="checkbox" 
                                                       wire:model.live="selectedTaxes" 
                                                       value="{{ $tax->id }}"
                                                       class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                                
                                                <div>
                                                    <div class="flex items-center space-x-2">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $tax->name }}</h4>
                                                        @if($tax->is_default)
                                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-blue-100 text-blue-800">
                                                                <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                                                </svg>
                                                                Par défaut
                                                            </span>
                                                        @endif
                                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                                            {{ $tax->rate }}%
                                                        </span>
                                                    </div>
                                                    @if($tax->description)
                                                        <p class="mt-1 text-sm text-gray-500">{{ Str::limit($tax->description, 100) }}</p>
                                                    @endif
                                                </div>
                                            </div>

                                            <div class="flex items-center space-x-2">
                                                <button wire:click="editTax({{ $tax->id }})"
                                                        class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Modifier
                                                </button>
                                                <button wire:click="deleteTax({{ $tax->id }})"
                                                        wire:confirm="Êtes-vous sûr de vouloir supprimer cette taxe ?"
                                                        class="inline-flex items-center px-3 py-2 border border-red-300 rounded-lg text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                                    <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                    </svg>
                                                    Supprimer
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                </svg>
                                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune taxe</h3>
                                <p class="mt-1 text-sm text-gray-500">Commencez par créer votre première taxe pour vos produits.</p>
                                <div class="mt-6">
                                    <button wire:click="createTax"
                                            class="inline-flex items-center px-4 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700">
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Créer une taxe
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Modal Form --}}
    <div x-data="{ showForm: false }" 
         @open-form.window="showForm = true" 
         @close-form.window="showForm = false" 
         x-show="showForm" 
         x-cloak 
         class="fixed inset-0 z-50 overflow-y-auto">
        
        {{-- Backdrop --}}
        <div x-show="showForm" 
             x-transition:enter="ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

        {{-- Modal --}}
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div x-show="showForm"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 @click.away="showForm = false"
                 class="relative transform overflow-hidden rounded-xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg">
                
                <form wire:submit.prevent="save">
                    {{-- Header --}}
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-200">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-semibold text-gray-900">
                                @if($formType === 'category')
                                    @if($editingCategory?->exists)
                                        {{ __('Modifier la Catégorie') }}
                                    @else
                                        {{ __('Nouvelle Catégorie') }}
                                    @endif
                                @else
                                    @if($editingTax?->exists)
                                        {{ __('Modifier la Taxe') }}
                                    @else
                                        {{ __('Nouvelle Taxe') }}
                                    @endif
                                @endif
                            </h3>
                            <button type="button" 
                                    @click="showForm = false"
                                    class="text-gray-400 hover:text-gray-600 transition-colors">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="px-6 py-6">
                        @if($formType === 'category')
                            <div class="space-y-6">
                                {{-- Category Name --}}
                                <div>
                                    <label for="categoryName" class="block text-sm font-medium text-gray-700">
                                        {{ __('Nom de la catégorie') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model.blur="categoryName" 
                                           id="categoryName"
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('categoryName') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                           placeholder="Ex: Électronique, Vêtements...">
                                    @error('categoryName')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Parent Category --}}
                                <div>
                                    <label for="categoryParentId" class="block text-sm font-medium text-gray-700">
                                        {{ __('Catégorie parente') }}
                                    </label>
                                    <select wire:model="categoryParentId" 
                                            id="categoryParentId"
                                            class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                                        <option value="">{{ __('Aucune (catégorie principale)') }}</option>
                                        @foreach($categoryOptions as $id => $name)
                                            <option value="{{ $id }}">{{ $name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- Color and Icon --}}
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label for="categoryColor" class="block text-sm font-medium text-gray-700">
                                            {{ __('Couleur') }}
                                        </label>
                                        <div class="mt-1 flex items-center space-x-3">
                                            <input type="color" 
                                                   wire:model.live="categoryColor" 
                                                   id="categoryColor"
                                                   class="h-10 w-16 rounded border border-gray-300 cursor-pointer">
                                            <input type="text" 
                                                   wire:model.blur="categoryColor"
                                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"
                                                   placeholder="#3B82F6">
                                        </div>
                                        @error('categoryColor')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label for="categoryIcon" class="block text-sm font-medium text-gray-700">
                                            {{ __('Icône (emoji)') }}
                                        </label>
                                        <input type="text" 
                                               wire:model.blur="categoryIcon" 
                                               id="categoryIcon"
                                               class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                               placeholder="📱 💻 👕"
                                               maxlength="2">
                                        @error('categoryIcon')
                                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                        @enderror
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label for="categoryDescription" class="block text-sm font-medium text-gray-700">
                                        {{ __('Description') }}
                                    </label>
                                    <textarea wire:model.blur="categoryDescription" 
                                              id="categoryDescription"
                                              rows="3"
                                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Description optionnelle de la catégorie..."></textarea>
                                    @error('categoryDescription')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>

                        @else
                            <div class="space-y-6">
                                {{-- Tax Name --}}
                                <div>
                                    <label for="taxName" class="block text-sm font-medium text-gray-700">
                                        {{ __('Nom de la taxe') }} <span class="text-red-500">*</span>
                                    </label>
                                    <input type="text" 
                                           wire:model.blur="taxName" 
                                           id="taxName"
                                           class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('taxName') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                           placeholder="Ex: TVA, Taxe de luxe...">
                                    @error('taxName')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Tax Rate --}}
                                <div>
                                    <label for="taxRate" class="block text-sm font-medium text-gray-700">
                                        {{ __('Taux de taxe (%)') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="mt-1 relative">
                                        <input type="number" 
                                               wire:model.blur="taxRate" 
                                               id="taxRate"
                                               step="0.01"
                                               min="0"
                                               max="100"
                                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 @error('taxRate') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                               placeholder="18.00">
                                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                                            <span class="text-gray-500 sm:text-sm">%</span>
                                        </div>
                                    </div>
                                    @error('taxRate')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>

                                {{-- Default Tax --}}
                                <div>
                                    <div class="flex items-start">
                                        <div class="flex items-center h-5">
                                            <input type="checkbox" 
                                                   wire:model="taxIsDefault" 
                                                   id="taxIsDefault"
                                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                                        </div>
                                        <div class="ml-3 text-sm">
                                            <label for="taxIsDefault" class="font-medium text-gray-700">
                                                {{ __('Définir comme taxe par défaut') }}
                                            </label>
                                            <p class="text-gray-500">Cette taxe sera automatiquement sélectionnée pour les nouveaux produits.</p>
                                        </div>
                                    </div>
                                </div>

                                {{-- Description --}}
                                <div>
                                    <label for="taxDescription" class="block text-sm font-medium text-gray-700">
                                        {{ __('Description') }}
                                    </label>
                                    <textarea wire:model.blur="taxDescription" 
                                              id="taxDescription"
                                              rows="3"
                                              class="mt-1 block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                                              placeholder="Description optionnelle de la taxe..."></textarea>
                                    @error('taxDescription')
                                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                    @enderror
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 flex justify-end space-x-3">
                        <button type="button" 
                                @click="showForm = false"
                                class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            {{ __('app.general.cancel') }}
                        </button>
                        <button type="submit" 
                                class="inline-flex items-center px-6 py-2 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('app.general.save') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>