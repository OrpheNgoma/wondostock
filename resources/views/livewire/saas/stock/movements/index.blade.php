<div class="space-y-6">
    <!-- En-tête moderne sobre -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Historique des Mouvements
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Suivez toutes les entrées et sorties de votre inventaire en temps réel
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button
                    wire:click="exportCsv"
                    type="button"
                    class="inline-flex items-center gap-2 rounded-lg border border-gray-300 bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors duration-200"
                >
                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Exporter CSV
                </button>
                <a href="{{ route('stock.entry') }}"
                   class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Entrée de Stock
                </a>
                <a href="{{ route('stock.transfer') }}" 
                    
                   class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5" />
                    </svg>
                    Transfert
                </a>
            </div>
        </div>
    </div>

    <!-- Messages de session -->
    @if (session('success'))
        <div class="mx-6 rounded-lg bg-green-50 p-4 border border-green-200">
            <div class="flex items-center gap-3">
                <div class="h-5 w-5 text-green-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif

    <!-- Filtres et recherche -->
    <div class="mx-6">
        <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Filtres et recherche</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Recherche produit -->
                <div class="sm:col-span-2">
                    <label for="search" class="block text-xs font-medium text-gray-700 mb-2">Rechercher un produit</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               id="search"
                               placeholder="Rechercher par nom de produit ou SKU (ex: PROD-001, T-shirt...)"
                               class="block w-full pl-10 pr-3 py-3 rounded-lg border-gray-300 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                    </div>
                </div>
                
                <!-- Filtre magasin -->
                <div>
                    <label for="storeFilter" class="block text-xs font-medium text-gray-700 mb-2">Magasin</label>
                    <select wire:model.live="storeFilter" 
                            id="storeFilter" 
                            class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                        <option value="">Tous les points de vente</option>
                        @foreach($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Type de mouvement -->
                <div>
                    <label for="typeFilter" class="block text-xs font-medium text-gray-700 mb-2">Type de mouvement</label>
                    <select wire:model.live="typeFilter" 
                            id="typeFilter" 
                            class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                        <option value="">Tous les types de mouvements</option>
                        @foreach($movementTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Filtres de date -->
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 mt-4">
                <div>
                    <label for="dateFrom" class="block text-xs font-medium text-gray-700 mb-2">Date de début</label>
                    <input type="date" 
                           wire:model.live="dateFrom" 
                           id="dateFrom" 
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
                <div>
                    <label for="dateTo" class="block text-xs font-medium text-gray-700 mb-2">Date de fin</label>
                    <input type="date" 
                           wire:model.live="dateTo" 
                           id="dateTo" 
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
            </div>
            
            <!-- Bouton de réinitialisation -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <button wire:click="resetFilters" 
                        type="button" 
                        class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Réinitialiser les filtres
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau des mouvements -->
    <div class="mx-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-gray-200">
            <!-- En-tête du tableau -->
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Mouvements de Stock</h3>
                        <p class="text-xs text-gray-600 mt-1">{{ $movements->total() }} mouvement(s) au total</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                        <span>Historique inventaire</span>
                    </div>
                </div>
            </div>

            <!-- Version desktop -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Produit
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Date & Heure
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Magasin
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Type
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Quantité
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Source
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($movements as $movement)
                            <tr wire:key="{{ $movement->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $movement->product->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $movement->product->sku }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-700">
                                        {{ $movement->created_at->format('d/m/Y') }}
                                        <div class="text-xs text-gray-500">{{ $movement->created_at->format('H:i') }}</div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="h-6 w-6 rounded bg-gray-100 flex items-center justify-center">
                                            <svg class="h-3 w-3 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                            </svg>
                                        </div>
                                        <span class="text-sm text-gray-700">{{ $movement->store->name }}</span>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @php
                                        $typeConfig = match($movement->type->value ?? 'in') {
                                            'in' => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-800', 'ring' => 'ring-emerald-600/20', 'dot' => 'bg-emerald-500'],
                                            'out' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'ring' => 'ring-red-600/20', 'dot' => 'bg-red-500'],
                                            'transfer' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'ring' => 'ring-blue-600/20', 'dot' => 'bg-blue-500'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20', 'dot' => 'bg-gray-500'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full {{ $typeConfig['bg'] }} px-2 py-1 text-xs font-medium {{ $typeConfig['text'] }} ring-1 {{ $typeConfig['ring'] }}">
                                        <div class="h-1.5 w-1.5 rounded-full {{ $typeConfig['dot'] }}"></div>
                                        {{ $movement->type->label() }}
                                    </span>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-2">
                                        @if($movement->quantity > 0)
                                            <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            <span class="text-sm font-semibold text-emerald-600">+{{ $movement->quantity }}</span>
                                        @else
                                            <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 12h-15" />
                                            </svg>
                                            <span class="text-sm font-semibold text-red-600">{{ $movement->quantity }}</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @if($movement->source instanceof \App\Models\Document)
                                        <a href="{{ route('documents.show', $movement->source) }}" 
                                            
                                           class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-2 py-1 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                            {{ $movement->source->document_number }}
                                        </a>
                                    @else
                                        <span class="text-xs text-gray-400">Manuel</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Aucun mouvement trouvé</p>
                                            <p class="text-xs text-gray-500 mt-1">Commencez par enregistrer vos premiers mouvements de stock</p>
                                        </div>
                                        <a href="{{ route('stock.entry') }}" 
                                            
                                           class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Première Entrée
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Version mobile -->
            <div class="lg:hidden divide-y divide-gray-200">
                @forelse ($movements as $movement)
                    <div wire:key="mobile-{{ $movement->id }}" class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $movement->product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $movement->product->sku }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                @if($movement->quantity > 0)
                                    <span class="text-sm font-semibold text-emerald-600">+{{ $movement->quantity }}</span>
                                @else
                                    <span class="text-sm font-semibold text-red-600">{{ $movement->quantity }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-gray-500">Date:</span>
                                <span class="text-gray-900 ml-1">{{ $movement->created_at->format('d/m/Y H:i') }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Magasin:</span>
                                <span class="text-gray-900 ml-1">{{ $movement->store->name }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="text-gray-900 ml-1">{{ $movement->type->label() }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Source:</span>
                                @if($movement->source instanceof \App\Models\Document)
                                    <a href="{{ route('documents.show', $movement->source) }}" 
                                        
                                       class="text-blue-600 ml-1 underline">{{ $movement->source->document_number }}</a>
                                @else
                                    <span class="text-gray-900 ml-1">Manuel</span>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Aucun mouvement trouvé</p>
                                <p class="text-xs text-gray-500 mt-1">Commencez par enregistrer vos premiers mouvements</p>
                            </div>
                            <a href="{{ route('stock.entry') }}" 
                                
                               class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Première Entrée
                            </a>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($movements->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </div>
</div>