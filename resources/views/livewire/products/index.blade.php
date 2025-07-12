<div class="space-y-8">
    @if ($showForm)
        <!-- Formulaire moderne -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <livewire:products.product-form :product="$editingProduct" />
        </div>
    @else
        <!-- En-tête moderne avec gradient -->
        <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-500 via-purple-600 to-indigo-700 p-8 shadow-2xl">
            <div class="absolute inset-0 bg-gradient-to-br from-purple-500/20 to-indigo-700/20 backdrop-blur-sm"></div>
            <div class="relative">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-2">
                            Catalogue Produits
                        </h1>
                        <p class="text-purple-100 text-lg">
                            Consultez et gérez l'ensemble de vos produits
                        </p>
                    </div>
                    <div class="flex items-center gap-3">
                        <button wire:click="create" 
                                type="button" 
                                class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Nouveau Produit
                        </button>
                    </div>
                </div>
            </div>
            <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
            <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
        </div>
        
        <!-- Barre de recherche moderne -->
        <div class="relative">
            <div class="relative rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                        </svg>
                    </div>
                    <input 
                        wire:model.live.debounce.300ms="search" 
                        type="text" 
                        placeholder="Rechercher un produit par nom, SKU ou catégorie..."
                        class="block w-full pl-12 pr-4 py-4 rounded-xl border-0 bg-gray-50 text-gray-900 placeholder:text-gray-400 focus:bg-white focus:ring-2 focus:ring-purple-500 focus:border-transparent transition-all duration-200 sm:text-sm"
                    >
                </div>
            </div>
        </div>

        <!-- Tableau moderne des produits -->
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <!-- En-tête du tableau -->
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-lg font-semibold text-gray-900">Liste des Produits</h3>
                        <p class="text-sm text-gray-600">{{ $products->total() }} produit(s) au total</p>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-gray-500">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                        </svg>
                        Vue grille
                    </div>
                </div>
            </div>

            <!-- Contenu du tableau -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">
                                <div class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    Produit
                                </div>
                            </th>
                            <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Catégorie</th>
                            <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">SKU</th>
                            <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Stock</th>
                            <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Prix</th>
                            <th scope="col" class="relative py-4 pl-3 pr-6">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-50 bg-white">
                        @forelse ($products as $product)
                            <tr wire:key="{{ $product->id }}" class="group hover:bg-gradient-to-r hover:from-purple-50/50 hover:to-indigo-50/50 transition-all duration-200">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-4">
                                        <div class="relative h-12 w-12 flex-shrink-0">
                                            <img class="h-12 w-12 rounded-xl object-cover shadow-sm ring-1 ring-gray-200" 
                                                src="{{ $product->getFirstMediaUrl('images') ?: 'https://placehold.co/48x48/f3f4f6/6b7280?text=' . urlencode(substr($product->name, 0, 2)) }}" 
                                                alt="Image de {{ $product->name }}"
                                                onerror="this.src='https://placehold.co/48x48/f3f4f6/6b7280?text=' + encodeURIComponent('{{ substr($product->name, 0, 2) }}')">
                                            <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-green-400 ring-2 ring-white"></div>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <p class="text-sm font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200">
                                                {{ $product->name }}
                                            </p>
                                            @if($product->description)
                                                <p class="text-xs text-gray-500 truncate mt-1">
                                                    {{ Str::limit($product->description, 40) }}
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @if($product->category)
                                        <span class="inline-flex items-center rounded-full bg-purple-50 px-2 py-1 text-xs font-medium text-purple-700 ring-1 ring-purple-600/20">
                                            {{ $product->category->name }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-50 px-2 py-1 text-xs font-medium text-gray-500 ring-1 ring-gray-200">
                                            Non classé
                                        </span>
                                    @endif
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm">
                                        <code class="bg-gray-100 text-gray-800 px-2 py-1 rounded text-xs font-mono">
                                            {{ $product->sku }}
                                        </code>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-2">
                                        @php
                                            $totalStock = $product->stores->sum('pivot.quantity');
                                            $stockStatus = $totalStock <= ($product->low_stock_threshold ?? 5) ? 'low' : 'good';
                                        @endphp
                                        @if($stockStatus === 'low')
                                            <svg class="h-4 w-4 text-red-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                        @else
                                            <svg class="h-4 w-4 text-green-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                        @endif
                                        <span class="text-sm font-medium {{ $stockStatus === 'low' ? 'text-red-600' : 'text-green-600' }}">
                                            {{ $totalStock }}
                                        </span>
                                        <span class="text-xs text-gray-500">
                                            {{ $product->unit?->name ?? 'pce' }}
                                        </span>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($product->selling_price, 0, ',', ' ') }}
                                        <span class="text-xs text-gray-500 font-normal">XAF</span>
                                    </div>
                                </td>
                                <td class="relative py-4 pl-3 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('products.edit', $product) }}" 
                                           wire:navigate 
                                           class="inline-flex items-center gap-1 rounded-lg bg-purple-50 px-3 py-2 text-xs font-medium text-purple-700 hover:bg-purple-100 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Modifier
                                        </a>
                                        <button wire:click="delete({{ $product->id }})" 
                                                wire:confirm="Êtes-vous sûr de vouloir supprimer ce produit ?" 
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-lg font-medium text-gray-900">Aucun produit trouvé</p>
                                            <p class="text-sm text-gray-500 mt-1">Commencez par ajouter votre premier produit</p>
                                        </div>
                                        <button wire:click="create" 
                                                class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Ajouter un produit
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination moderne -->
            @if($products->hasPages())
                <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    @endif
</div>