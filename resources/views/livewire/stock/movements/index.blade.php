<div>
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Historique des Mouvements</h2>
            <p class="mt-1 text-sm leading-6 text-gray-500">Suivez toutes les entrées et sorties de votre inventaire.</p>
        </div>
    </div>
    
    <!-- Section des filtres -->
    <div class="mt-6 bg-white p-4 rounded-xl shadow-sm">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <div class="sm:col-span-2">
                <label for="search" class="block text-sm font-medium text-gray-700">Rechercher un produit</label>
                <input wire:model.live.debounce.300ms="search" type="text" id="search" placeholder="Nom ou SKU..." class="mt-1 block w-full rounded-lg border-gray-300 py-2.5 px-4 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="storeFilter" class="block text-sm font-medium text-gray-700">Magasin</label>
                <select wire:model.live="storeFilter" id="storeFilter" class="mt-1 block w-full rounded-lg border-gray-300 py-2.5 px-4 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les magasins</option>
                    @foreach($stores as $store)
                        <option value="{{ $store->id }}">{{ $store->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="typeFilter" class="block text-sm font-medium text-gray-700">Type de mouvement</label>
                <select wire:model.live="typeFilter" id="typeFilter" class="mt-1 block w-full rounded-lg border-gray-300 py-2.5 px-4 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
                    <option value="">Tous les types</option>
                    @foreach($movementTypes as $type)
                        <option value="{{ $type->value }}">{{ $type->label() }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label for="dateFrom" class="block text-sm font-medium text-gray-700">Du</label>
                <input type="date" wire:model.live="dateFrom" id="dateFrom" class="mt-1 block w-full rounded-lg border-gray-300 py-2.5 px-4 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="dateTo" class="block text-sm font-medium text-gray-700">Au</label>
                <input type="date" wire:model.live="dateTo" id="dateTo" class="mt-1 block w-full rounded-lg border-gray-300 py-2.5 px-4 shadow-sm focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500">
            </div>
            <div class="col-span-full flex justify-end">
                <button wire:click="resetFilters" type="button" class="rounded-lg bg-gray-200 px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm hover:bg-gray-300">Réinitialiser</button>
            </div>
        </div>
    </div>

    <!-- Tableau des mouvements (Desktop) -->
    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-sm font-semibold text-gray-900">Produit</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Magasin</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Quantité</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Source</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($movements as $movement)
                                <tr wire:key="{{ $movement->id }}">
                                    <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm">
                                        <div class="font-medium text-gray-900">{{ $movement->product->name }}</div>
                                        <div class="text-gray-500">{{ $movement->product->sku }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $movement->created_at->format('d/m/Y H:i') }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $movement->store->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $movement->type->label() }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if($movement->source instanceof \App\Models\Document)
                                            <a href="{{ route('documents.show', $movement->source) }}" wire:navigate class="text-indigo-600 hover:underline">{{ $movement->source->document_number }}</a>
                                        @else
                                            -
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center py-10 text-gray-500">Aucun mouvement de stock trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Liste des mouvements (Mobile) -->
    <div class="mt-8 space-y-4 lg:hidden">
        @forelse ($movements as $movement)
            <div class="bg-white p-4 rounded-lg shadow-sm ring-1 ring-black ring-opacity-5" wire:key="mobile-{{ $movement->id }}">
                <div class="flex items-center justify-between">
                    <div class="font-bold text-gray-900">{{ $movement->product->name }}</div>
                    <div class="font-semibold {{ $movement->quantity > 0 ? 'text-green-600' : 'text-red-600' }}">{{ $movement->quantity > 0 ? '+' : '' }}{{ $movement->quantity }}</div>
                </div>
                <div class="mt-2 text-sm text-gray-500 space-y-1">
                    <p><strong>Date:</strong> {{ $movement->created_at->format('d/m/Y H:i') }}</p>
                    <p><strong>Magasin:</strong> {{ $movement->store->name }}</p>
                    <p><strong>Type:</strong> {{ $movement->type->label() }}</p>
                    <p><strong>Source:</strong> @if($movement->source instanceof \App\Models\Document)<a href="{{ route('documents.show', $movement->source) }}" wire:navigate class="text-indigo-600 hover:underline">{{ $movement->source->document_number }}</a>@else-@endif</p>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-gray-500">Aucun mouvement de stock trouvé.</div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $movements->links() }}
    </div>
</div>