<div x-data="{ 
        showShortcuts: false,
        init() {
            this.$nextTick(() => {
                document.addEventListener('keydown', (e) => {
                    // Ctrl+S pour sauvegarder
                    if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        console.log('Ctrl+S détecté');
                        this.$wire.save();
                        return;
                    }
                    // Ctrl+P pour recherche produit
                    if (e.ctrlKey && e.key === 'p') {
                        e.preventDefault();
                        console.log('Ctrl+P détecté');
                        const productSearch = document.querySelector('[data-search-product]');
                        if (productSearch) {
                            productSearch.focus();
                            productSearch.select();
                        }
                        return;
                    }
                    // Échap pour fermer les popups
                    if (e.key === 'Escape') {
                        console.log('Escape détecté');
                        this.showShortcuts = false;
                        if (document.activeElement && document.activeElement.tagName === 'INPUT') {
                            document.activeElement.blur();
                        }
                    }
                });
            });
        }
    }" 
    class="min-h-screen bg-gradient-to-br from-slate-50 to-blue-50">
    <!-- Header Modern avec gradient -->
    <div class="sticky top-0 z-10 bg-white/80 backdrop-blur-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-16">
                <div class="flex items-center space-x-4">
                    <div class="flex items-center space-x-2">
                        <div class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></div>
                        <h1 class="text-xl font-bold text-gray-900">
                            {{ $document->exists ? 'Modifier la vente' : 'Nouvelle vente' }}
                        </h1>
                    </div>
                    <div class="hidden md:flex items-center space-x-2 text-sm text-gray-500">
                        <span>•</span>
                        <span>{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Raccourcis clavier -->
                    <button @click="showShortcuts = !showShortcuts" 
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors relative"
                            title="Raccourcis clavier (actifs)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                    </button>
                    
                    <a href="{{ route('documents.index') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors inline-block">
                        ← Retour
                    </a>
                    
                    <button form="document-form" type="submit" 
                            class="px-6 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-blue-700 rounded-lg hover:from-blue-700 hover:to-blue-800 transition-all shadow-lg shadow-blue-500/25">
                        <span wire:loading.remove wire:target="save">Enregistrer</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sauvegarde...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Popup raccourcis clavier -->
    <div x-show="showShortcuts" x-transition 
         @click.outside="showShortcuts = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl p-6 mx-4 max-w-md shadow-2xl">
            <h3 class="text-lg font-semibold mb-4">Raccourcis clavier</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>Enregistrer</span><kbd class="px-2 py-1 bg-gray-100 rounded">Ctrl + S</kbd></div>
                <div class="flex justify-between"><span>Recherche produit</span><kbd class="px-2 py-1 bg-gray-100 rounded">Ctrl + P</kbd></div>
                <div class="flex justify-between"><span>Navigation</span><kbd class="px-2 py-1 bg-gray-100 rounded">Tab</kbd></div>
                <div class="flex justify-between"><span>Échap pour fermer</span><kbd class="px-2 py-1 bg-gray-100 rounded">Esc</kbd></div>
            </div>
            <button @click="showShortcuts = false" class="mt-4 w-full px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Fermer</button>
        </div>
    </div>

    <form id="document-form" wire:submit.prevent="save" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Section Client/Magasin - Optimisée pour la rapidité -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            
            <!-- Colonne principale : Client + Produits -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Client Selection Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-visible">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                            Informations client
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Client Search -->
                            <div class="relative" x-data="{ focused: false }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Client <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <input type="text" 
                                           wire:model.live.debounce.300ms="customer_search" 
                                           data-search-customer
                                           @focus="focused = true"
                                           @blur="setTimeout(() => focused = false, 200)"
                                           placeholder="Rechercher ou saisir le nom du client..."
                                           class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                    <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>
                                
                                <!-- Dropdown clients -->
                                @if(count($customers_list) > 0)
                                <div class="absolute z-50 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-60 overflow-auto">
                                    @foreach($customers_list as $customer)
                                    <div wire:click="selectCustomer({{ $customer->id }})" 
                                         class="px-4 py-3 hover:bg-blue-50 cursor-pointer border-b border-gray-100 last:border-b-0 flex items-center justify-between">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ $customer->name }}</div>
                                            @if($customer->email)
                                            <div class="text-sm text-gray-500">{{ $customer->email }}</div>
                                            @endif
                                        </div>
                                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                                        </svg>
                                    </div>
                                    @endforeach
                                </div>
                                @endif
                                
                                @error('customer_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>

                            <!-- Store Selection -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Magasin <span class="text-red-500">*</span>
                                </label>
                                <select wire:model="store_id" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Choisir un magasin</option>
                                    @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                    @endforeach
                                </select>
                                @error('store_id') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Produits Section -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-green-500 to-green-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center justify-between">
                            <span class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                                </svg>
                                Articles ({{ count($items) }})
                            </span>
                            <div class="text-sm opacity-90">
                                Total: {{ number_format($total_amount, 0, ',', ' ') }} XAF
                            </div>
                        </h2>
                    </div>

                    <div class="p-6">
                        <!-- Ajout rapide de produit -->
                        <div class="mb-6 relative" x-data="{ focused: false }">
                            <div class="relative">
                                <input type="text" 
                                       wire:model.live.debounce.300ms="product_search"
                                       data-search-product
                                       @focus="focused = true"
                                       @blur="setTimeout(() => focused = false, 200)" 
                                       placeholder="🔍 Rechercher un produit à ajouter... (Ctrl+P)"
                                       class="w-full pl-12 pr-4 py-4 text-lg border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors">
                                <svg class="absolute left-4 top-4.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                </svg>
                            </div>

                            <!-- Dropdown produits -->
                            @if(count($products_list) > 0)
                            <div class="absolute z-50 w-full mt-2 bg-white border border-gray-200 rounded-lg shadow-xl max-h-80 overflow-auto">
                                @foreach($products_list as $product)
                                <div wire:click="addProduct({{ $product->id }})" 
                                     class="px-6 py-4 hover:bg-green-50 cursor-pointer border-b border-gray-100 last:border-b-0 flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                        <div class="text-sm text-gray-500">
                                            Stock: {{ $product->stock_quantity ?? 0 }} • 
                                            Catégorie: {{ $product->category->name ?? 'Aucune' }}
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="font-semibold text-green-600">{{ number_format($product->selling_price, 0, ',', ' ') }} XAF</div>
                                    </div>
                                    <svg class="w-5 h-5 text-gray-400 ml-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>

                        <!-- Table des articles -->
                        @if(count($items) > 0)
                        <div class="overflow-hidden rounded-lg border border-gray-200">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produit</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-24">Qté</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-44">Prix unit.</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider w-36">Total</th>
                                        <th class="px-4 py-3 w-12"></th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach ($items as $index => $item)
                                    <tr wire:key="item-{{ $index }}" class="hover:bg-gray-50 transition-colors">
                                        <td class="px-4 py-4">
                                            <div class="font-medium text-gray-900">{{ $item['name'] }}</div>
                                            <div class="text-sm text-gray-500">{{ $item['description'] }}</div>
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" 
                                                   wire:model.live="items.{{ $index }}.quantity"
                                                   min="1"
                                                   step="1"
                                                   class="w-full px-3 py-2 text-center border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </td>
                                        <td class="px-4 py-4">
                                            <input type="number" 
                                                   wire:model.live="items.{{ $index }}.unit_price"
                                                   min="0"
                                                   step="0.01"
                                                   class="w-full px-3 py-2 text-right border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                        </td>
                                        <td class="px-4 py-4 text-right font-semibold text-gray-900">
                                            {{ number_format($item['quantity'] * $item['unit_price'], 0, ',', ' ') }} XAF
                                        </td>
                                        <td class="px-4 py-4 text-center">
                                            <button type="button" 
                                                    wire:click="removeItem({{ $index }})"
                                                    class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors"
                                                    title="Supprimer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                                </svg>
                                            </button>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @else
                        <div class="text-center py-12 border-2 border-dashed border-gray-300 rounded-lg">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                            <p class="mt-2 text-lg font-medium text-gray-900">Aucun article ajouté</p>
                            <p class="text-gray-500">Recherchez un produit ci-dessus pour commencer</p>
                        </div>
                        @endif

                        @error('items') 
                        <p class="mt-2 text-sm text-red-600">{{ $message }}</p> 
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Sidebar : Options + Totaux -->
            <div class="space-y-6">
                
                <!-- Options Document -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data="{ type: @entangle('type') }">
                    <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"/>
                            </svg>
                            Options
                        </h3>
                    </div>
                    
                    <div class="p-6 space-y-6">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Type de document</label>
                            <select wire:model.live="type" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                                @foreach($documentTypes as $docType)
                                <option value="{{ $docType->value }}">{{ $docType->label() }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date d'émission</label>
                            <input type="date" wire:model="document_date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                        </div>

                        <div x-show="type === 'invoice' || type === 'proforma'" x-transition>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Date d'échéance</label>
                            <input type="date" wire:model="due_date" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500">
                            @error('due_date') 
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                            @enderror
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                            <textarea wire:model="notes" rows="4" 
                                      placeholder="Notes internes ou commentaires..."
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Résumé/Totaux -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-amber-500 to-amber-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/>
                            </svg>
                            Résumé
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <dl class="space-y-4">
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-600">Articles</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ count($items) }}</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-600">Sous-total</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($sub_total, 0, ',', ' ') }} XAF</dd>
                            </div>
                            <div class="flex justify-between items-center">
                                <dt class="text-sm text-gray-600">Taxes</dt>
                                <dd class="text-sm font-medium text-gray-900">{{ number_format($tax_amount, 0, ',', ' ') }} XAF</dd>
                            </div>
                            <div class="border-t border-gray-200 pt-4">
                                <div class="flex justify-between items-center">
                                    <dt class="text-lg font-semibold text-gray-900">Total</dt>
                                    <dd class="text-2xl font-bold text-amber-600">{{ number_format($total_amount, 0, ',', ' ') }} XAF</dd>
                                </div>
                            </div>
                        </dl>
                    </div>
                </div>

                <!-- Actions rapides -->
                @if(count($items) > 0)
                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-200">
                    <h4 class="font-semibold text-gray-900 mb-3">Actions rapides</h4>
                    <div class="space-y-2">
                        <button type="button" 
                                wire:click="saveDraft"
                                class="w-full px-4 py-2 text-sm text-blue-700 bg-blue-100 rounded-lg hover:bg-blue-200 transition-colors">
                            💾 Sauvegarder comme brouillon
                        </button>
                        <button type="button" 
                                wire:click="saveAndValidate"
                                class="w-full px-4 py-2 text-sm text-green-700 bg-green-100 rounded-lg hover:bg-green-200 transition-colors">
                            ✅ Valider et envoyer
                        </button>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </form>

</div>