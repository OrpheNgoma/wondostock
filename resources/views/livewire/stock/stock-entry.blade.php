<div x-data="stockEntry" class="min-h-screen bg-gradient-to-br from-emerald-50 via-white to-blue-50">
    
    <!-- Header avec indicateurs en temps réel -->
    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-lg border-b border-emerald-200 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-500 to-green-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Entrée de Stock</h1>
                            <p class="text-sm text-gray-600">Gestion intelligente de l'inventaire</p>
                        </div>
                    </div>
                    
                    <!-- Indicateurs live -->
                    <div class="hidden lg:flex items-center space-x-4">
                        <div class="flex items-center space-x-2 px-3 py-2 bg-emerald-100 rounded-lg">
                            <div class="w-3 h-3 bg-emerald-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-emerald-700">{{ count($items) }} produit(s)</span>
                        </div>
                        <div class="flex items-center space-x-2 px-3 py-2 bg-blue-100 rounded-lg">
                            <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-blue-700">{{ now()->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <a href="{{ route('dashboard') }}" 
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        ← Retour
                    </a>
                    
                    <button form="stock-entry-form" type="submit" 
                            :disabled="!hasItems"
                            :class="hasItems ? 'bg-gradient-to-r from-emerald-600 to-green-600 hover:from-emerald-700 hover:to-green-700 text-white' : 'bg-gray-400 cursor-not-allowed text-gray-200'"
                            class="px-6 py-2 text-sm font-semibold rounded-lg transition-all shadow-lg">
                        <span wire:loading.remove wire:target="save">💾 Enregistrer l'Entrée</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
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

    <form id="stock-entry-form" wire:submit.prevent="save" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Configuration de l'entrée -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
            
            <!-- Informations principales -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                        <h2 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Configuration de l'entrée
                        </h2>
                    </div>
                    
                    <div class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Magasin de destination <span class="text-red-500">*</span>
                                </label>
                                <select wire:model.live="store_id" 
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
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Date d'entrée <span class="text-red-500">*</span>
                                </label>
                                <input type="date" wire:model="entry_date" 
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('entry_date') 
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p> 
                                @enderror
                            </div>
                            
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">Type d'entrée</label>
                                <select wire:model="type" 
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="purchase">📦 Achat / Livraison</option>
                                    <option value="transfer_in">🔄 Transfert Entrant</option>
                                    <option value="adjustment">⚖️ Ajustement d'Inventaire</option>
                                    <option value="return">↩️ Retour Client</option>
                                    <option value="production">🏭 Production Interne</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="mt-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Notes / Référence
                            </label>
                            <input type="text" wire:model="notes" 
                                   placeholder="Ex: N° BL Fournisseur, Ajustement d'inventaire..."
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Résumé en temps réel -->
            <div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-4">
                        <h3 class="text-lg font-semibold text-white flex items-center">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 00-2-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                            Résumé Live
                        </h3>
                    </div>
                    
                    <div class="p-6">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Articles ajoutés</span>
                                <span class="text-lg font-bold text-emerald-600">{{ count($items) }}</span>
                            </div>
                            
                            <div class="flex justify-between items-center">
                                <span class="text-sm text-gray-600">Quantité totale</span>
                                <span class="text-lg font-bold text-blue-600">
                                    {{ array_sum(array_column($items, 'quantity')) }}
                                </span>
                            </div>
                            
                            @if(count($items) > 0)
                            <div class="border-t pt-4">
                                <h4 class="text-sm font-medium text-gray-700 mb-2">Derniers ajouts</h4>
                                <div class="space-y-2 max-h-32 overflow-y-auto">
                                    @foreach(array_slice($items, -3) as $item)
                                    <div class="flex justify-between text-xs">
                                        <span class="text-gray-600 truncate">{{ $item['name'] }}</span>
                                        <span class="font-medium text-emerald-600">+{{ $item['quantity'] }}</span>
                                    </div>
                                    @endforeach
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Produits - Cœur de l'application -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="bg-gradient-to-r from-emerald-500 to-green-600 px-6 py-4">
                <h2 class="text-xl font-semibold text-white flex items-center justify-between">
                    <span class="flex items-center">
                        <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                        Gestion des Produits
                    </span>
                    <div class="flex items-center space-x-4 text-sm opacity-90">
                        <span>📦 {{ count($items) }} produit(s)</span>
                        <span>📊 {{ array_sum(array_column($items, 'quantity')) }} unités</span>
                    </div>
                </h2>
            </div>

            <div class="p-6">
                <!-- Recherche super-intelligente -->
                <div class="mb-8">
                    <div class="relative" x-data="{ focused: false }">
                        <div class="relative">
                            <input type="text" 
                                   wire:model.live.debounce.300ms="product_search"
                                   @focus="focused = true; playSound('focus')"
                                   @blur="setTimeout(() => focused = false, 200)"
                                   placeholder="🔍 Recherche intelligente : nom, SKU, code-barres... (Scanner compatible)"
                                   class="w-full pl-12 pr-4 py-4 text-lg border-2 border-emerald-300 rounded-xl focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500 transition-all bg-gradient-to-r from-white to-emerald-50">
                            <svg class="absolute left-4 top-4.5 w-5 h-5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                            </svg>
                        </div>

                        <!-- Dropdown résultats avancé -->
                        @if(count($products_list) > 0)
                        <div class="absolute z-50 w-full mt-2 bg-white border-2 border-emerald-200 rounded-xl shadow-2xl max-h-80 overflow-auto">
                            @foreach($products_list as $product)
                            <div wire:click="addProduct({{ $product->id }})" 
                                 @click="
                                    @if(($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5))
                                        playSound('alert')
                                    @elseif(($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5) * 2)
                                        playSound('warning')
                                    @else
                                        playSound('success')
                                    @endif
                                 "
                                 class="px-6 py-4 hover:bg-emerald-50 cursor-pointer border-b border-emerald-100 last:border-b-0 group transition-colors {{ ($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5) ? 'bg-red-50 border-red-200' : (($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5) * 2 ? 'bg-orange-50 border-orange-200' : '') }}">
                                <div class="flex items-center justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-3">
                                            <div class="w-10 h-10 bg-gradient-to-r from-emerald-400 to-green-500 rounded-lg flex items-center justify-center">
                                                <span class="text-white font-bold text-sm">{{ substr($product->name, 0, 2) }}</span>
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-900 group-hover:text-emerald-700">{{ $product->name }}</div>
                                                <div class="text-sm text-gray-500">
                                                    SKU: {{ $product->sku ?? 'N/A' }} • 
                                                    Catégorie: {{ $product->category->name ?? 'Aucune' }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <div class="text-sm text-gray-500">Stock actuel</div>
                                        <div class="flex items-center space-x-2">
                                            <div class="font-bold text-lg {{ ($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5) ? 'text-red-600' : 'text-emerald-600' }}">
                                                {{ $product->stock_quantity ?? 0 }}
                                            </div>
                                            @if(($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5))
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-red-500 animate-pulse" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs text-red-600 font-medium">RUPTURE</span>
                                            </div>
                                            @elseif(($product->stock_quantity ?? 0) <= ($product->min_stock ?? 5) * 2)
                                            <div class="flex items-center space-x-1">
                                                <svg class="w-4 h-4 text-orange-500" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                                </svg>
                                                <span class="text-xs text-orange-600 font-medium">FAIBLE</span>
                                            </div>
                                            @endif
                                        </div>
                                    </div>
                                    <svg class="w-5 h-5 text-emerald-400 ml-4 group-hover:text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                    </svg>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Table des produits moderne -->
                @if(count($items) > 0)
                <div class="overflow-hidden rounded-xl border-2 border-emerald-200">
                    <table class="min-w-full divide-y divide-emerald-200">
                        <thead class="bg-gradient-to-r from-emerald-500 to-green-600">
                            <tr>
                                <th class="px-6 py-4 text-left text-sm font-semibold text-white uppercase tracking-wider">Produit</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-white uppercase tracking-wider w-32">Stock Actuel</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-white uppercase tracking-wider w-40">Quantité à Ajouter</th>
                                <th class="px-6 py-4 text-center text-sm font-semibold text-white uppercase tracking-wider w-32">Nouveau Stock</th>
                                <th class="px-6 py-4 w-16"></th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-emerald-100">
                            @foreach ($items as $index => $item)
                            <tr wire:key="item-{{ $index }}" class="hover:bg-emerald-50 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-12 h-12 bg-gradient-to-r from-emerald-400 to-green-500 rounded-lg flex items-center justify-center">
                                            <span class="text-white font-bold">{{ substr($item['name'], 0, 2) }}</span>
                                        </div>
                                        <div>
                                            <div class="font-semibold text-gray-900">{{ $item['name'] }}</div>
                                            <div class="text-sm text-gray-500">SKU: {{ $item['sku'] ?? 'N/A' }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-gray-100 text-gray-800">
                                        {{ $this->getCurrentStock($item['product_id']) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <div class="flex items-center justify-center space-x-2">
                                        <button type="button" 
                                                wire:click="decrementQuantity({{ $index }})"
                                                class="w-8 h-8 bg-red-100 hover:bg-red-200 text-red-600 rounded-lg flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                            </svg>
                                        </button>
                                        <input type="number" 
                                               wire:model.live="items.{{ $index }}.quantity"
                                               min="1"
                                               @input="playSound('input')"
                                               class="w-20 px-3 py-2 text-center text-lg font-bold border-2 border-emerald-300 rounded-lg focus:ring-4 focus:ring-emerald-200 focus:border-emerald-500">
                                        <button type="button" 
                                                wire:click="incrementQuantity({{ $index }})"
                                                class="w-8 h-8 bg-emerald-100 hover:bg-emerald-200 text-emerald-600 rounded-lg flex items-center justify-center transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span class="inline-flex items-center px-4 py-2 rounded-full text-lg font-bold bg-emerald-100 text-emerald-800">
                                        {{ $this->getCurrentStock($item['product_id']) + $item['quantity'] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <button type="button" 
                                            wire:click="removeItem({{ $index }})"
                                            @click="playSound('delete')"
                                            class="text-red-500 hover:text-red-700 hover:bg-red-50 p-2 rounded-lg transition-colors group-hover:visible"
                                            title="Supprimer">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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
                <div class="text-center py-16 border-2 border-dashed border-emerald-300 rounded-xl bg-gradient-to-r from-emerald-50 to-green-50">
                    <div class="w-24 h-24 mx-auto mb-4 bg-gradient-to-r from-emerald-400 to-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-semibold text-gray-900 mb-2">Prêt pour l'inventaire</h3>
                    <p class="text-gray-600 mb-6">Recherchez et ajoutez vos produits ci-dessus pour commencer</p>
                    <div class="flex items-center justify-center space-x-4 text-sm text-gray-500">
                        <span>💡 Scanner de code-barres compatible</span>
                        <span>•</span>
                        <span>🚀 Recherche intelligente</span>
                        <span>•</span>
                        <span>⚡ Mise à jour en temps réel</span>
                    </div>
                </div>
                @endif

                @error('items') 
                <div class="mt-4 p-4 bg-red-50 border border-red-200 rounded-lg">
                    <p class="text-sm text-red-600">{{ $message }}</p>
                </div>
                @enderror
            </div>
        </div>
    </form>

    <!-- Notifications sonores -->
    <div class="hidden">
        <audio id="sound-focus" preload="auto">
            <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEeBC+Bz/DFbyMELojS8t2PPwYTVbTq5K5fFwlymd7rzHIpAjx+ze7VgzQIGGS65uOgWBULU6Xm87hhGgU2jd3ySHQrAjl+yt6LMwcYZLfp5Z5NFAtHntrtyl4mAy5sz/LNdSsGNILR6t+RQgkPUrDt46JaEApBmuDsx2QfBSp2y+/ZhTkIMmm98+eVTgUIU6fm8bZiHAU5jNz0yXQpBDN/y+/WgDUIGWO36+eXTggKUKzp8LZeHAVHl9nt3ZnLYkF4gm8=" type="audio/wav">
        </audio>
        <audio id="sound-success" preload="auto">
            <source src="data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEeBC+Bz/DFbyMELojS8t2PPwYTVbTq5K5fFwlymd7rzHIpAjx+ze7VgzQIGGS65uOgWBULU6Xm87hhGgU2jd3ySHQrAjl+yt6LMwcYZLfp5Z5NFAtHntrtyl4mAy5sz/LNdSsGNILR6t+RQgkPUrDt46JaEApBmuDsx2QfBSp2y+/ZhTkIMmm98+eVTgUIU6fm8bZiHAU5jNz0yXQpBDN/y+/WgDUIGWO36+eXTggKUKzp8LZeHAVHl9nt3ZnLYkF4gm8=" type="audio/wav">
        </audio>
        <audio id="sound-input" preload="auto">
            <source src="data:audio/wav;base64,UklGRrQGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YZAGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEeBC+Bz/DFbyMELojS8t2PPwYTVbTq5K5fFwlymd7rzHIpAjx+ze7VgzQIGGS65uOgWBULU6Xm87hhGgU2jd3ySHQrAjl+yt6LMwcYZLfp5Z5NFAtHntrtyl4mAy5sz/LNdSsGNILR6t+RQgkPUrDt46JaEApBmuDsx2QfBSp2y+/ZhTkIMmm98+eVTgUIU6fm8bZiHAU5jNz0yXQpBDN/y+/WgDUIGWO36+eXTggKUKzp8LZeHAVHl9nt3ZnJXCEYQrKg" type="audio/wav">
        </audio>
        <audio id="sound-delete" preload="auto">
            <source src="data:audio/wav;base64,UklGRrQGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YZAGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmEeBC+Bz/DFbyMELojS8t2PPwYTVbTq5K5fFwlymd7rzHIpAjx+ze7VgzQIGGS65uOgWBULU6Xm87hhGgU2jd3ySHQrAjl+yt6LMwcYZLfp5Z5NFAtHntrtyl4mAy5sz/LNdSsGNILR6t+RQgkPUrDt46JaEApBmuDsx2QfBSp2y+/ZhTkIMmm98+eVTgUIU6fm8bZiHAU5jNz0yXQpBDN/y+/WgDUIGWO36+eXTggKUKzp8LZeHAVHl9nt3ZnJXCEYQrKg" type="audio/wav">
        </audio>
        <audio id="sound-alert" preload="auto">
            <source src="data:audio/wav;base64,UklGRhgCAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQEAAAC4tbqctbwcu7u8u7q7vLy7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7" type="audio/wav">
        </audio>
        <audio id="sound-warning" preload="auto">
            <source src="data:audio/wav;base64,UklGRhgDAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQEAAAC4tbqctbwDu7u8u7q7vLy7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7vLu8u7y7" type="audio/wav">
        </audio>
    </div>

    <!-- Scripts Alpine.js -->
    <script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('stockEntry', () => ({
            hasItems: @entangle('items').live,
            
            get hasItems() {
                return this.hasItems && this.hasItems.length > 0;
            },
            
            playSound(type) {
                try {
                    const audio = document.getElementById(`sound-${type}`);
                    if (audio) {
                        audio.currentTime = 0;
                        audio.volume = 0.3;
                        audio.play().catch(() => {});
                    }
                } catch (e) {}
            },
            
            init() {
                // Feedback visuel au chargement
                this.$nextTick(() => {
                    this.playSound('focus');
                });
            }
        }));
    });
    </script>

    <!-- Notifications -->
    <div x-data="{ show: false, message: '', type: 'success' }" 
         @notify.window="show = true; message = $event.detail.message; type = $event.detail.type || 'success'; setTimeout(() => show = false, 5000)"
         x-show="show" x-transition
         :class="type === 'success' ? 'bg-emerald-500' : 'bg-red-500'"
         class="fixed top-4 right-4 z-50 text-white px-6 py-4 rounded-lg shadow-2xl">
        <div class="flex items-center space-x-2">
            <svg x-show="type === 'success'" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
            </svg>
            <span x-text="message"></span>
        </div>
    </div>
</div>