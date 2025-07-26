{{-- Statistiques des transferts --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Transferts entrants</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['transfer_stats']['incoming_count'] ?? 0 }}</dd>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Transferts sortants</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['transfer_stats']['outgoing_count'] ?? 0 }}</dd>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">En attente (entrant)</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['transfer_stats']['pending_incoming'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">En attente (sortant)</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['transfer_stats']['pending_outgoing'] ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Transferts entrants --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">📥 Transferts entrants</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                    {{ count($data['incoming_transfers'] ?? []) }} transferts
                </span>
            </div>
            @if(count($data['incoming_transfers'] ?? []) > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($data['incoming_transfers'] as $transfer)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $transfer->reference }}</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $transfer->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($transfer->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($transfer->status->value ?? '') }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $transfer->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p><span class="font-medium">De:</span> {{ $transfer->fromStore->name ?? 'Magasin supprimé' }}</p>
                                <p><span class="font-medium">Articles:</span> {{ $transfer->items->count() }} produits</p>
                                @if($transfer->notes)
                                    <p class="mt-1 text-xs italic">{{ Str::limit($transfer->notes, 50) }}</p>
                                @endif
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="text-xs text-gray-500">
                                    {{ $transfer->items->sum('quantity') }} unités au total
                                </div>
                                @if($transfer->status === 'pending')
                                    <button class="text-xs text-blue-600 hover:text-blue-500 font-medium">
                                        Valider le transfert
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M9 19l3 3m0 0l3-3m-3 3V10"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Aucun transfert entrant</p>
                </div>
            @endif
        </div>
    </div>

    {{-- Transferts sortants --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-medium text-gray-900">📤 Transferts sortants</h3>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                    {{ count($data['outgoing_transfers'] ?? []) }} transferts
                </span>
            </div>
            @if(count($data['outgoing_transfers'] ?? []) > 0)
                <div class="space-y-3 max-h-96 overflow-y-auto">
                    @foreach($data['outgoing_transfers'] as $transfer)
                        <div class="border border-gray-200 rounded-lg p-3 hover:bg-gray-50">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center space-x-2">
                                    <span class="text-sm font-medium text-gray-900">{{ $transfer->reference }}</span>
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full 
                                                {{ $transfer->status === 'completed' ? 'bg-green-100 text-green-800' : 
                                                   ($transfer->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ ucfirst($transfer->status->value ?? '') }}
                                    </span>
                                </div>
                                <span class="text-xs text-gray-500">{{ $transfer->created_at->diffForHumans() }}</span>
                            </div>
                            <div class="text-sm text-gray-600">
                                <p><span class="font-medium">Vers:</span> {{ $transfer->toStore->name ?? 'Magasin supprimé' }}</p>
                                <p><span class="font-medium">Articles:</span> {{ $transfer->items->count() }} produits</p>
                                @if($transfer->notes)
                                    <p class="mt-1 text-xs italic">{{ Str::limit($transfer->notes, 50) }}</p>
                                @endif
                            </div>
                            <div class="mt-2 flex items-center justify-between">
                                <div class="text-xs text-gray-500">
                                    {{ $transfer->items->sum('quantity') }} unités au total
                                </div>
                                @if($transfer->status === 'pending')
                                    <button class="text-xs text-red-600 hover:text-red-500 font-medium">
                                        Annuler le transfert
                                    </button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                    </svg>
                    <p class="mt-2 text-sm text-gray-500">Aucun transfert sortant</p>
                </div>
            @endif
        </div>
    </div>
</div>

{{-- Actions rapides --}}
<div class="mt-6 bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-4">⚡ Actions rapides</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <a href="{{ route('stock.transfer') }}" 
               class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Nouveau transfert</span>
                <span class="block text-xs text-gray-500">Créer un transfert entre magasins</span>
            </a>

            <a href="{{ route('stock.movements.index') }}?type=transfer&status=pending" 
               class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Transferts en attente</span>
                <span class="block text-xs text-gray-500">Valider les transferts en cours</span>
            </a>

            <a href="{{ route('stock.movements.index') }}?type=transfer" 
               class="relative block w-full border-2 border-gray-300 border-dashed rounded-lg p-6 text-center hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="mx-auto h-8 w-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span class="mt-2 block text-sm font-medium text-gray-900">Historique complet</span>
                <span class="block text-xs text-gray-500">Voir tous les transferts</span>
            </a>
        </div>
    </div>
</div>