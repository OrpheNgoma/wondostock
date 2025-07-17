{{-- KPIs principaux --}}
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Ventes aujourd'hui</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['kpis']['today_sales'], 0, ',', ' ') }} FCFA</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Valeur du stock</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['kpis']['stock_value'], 0, ',', ' ') }} FCFA</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-yellow-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.858-.833-2.632 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Stock critique</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['kpis']['low_stock_count'] }} produits</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Produits en stock</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['kpis']['total_products'] }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Alertes importantes --}}
@if(count($data['alerts'] ?? []) > 0)
    <div class="mb-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">⚠️ Alertes importantes</h3>
        <div class="space-y-3">
            @foreach($data['alerts'] as $alert)
                <div class="p-4 rounded-lg border-l-4 {{ $alert['type'] === 'warning' ? 'bg-yellow-50 border-yellow-400' : 'bg-blue-50 border-blue-400' }}">
                    <div class="flex justify-between items-start">
                        <div>
                            <h4 class="text-sm font-medium {{ $alert['type'] === 'warning' ? 'text-yellow-800' : 'text-blue-800' }}">
                                {{ $alert['title'] }}
                            </h4>
                            <p class="mt-1 text-sm {{ $alert['type'] === 'warning' ? 'text-yellow-700' : 'text-blue-700' }}">
                                {{ $alert['message'] }}
                            </p>
                        </div>
                        <a href="{{ $alert['url'] }}" 
                           class="text-sm font-medium {{ $alert['type'] === 'warning' ? 'text-yellow-800 hover:text-yellow-900' : 'text-blue-800 hover:text-blue-900' }}">
                            {{ $alert['action'] }} →
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endif

{{-- Activités récentes --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Mouvements de stock récents --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📦 Mouvements récents</h3>
            @if(count($data['recent_movements'] ?? []) > 0)
                <div class="space-y-3">
                    @foreach($data['recent_movements'] as $movement)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center {{ $movement->quantity > 0 ? 'bg-green-100 text-green-600' : 'bg-red-100 text-red-600' }}">
                                        @if($movement->quantity > 0)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        @endif
                                    </div>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $movement->product->name ?? 'Produit supprimé' }}</p>
                                    <p class="text-xs text-gray-500">{{ $movement->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}
                                </p>
                                <p class="text-xs text-gray-500">{{ ucfirst($movement->type->value ?? '') }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('stock.movements.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Voir tous les mouvements →
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucun mouvement récent</p>
            @endif
        </div>
    </div>

    {{-- Ventes récentes --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">💰 Ventes récentes</h3>
            @if(count($data['recent_sales'] ?? []) > 0)
                <div class="space-y-3">
                    @foreach($data['recent_sales'] as $sale)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <div>
                                <p class="text-sm font-medium text-gray-900">
                                    {{ $sale->customer->name ?? 'Client supprimé' }}
                                </p>
                                <p class="text-xs text-gray-500">
                                    Facture #{{ $sale->document_number }} • {{ $sale->created_at->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-green-600">{{ number_format($sale->total_amount, 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-500 capitalize">{{ $sale->status->value ?? '' }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    <a href="{{ route('documents.index') }}" class="text-sm font-medium text-blue-600 hover:text-blue-500">
                        Voir toutes les ventes →
                    </a>
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune vente récente</p>
            @endif
        </div>
    </div>
</div>