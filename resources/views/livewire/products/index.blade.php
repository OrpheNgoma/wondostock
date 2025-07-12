<div>
    @if ($showForm)
        <!-- Affiche le composant formulaire -->
        <div>
            <livewire:products.product-form :product="$editingProduct" />
        </div>
    @else
        <!-- En-tête de la page -->
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Catalogue Produits</h2>
                <p class="mt-1 text-sm text-gray-500">Consultez et gérez l'ensemble de vos produits.</p>
            </div>
            <div class="mt-5 flex sm:mt-0 sm:ml-4">
                <button wire:click="create" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" /></svg>
                    Nouveau Produit
                </button>
            </div>
        </div>
        
        <!-- Filtres et recherche -->
        <div class="mt-6">
            <input 
                wire:model.live.debounce.300ms="search" 
                type="text" 
                placeholder="Rechercher un produit par nom..."
                class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
            >
        </div>

        <!-- Tableau des produits -->
        <div class="mt-8 flow-root">
            <div class="inline-block min-w-full py-2 align-middle sm:px-1">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Produit</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Catégorie</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">SKU</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Stock Total</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Prix de Vente</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"><span>Actions</span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($products as $product)
                                <tr wire:key="{{ $product->id }}">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="flex items-center">
                                             <div class="h-10 w-10 flex-shrink-0">
                                                {{-- Affichage robuste de l'image --}}
                                                <img class="h-10 w-10 rounded-md object-cover" 
                                                    src="{{ $product->getFirstMediaUrl('images') ?: 'https://placehold.co/40x40/e2e8f0/64748b?text=Img' }}" 
                                                    alt="Image de {{ $product->name }}"
                                                    onerror="this.src='https://placehold.co/40x40/e2e8f0/64748b?text=Img'">
                                            </div>
                                            <div class="ml-4">
                                                <div class="font-medium text-gray-900">{{ $product->name }}</div>
                                                
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-800">{{ $product->category?->name ?? 'Non classé' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $product->sku }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $product->stores->sum('pivot.quantity') }} {{ $product->unit?->name ?? 'pce' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-semibold">{{ number_format($product->selling_price, 0, ',', ' ') }} FCFA</td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('products.edit', $product) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">Modifier</a>
                                        <button wire:click="delete({{ $product->id }})" wire:confirm="Êtes-vous sûr ?" class="ml-4 text-red-600 hover:text-red-900">Supprimer</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-10 text-gray-500">Aucun produit trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">
                    {{ $products->links() }}
                </div>
            </div>
        </div>
    @endif
</div>