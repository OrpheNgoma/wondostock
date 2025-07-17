<div class="min-h-screen bg-gray-50" x-data="{ 
    showProductModal: @entangle('showProductModal'),
    showConfirmModal: @entangle('showConfirmModal'),
    currentStep: @entangle('current_step')
}">
    {{-- Header Section --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-6">
                <div>
                    <h1 class="text-2xl font-bold text-gray-900">
                        {{ $document->exists ? __('Modifier le Bon de Commande') : __('Nouveau Bon de Commande') }}
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Créez et gérez vos commandes fournisseurs') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('purchases.index') }}" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        {{ __('Retour') }}
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Progress Steps --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <nav aria-label="Progress" class="py-4">
                <ol class="flex items-center justify-center space-x-8">
                    @php
                        $steps = [
                            'basic_info' => ['label' => 'Informations de base', 'icon' => 'clipboard-list'],
                            'products' => ['label' => 'Produits à commander', 'icon' => 'cube'],
                            'review' => ['label' => 'Vérification', 'icon' => 'check-circle']
                        ];
                        $stepOrder = ['basic_info', 'products', 'review'];
                        $currentIndex = array_search($current_step, $stepOrder);
                    @endphp
                    
                    @foreach($steps as $key => $step)
                        @php
                            $stepIndex = array_search($key, $stepOrder);
                            $isCompleted = $stepIndex < $currentIndex;
                            $isCurrent = $key === $current_step;
                            $isUpcoming = $stepIndex > $currentIndex;
                        @endphp
                        
                        <li class="flex items-center">
                            <div class="flex items-center {{ !$loop->last ? 'relative' : '' }}">
                                <div class="flex h-10 w-10 items-center justify-center rounded-full border-2 {{ 
                                    $isCompleted ? 'bg-blue-600 border-blue-600' : 
                                    ($isCurrent ? 'bg-blue-100 border-blue-600' : 'bg-gray-100 border-gray-300') 
                                }}">
                                    @if($isCompleted)
                                        <svg class="h-5 w-5 text-white" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                    @else
                                        <span class="h-5 w-5 text-{{ $isCurrent ? 'blue-600' : 'gray-500' }} font-medium">{{ $stepIndex + 1 }}</span>
                                    @endif
                                </div>
                                <span class="ml-3 text-sm font-medium {{ $isCurrent ? 'text-blue-600' : ($isCompleted ? 'text-gray-900' : 'text-gray-500') }}">
                                    {{ $step['label'] }}
                                </span>
                                @if(!$loop->last)
                                    <div class="ml-8 h-0.5 w-16 {{ $isCompleted ? 'bg-blue-600' : 'bg-gray-200' }}"></div>
                                @endif
                            </div>
                        </li>
                    @endforeach
                </ol>
            </nav>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-6xl mx-auto">
            <form wire:submit.prevent="save">
                {{-- Step 1: Basic Information --}}
                @if($current_step === 'basic_info')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Main Information --}}
                        <div class="lg:col-span-2">
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations du bon de commande') }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ __('Définissez les détails de votre commande') }}</p>
                                </div>

                                <div class="p-6 space-y-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Reference --}}
                                        <div>
                                            <label for="reference" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Référence') }}</label>
                                            <input type="text" 
                                                   wire:model="reference" 
                                                   id="reference"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @error('reference')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                        </div>

                                        {{-- Priority --}}
                                        <div>
                                            <label for="priority" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Priorité') }} *</label>
                                            <select wire:model="priority" 
                                                    id="priority"
                                                    class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                @foreach($priorities as $value => $label)
                                                    <option value="{{ $value }}">{{ $label }}</option>
                                                @endforeach
                                            </select>
                                            @error('priority')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Document Date --}}
                                        <div>
                                            <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Date de commande') }} *</label>
                                            <input type="date" 
                                                   wire:model="document_date" 
                                                   id="document_date"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @error('document_date')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                        </div>

                                        {{-- Delivery Date --}}
                                        <div>
                                            <label for="delivery_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Date de livraison souhaitée') }}</label>
                                            <input type="date" 
                                                   wire:model="delivery_date" 
                                                   id="delivery_date"
                                                   class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            @error('delivery_date')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                        </div>
                                    </div>

                                    {{-- Store Selection --}}
                                    <div>
                                        <label for="store_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Magasin de destination') }} *</label>
                                        <select wire:model="store_id" 
                                                id="store_id"
                                                class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                            <option value="">{{ __('Sélectionner un magasin') }}</option>
                                            @foreach($stores as $store)
                                                <option value="{{ $store->id }}">{{ $store->name }}</option>
                                            @endforeach
                                        </select>
                                        @error('store_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>

                                    {{-- Supplier Search --}}
                                    <div>
                                        <label for="supplier_search" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Fournisseur') }} *</label>
                                        <div class="relative">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                @if($isSearchingSuppliers)
                                                    <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                    </svg>
                                                @else
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                                    </svg>
                                                @endif
                                            </div>
                                            <input type="text" 
                                                   wire:model.live.debounce.300ms="supplier_search"
                                                   id="supplier_search"
                                                   placeholder="Rechercher un fournisseur..."
                                                   class="block w-full pl-10 pr-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        </div>

                                        {{-- Supplier Search Results --}}
                                        @if(count($suppliers_list) > 0)
                                            <div class="mt-2 border border-gray-200 rounded-lg max-h-48 overflow-y-auto">
                                                @foreach($suppliers_list as $supplier)
                                                    <div class="p-3 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer transition-colors"
                                                         wire:click="selectSupplier({{ $supplier->id }})">
                                                        <div class="flex items-center justify-between">
                                                            <div>
                                                                <h4 class="text-sm font-medium text-gray-900">{{ $supplier->name }}</h4>
                                                                @if($supplier->email || $supplier->phone_number)
                                                                    <p class="text-sm text-gray-500">
                                                                        {{ $supplier->email }}
                                                                        @if($supplier->email && $supplier->phone_number) • @endif
                                                                        {{ $supplier->phone_number }}
                                                                    </p>
                                                                @endif
                                                            </div>
                                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                                            </svg>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @endif
                                        @error('supplier_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>

                                    {{-- Notes --}}
                                    <div>
                                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Notes et instructions') }}</label>
                                        <textarea wire:model="notes" 
                                                  id="notes" 
                                                  rows="3"
                                                  placeholder="Ajoutez des notes ou des instructions spéciales pour cette commande..."
                                                  class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                        @error('notes')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Supplier Information Sidebar --}}
                        <div class="lg:col-span-1">
                            @if($this->selectedSupplier)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                                        <h3 class="text-lg font-semibold text-blue-900">{{ __('Fournisseur sélectionné') }}</h3>
                                    </div>
                                    <div class="p-6">
                                        <div class="space-y-4">
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">{{ $this->selectedSupplier->name }}</h4>
                                                @if($this->selectedSupplier->legal_name)
                                                    <p class="text-sm text-gray-600">{{ $this->selectedSupplier->legal_name }}</p>
                                                @endif
                                            </div>
                                            
                                            @if($this->selectedSupplier->email)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $this->selectedSupplier->email }}
                                                </div>
                                            @endif
                                            
                                            @if($this->selectedSupplier->phone_number)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    {{ $this->selectedSupplier->phone_number }}
                                                </div>
                                            @endif
                                            
                                            @if($this->selectedSupplier->address)
                                                <div class="flex items-start text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $this->selectedSupplier->address }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                    <div class="px-6 py-12 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('Aucun fournisseur sélectionné') }}</h3>
                                        <p class="mt-1 text-sm text-gray-500">{{ __('Recherchez et sélectionnez un fournisseur') }}</p>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Step Navigation --}}
                    <div class="mt-8 flex justify-end">
                        <button type="button" 
                                wire:click="nextStep"
                                class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            {{ __('Suivant') }}
                            <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                            </svg>
                        </button>
                    </div>
                @endif

                {{-- Step 2: Products Selection --}}
                @if($current_step === 'products')
                    <div class="space-y-6">
                        {{-- Products Search Card --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Ajouter des produits') }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ __('Recherchez et ajoutez les produits à commander') }}</p>
                                    </div>
                                    @if(count($items) > 0)
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">{{ count($items) }}</span> produit(s) ajouté(s)
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-6">
                                {{-- Search Bar --}}
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        @if($isSearchingProducts)
                                            <svg class="animate-spin h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        @else
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                                            </svg>
                                        @endif
                                    </div>
                                    <input type="text" 
                                           wire:model.live.debounce.300ms="product_search"
                                           placeholder="Rechercher par nom, SKU ou description..."
                                           class="block w-full pl-10 pr-3 py-3 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>

                                {{-- Search Results --}}
                                @if(count($products_list) > 0)
                                    <div class="mt-4 border border-gray-200 rounded-lg max-h-64 overflow-y-auto">
                                        @foreach($products_list as $product)
                                            <div class="p-4 hover:bg-gray-50 border-b border-gray-100 last:border-b-0 cursor-pointer transition-colors"
                                                 wire:click="addProduct({{ $product->id }})">
                                                <div class="flex items-center justify-between">
                                                    <div class="flex-1">
                                                        <h4 class="text-sm font-medium text-gray-900">{{ $product->name }}</h4>
                                                        <p class="text-sm text-gray-500">SKU: {{ $product->sku }} • {{ $product->category?->name ?? 'Sans catégorie' }}</p>
                                                        @if($product->purchase_price)
                                                            <p class="text-sm text-green-600 font-medium">Prix d'achat: @fcfa($product->purchase_price)</p>
                                                        @endif
                                                    </div>
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(strlen($product_search) >= 2 && !$isSearchingProducts)
                                    <div class="mt-4 text-center text-gray-500 py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm">Aucun produit trouvé</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Selected Products --}}
                        @if(count($items) > 0)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Produits à commander') }}</h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span>Total articles: <span class="font-medium">{{ $total_items }}</span></span>
                                            <span>Total commande: <span class="font-medium">@fcfa($total_amount)</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                            </tr>
                                        </thead>
                                        <tbody class="bg-white divide-y divide-gray-200">
                                            @foreach($items as $index => $item)
                                                <tr wire:key="item-{{ $index }}" class="hover:bg-gray-50">
                                                    <td class="px-6 py-4">
                                                        <div class="flex items-center">
                                                            <div>
                                                                <div class="text-sm font-medium text-gray-900">{{ $item['name'] }}</div>
                                                                <div class="text-sm text-gray-500">{{ $item['sku'] ?? 'N/A' }} • {{ $item['category'] ?? 'Sans catégorie' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <input type="number" 
                                                               wire:model.lazy="items.{{ $index }}.quantity"
                                                               wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                               min="1"
                                                               class="w-20 px-2 py-1 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <input type="number" 
                                                               wire:model.lazy="items.{{ $index }}.unit_price"
                                                               wire:change="updateUnitPrice({{ $index }}, $event.target.value)"
                                                               min="0"
                                                               step="0.01"
                                                               class="w-24 px-2 py-1 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
                                                        @fcfa($item['quantity'] * $item['unit_price'])
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <button type="button" 
                                                                wire:click="removeItem({{ $index }})"
                                                                class="text-red-600 hover:text-red-800 transition-colors">
                                                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @else
                            {{-- Empty State --}}
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-12 text-center">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit ajouté</h3>
                                    <p class="mt-1 text-sm text-gray-500">Commencez par rechercher et ajouter des produits à commander.</p>
                                </div>
                            </div>
                        @endif

                        {{-- Step Navigation --}}
                        <div class="flex justify-between">
                            <button type="button" 
                                    wire:click="previousStep"
                                    class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                                </svg>
                                {{ __('Précédent') }}
                            </button>
                            
                            <button type="button" 
                                    wire:click="nextStep"
                                    class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                {{ __('Vérifier') }}
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif

                {{-- Step 3: Review --}}
                @if($current_step === 'review')
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        {{-- Command Summary --}}
                        <div class="lg:col-span-2 space-y-6">
                            {{-- Order Information --}}
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Résumé de la commande') }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ __('Vérifiez les informations avant de confirmer') }}</p>
                                </div>

                                <div class="p-6">
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- Order Details --}}
                                        <div class="space-y-4">
                                            <h4 class="text-sm font-medium text-gray-900">{{ __('Détails de la commande') }}</h4>
                                            <dl class="space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Référence:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $reference }}</dd>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Date de commande:</dt>
                                                    <dd class="text-gray-900">{{ \Carbon\Carbon::parse($document_date)->format('d/m/Y') }}</dd>
                                                </div>
                                                @if($delivery_date)
                                                    <div class="flex justify-between text-sm">
                                                        <dt class="text-gray-500">Livraison souhaitée:</dt>
                                                        <dd class="text-gray-900">{{ \Carbon\Carbon::parse($delivery_date)->format('d/m/Y') }}</dd>
                                                    </div>
                                                @endif
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Priorité:</dt>
                                                    <dd class="text-gray-900">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ 
                                                            $priority === 'urgent' ? 'bg-red-100 text-red-800' : 
                                                            ($priority === 'high' ? 'bg-orange-100 text-orange-800' : 
                                                            ($priority === 'normal' ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-800')) 
                                                        }}">
                                                            {{ $priorities[$priority] ?? $priority }}
                                                        </span>
                                                    </dd>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Magasin de destination:</dt>
                                                    <dd class="text-gray-900">{{ $stores->find($store_id)?->name }}</dd>
                                                </div>
                                                @if($notes)
                                                    <div class="flex justify-between text-sm">
                                                        <dt class="text-gray-500">Notes:</dt>
                                                        <dd class="text-gray-900">{{ $notes }}</dd>
                                                    </div>
                                                @endif
                                            </dl>
                                        </div>

                                        {{-- Order Stats --}}
                                        <div class="space-y-4">
                                            <h4 class="text-sm font-medium text-gray-900">{{ __('Statistiques') }}</h4>
                                            <dl class="space-y-2">
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Nombre de produits:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ count($items) }}</dd>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Total articles:</dt>
                                                    <dd class="text-gray-900 font-medium">{{ $total_items }}</dd>
                                                </div>
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Sous-total:</dt>
                                                    <dd class="text-gray-900 font-medium">@fcfa($sub_total)</dd>
                                                </div>
                                                @if($tax_amount > 0)
                                                    <div class="flex justify-between text-sm">
                                                        <dt class="text-gray-500">Taxes:</dt>
                                                        <dd class="text-gray-900 font-medium">@fcfa($tax_amount)</dd>
                                                    </div>
                                                @endif
                                                <div class="flex justify-between text-base font-semibold border-t border-gray-200 pt-2">
                                                    <dt class="text-gray-900">Total:</dt>
                                                    <dd class="text-gray-900">@fcfa($total_amount)</dd>
                                                </div>
                                            </dl>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Products List --}}
                            @if(count($items) > 0)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Produits à commander') }}</h3>
                                    </div>

                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Prix unitaire</th>
                                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($items as $item)
                                                    <tr>
                                                        <td class="px-6 py-4">
                                                            <div class="flex items-center">
                                                                <div>
                                                                    <div class="text-sm font-medium text-gray-900">{{ $item['name'] }}</div>
                                                                    <div class="text-sm text-gray-500">{{ $item['sku'] ?? 'N/A' }}</div>
                                                                </div>
                                                            </div>
                                                        </td>
                                                        <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                            {{ $item['quantity'] }} {{ $item['unit'] ?? 'unité(s)' }}
                                                        </td>
                                                        <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                            @fcfa($item['unit_price'])
                                                        </td>
                                                        <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
                                                            @fcfa($item['quantity'] * $item['unit_price'])
                                                        </td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                            <tfoot class="bg-gray-50">
                                                <tr>
                                                    <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total général:</td>
                                                    <td class="px-6 py-3 text-center text-sm font-bold text-gray-900">@fcfa($total_amount)</td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Supplier Information Sidebar --}}
                        <div class="lg:col-span-1">
                            @if($this->selectedSupplier)
                                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                    <div class="px-6 py-4 border-b border-gray-200 bg-blue-50">
                                        <h3 class="text-lg font-semibold text-blue-900">{{ __('Fournisseur') }}</h3>
                                    </div>
                                    <div class="p-6">
                                        <div class="space-y-4">
                                            <div>
                                                <h4 class="text-lg font-medium text-gray-900">{{ $this->selectedSupplier->name }}</h4>
                                                @if($this->selectedSupplier->legal_name)
                                                    <p class="text-sm text-gray-600">{{ $this->selectedSupplier->legal_name }}</p>
                                                @endif
                                            </div>
                                            
                                            @if($this->selectedSupplier->email)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                    </svg>
                                                    {{ $this->selectedSupplier->email }}
                                                </div>
                                            @endif
                                            
                                            @if($this->selectedSupplier->phone_number)
                                                <div class="flex items-center text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                                    </svg>
                                                    {{ $this->selectedSupplier->phone_number }}
                                                </div>
                                            @endif
                                            
                                            @if($this->selectedSupplier->address)
                                                <div class="flex items-start text-sm text-gray-600">
                                                    <svg class="h-4 w-4 mr-2 mt-0.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    </svg>
                                                    {{ $this->selectedSupplier->address }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Step Navigation --}}
                    <div class="mt-8 flex justify-between">
                        <button type="button" 
                                wire:click="previousStep"
                                class="inline-flex items-center px-6 py-2 border border-gray-300 text-gray-700 text-sm font-medium rounded-lg hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                            </svg>
                            {{ __('Modifier') }}
                        </button>
                        
                        <button type="button" 
                                wire:click="confirmSave"
                                class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            {{ __('Confirmer la commande') }}
                        </button>
                    </div>
                @endif
            </form>
        </div>
    </div>

    {{-- Product Modal --}}
    <div x-show="showProductModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
         style="display: none;">
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showProductModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    
                    @if($selectedProduct)
                        <div>
                            <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-blue-100">
                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            
                            <div class="mt-3 text-center sm:mt-5">
                                <h3 class="text-base font-semibold leading-6 text-gray-900">
                                    Ajouter {{ $selectedProduct->name }}
                                </h3>
                                <div class="mt-2">
                                    <p class="text-sm text-gray-500">
                                        SKU: {{ $selectedProduct->sku }}<br>
                                        {{ $selectedProduct->category?->name ?? 'Sans catégorie' }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-5 space-y-4">
                                <div>
                                    <label for="modal_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                        Quantité à commander
                                    </label>
                                    <input type="number" 
                                           wire:model="modalQuantity"
                                           id="modal_quantity"
                                           min="1"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="modal_unit_price" class="block text-sm font-medium text-gray-700 mb-2">
                                        Prix unitaire (FCFA)
                                    </label>
                                    <input type="number" 
                                           wire:model="modalUnitPrice"
                                           id="modal_unit_price"
                                           min="0"
                                           step="0.01"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                                
                                <div>
                                    <label for="modal_description" class="block text-sm font-medium text-gray-700 mb-2">
                                        Description (optionnel)
                                    </label>
                                    <input type="text" 
                                           wire:model="modalDescription"
                                           id="modal_description"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                            <button type="button" 
                                    wire:click="addProductFromModal"
                                    class="inline-flex w-full justify-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 sm:col-start-2">
                                Ajouter
                            </button>
                            <button type="button" 
                                    wire:click="$set('showProductModal', false)"
                                    class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                                Annuler
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- Confirmation Modal --}}
    <div x-show="showConfirmModal" 
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-50"
         style="display: none;">
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div x-show="showConfirmModal"
                     x-transition:enter="ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave="ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     class="relative transform overflow-hidden rounded-lg bg-white px-4 pb-4 pt-5 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:p-6">
                    
                    <div>
                        <div class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                Confirmer la commande
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir enregistrer ce bon de commande ?<br>
                                    Montant total: <span class="font-medium">@fcfa($total_amount)</span>
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mt-5 sm:mt-6 sm:grid sm:grid-flow-row-dense sm:grid-cols-2 sm:gap-3">
                        <button type="button" 
                                wire:click="save"
                                wire:loading.attr="disabled"
                                class="inline-flex w-full justify-center items-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600 sm:col-start-2 disabled:opacity-50">
                            <span wire:loading.remove wire:target="save">Confirmer</span>
                            <span wire:loading wire:target="save" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Enregistrement...
                            </span>
                        </button>
                        <button type="button" 
                                wire:click="$set('showConfirmModal', false)"
                                class="mt-3 inline-flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:col-start-1 sm:mt-0">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>