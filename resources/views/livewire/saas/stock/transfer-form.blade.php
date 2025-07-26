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
                    <h1 class="text-2xl font-bold text-gray-900">{{ __('Transfert de Stock') }}</h1>
                    <p class="mt-1 text-sm text-gray-500">{{ __('Déplacez des produits d\'un magasin à un autre') }}</p>
                </div>
                <div class="flex items-center space-x-3">
                    <a href="{{ route('stock.movements.index') }}" 
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
                            'transfer_info' => ['label' => 'Informations du transfert', 'icon' => 'clipboard-list'],
                            'products' => ['label' => 'Sélection des produits', 'icon' => 'cube'],
                            'review' => ['label' => 'Vérification', 'icon' => 'check-circle']
                        ];
                        $stepOrder = ['transfer_info', 'products', 'review'];
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
        <div class="max-w-4xl mx-auto">
            <form wire:submit.prevent="save">
                {{-- Step 1: Transfer Information --}}
                @if($current_step === 'transfer_info')
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations du transfert') }}</h3>
                            <p class="text-sm text-gray-600 mt-1">{{ __('Définissez les détails de votre transfert de stock') }}</p>
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

                                {{-- Transfer Date --}}
                                <div>
                                    <label for="transfer_date" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Date du transfert') }} *</label>
                                    <input type="date" 
                                           wire:model="transfer_date" 
                                           id="transfer_date"
                                           class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @error('transfer_date')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- From Store --}}
                                <div>
                                    <label for="from_store_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Magasin d\'origine') }} *</label>
                                    <select wire:model.live="from_store_id" 
                                            id="from_store_id"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('Sélectionner un magasin') }}</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('from_store_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                </div>

                                {{-- To Store --}}
                                <div>
                                    <label for="to_store_id" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Magasin de destination') }} *</label>
                                    <select wire:model="to_store_id" 
                                            id="to_store_id"
                                            class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                        <option value="">{{ __('Sélectionner un magasin') }}</option>
                                        @foreach($stores as $store)
                                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                                        @endforeach
                                    </select>
                                    @error('to_store_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                </div>
                            </div>

                            {{-- Transfer Reason --}}
                            <div>
                                <label for="transfer_reason" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Motif du transfert') }} *</label>
                                <select wire:model="transfer_reason" 
                                        id="transfer_reason"
                                        class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                    @foreach($transferReasons as $value => $label)
                                        <option value="{{ $value }}">{{ $label }}</option>
                                    @endforeach
                                </select>
                                @error('transfer_reason')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                            </div>

                            {{-- Notes --}}
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Notes additionnelles') }}</label>
                                <textarea wire:model="notes" 
                                          id="notes" 
                                          rows="3"
                                          placeholder="Ajoutez des notes ou des instructions spéciales..."
                                          class="block w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm"></textarea>
                                @error('notes')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                            </div>

                            {{-- Store Information Cards --}}
                            @if($from_store_id && $to_store_id)
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-6">
                                    {{-- Origin Store Card --}}
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-sm font-medium text-blue-900">{{ __('Magasin d\'origine') }}</h4>
                                                <p class="text-sm text-blue-700">{{ $this->fromStore?->name }}</p>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Destination Store Card --}}
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0">
                                                <svg class="h-8 w-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <div class="ml-3">
                                                <h4 class="text-sm font-medium text-green-900">{{ __('Magasin de destination') }}</h4>
                                                <p class="text-sm text-green-700">{{ $this->toStore?->name }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        {{-- Step Navigation --}}
                        <div class="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-end">
                            <button type="button" 
                                    wire:click="nextStep"
                                    class="inline-flex items-center px-6 py-2 bg-blue-600 text-white text-sm font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 transition-colors">
                                {{ __('Suivant') }}
                                <svg class="ml-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                                </svg>
                            </button>
                        </div>
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
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Sélection des produits') }}</h3>
                                        <p class="text-sm text-gray-600 mt-1">{{ __('Recherchez et ajoutez les produits à transférer') }}</p>
                                    </div>
                                    @if(count($items) > 0)
                                        <div class="text-sm text-gray-600">
                                            <span class="font-medium">{{ count($items) }}</span> produit(s) sélectionné(s)
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <div class="p-6">
                                {{-- Search Bar --}}
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        @if($isSearching)
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
                                                    </div>
                                                    <div class="text-right">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $product->stores->first()->pivot->quantity > 10 ? 'bg-green-100 text-green-800' : ($product->stores->first()->pivot->quantity > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                            Stock: {{ $product->stores->first()->pivot->quantity }}
                                                        </span>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @elseif(strlen($product_search) >= 2 && !$isSearching)
                                    <div class="mt-4 text-center text-gray-500 py-8">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                        </svg>
                                        <p class="mt-2 text-sm">Aucun produit trouvé avec du stock disponible</p>
                                    </div>
                                @endif
                            </div>
                        </div>

                        {{-- Selected Products --}}
                        @if(count($items) > 0)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <div class="flex items-center justify-between">
                                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Produits sélectionnés') }}</h3>
                                        <div class="flex items-center space-x-4 text-sm text-gray-600">
                                            <span>Total articles: <span class="font-medium">{{ $total_items }}</span></span>
                                            <span>Valeur estimée: <span class="font-medium">@fcfa($estimated_value)</span></span>
                                        </div>
                                    </div>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Stock disponible</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité à transférer</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur</th>
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
                                                                <div class="text-sm text-gray-500">{{ $item['sku'] }} • {{ $item['category'] ?? 'Sans catégorie' }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $item['max_quantity'] > 10 ? 'bg-green-100 text-green-800' : ($item['max_quantity'] > 0 ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                            {{ $item['max_quantity'] }} {{ $item['unit'] ?? 'unité(s)' }}
                                                        </span>
                                                    </td>
                                                    <td class="px-6 py-4 text-center">
                                                        <input type="number" 
                                                               wire:model.lazy="items.{{ $index }}.quantity"
                                                               wire:change="updateQuantity({{ $index }}, $event.target.value)"
                                                               min="1" 
                                                               max="{{ $item['max_quantity'] }}"
                                                               class="w-20 px-2 py-1 text-center border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                        @fcfa(($item['price'] ?? 0) * $item['quantity'])
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
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun produit sélectionné</h3>
                                    <p class="mt-1 text-sm text-gray-500">Commencez par rechercher et ajouter des produits à transférer.</p>
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
                    <div class="space-y-6">
                        {{-- Transfer Summary --}}
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                            <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                <h3 class="text-lg font-semibold text-gray-900">{{ __('Résumé du transfert') }}</h3>
                                <p class="text-sm text-gray-600 mt-1">{{ __('Vérifiez les informations avant de confirmer le transfert') }}</p>
                            </div>

                            <div class="p-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    {{-- Transfer Details --}}
                                    <div class="space-y-4">
                                        <h4 class="text-sm font-medium text-gray-900">{{ __('Détails du transfert') }}</h4>
                                        <dl class="space-y-2">
                                            <div class="flex justify-between text-sm">
                                                <dt class="text-gray-500">Référence:</dt>
                                                <dd class="text-gray-900 font-medium">{{ $reference }}</dd>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <dt class="text-gray-500">Date:</dt>
                                                <dd class="text-gray-900">{{ \Carbon\Carbon::parse($transfer_date)->format('d/m/Y') }}</dd>
                                            </div>
                                            <div class="flex justify-between text-sm">
                                                <dt class="text-gray-500">Motif:</dt>
                                                <dd class="text-gray-900">{{ $transfer_reason }}</dd>
                                            </div>
                                            @if($notes)
                                                <div class="flex justify-between text-sm">
                                                    <dt class="text-gray-500">Notes:</dt>
                                                    <dd class="text-gray-900">{{ $notes }}</dd>
                                                </div>
                                            @endif
                                        </dl>
                                    </div>

                                    {{-- Transfer Stats --}}
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
                                                <dt class="text-gray-500">Valeur estimée:</dt>
                                                <dd class="text-gray-900 font-medium">@fcfa($estimated_value)</dd>
                                            </div>
                                        </dl>
                                    </div>
                                </div>

                                {{-- Store Transfer Info --}}
                                <div class="mt-6 flex items-center justify-center">
                                    <div class="flex items-center space-x-4">
                                        <div class="text-center">
                                            <div class="bg-blue-100 rounded-full p-3 mx-auto">
                                                <svg class="h-6 w-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <p class="mt-2 text-sm font-medium text-gray-900">{{ $this->fromStore?->name }}</p>
                                            <p class="text-xs text-gray-500">Origine</p>
                                        </div>
                                        
                                        <div class="flex-shrink-0">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path>
                                            </svg>
                                        </div>
                                        
                                        <div class="text-center">
                                            <div class="bg-green-100 rounded-full p-3 mx-auto">
                                                <svg class="h-6 w-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                                </svg>
                                            </div>
                                            <p class="mt-2 text-sm font-medium text-gray-900">{{ $this->toStore?->name }}</p>
                                            <p class="text-xs text-gray-500">Destination</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Products Summary --}}
                        @if(count($items) > 0)
                            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                                <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Produits à transférer') }}</h3>
                                </div>

                                <div class="overflow-x-auto">
                                    <table class="min-w-full divide-y divide-gray-200">
                                        <thead class="bg-gray-50">
                                            <tr>
                                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Quantité</th>
                                                <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Valeur unitaire</th>
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
                                                                <div class="text-sm text-gray-500">{{ $item['sku'] }}</div>
                                                            </div>
                                                        </div>
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                        {{ $item['quantity'] }} {{ $item['unit'] ?? 'unité(s)' }}
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm text-gray-900">
                                                        @fcfa($item['price'] ?? 0)
                                                    </td>
                                                    <td class="px-6 py-4 text-center text-sm font-medium text-gray-900">
                                                        @fcfa(($item['price'] ?? 0) * $item['quantity'])
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="bg-gray-50">
                                            <tr>
                                                <td colspan="3" class="px-6 py-3 text-right text-sm font-medium text-gray-900">Total général:</td>
                                                <td class="px-6 py-3 text-center text-sm font-bold text-gray-900">@fcfa($estimated_value)</td>
                                            </tr>
                                        </tfoot>
                                    </table>
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
                                {{ __('Modifier') }}
                            </button>
                            
                            <button type="button" 
                                    wire:click="confirmTransfer"
                                    class="inline-flex items-center px-6 py-2 bg-green-600 text-white text-sm font-medium rounded-lg hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 transition-colors">
                                <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                {{ __('Confirmer le transfert') }}
                            </button>
                        </div>
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
                                        Stock disponible: {{ $selectedProduct->stores->first()?->pivot->quantity ?? 0 }} unité(s)
                                    </p>
                                </div>
                            </div>
                            
                            <div class="mt-5">
                                <label for="modal_quantity" class="block text-sm font-medium text-gray-700 mb-2">
                                    Quantité à transférer
                                </label>
                                <input type="number" 
                                       wire:model="modalQuantity"
                                       id="modal_quantity"
                                       min="1" 
                                       max="{{ $selectedProduct->stores->first()?->pivot->quantity ?? 0 }}"
                                       class="block w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
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
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                        
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-base font-semibold leading-6 text-gray-900">
                                Confirmer le transfert
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir effectuer ce transfert de stock ?<br>
                                    Cette action ne peut pas être annulée.
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
                                Traitement...
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