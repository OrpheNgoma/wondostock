<div>
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between print:hidden">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900">Impression d'Étiquettes</h2>
            <p class="mt-1 text-sm text-gray-500">Sélectionnez les produits et imprimez leurs étiquettes avec codes-barres.</p>
        </div>
        <div class="mt-5 flex gap-3 sm:mt-0 sm:ml-4">
            @if(count($productsToPrint) > 0)
                <span class="inline-flex items-center rounded-md bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">
                    {{ count($productsToPrint) }} produits, {{ $this->getTotalLabels() }} étiquettes
                </span>
                <button wire:click="clearAll" type="button" class="inline-flex items-center rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-500">
                    Vider
                </button>
                <button onclick="window.print()" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                    Imprimer
                </button>
            @else
                <span class="text-sm text-gray-500">Sélectionnez des produits pour commencer</span>
            @endif
        </div>
    </div>
    
    <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <!-- Colonne de Gauche : Sélection des produits -->
        <div class="md:col-span-1 print:hidden">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Ajouter des produits</h3>
                <div class="mt-4 relative">
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Rechercher un produit..." class="w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @if(count($searchResults) > 0)
                    <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-auto">
                        @foreach($searchResults as $product)
                        <li wire:click="addProduct({{ $product->id }})" class="p-2 hover:bg-gray-100 cursor-pointer">{{ $product->name }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                <div class="mt-6">
                    <h4 class="text-sm font-medium text-gray-700">Produits à imprimer :</h4>
                    <ul class="mt-2 space-y-2">
                        @forelse($productsToPrint as $index => $product)
                        <li wire:key="print-{{$product['id']}}" class="flex items-center justify-between p-2 bg-gray-50 rounded-md">
                            <span class="text-sm text-gray-800">{{ $product['name'] }}</span>
                            <div class="flex items-center gap-2">
                                <input 
                                    type="number" 
                                    wire:model.blur="productsToPrint.{{$index}}.quantity" 
                                    min="1" 
                                    max="100" 
                                    class="w-16 rounded-md border-gray-300 text-center text-sm"
                                    title="Nombre d'étiquettes (1-100)"
                                >
                                <button 
                                    wire:click="removeProduct({{$index}})" 
                                    class="text-red-500 hover:text-red-700 font-bold text-lg"
                                    title="Retirer le produit"
                                >&times;</button>
                            </div>
                        </li>
                        @empty
                        <li class="text-center text-sm text-gray-500 py-4">Aucun produit sélectionné.</li>
                        @endforelse
                    </ul>
                </div>
            </div>
        </div>

        <!-- Colonne de Droite : Prévisualisation de l'impression -->
        <div class="md:col-span-2">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4">
                    @foreach($productsToPrint as $product)
                        @for($i = 0; $i < $product['quantity']; $i++)
                            <div class="text-center border border-dashed border-gray-300 p-2 rounded-lg">
                                <p class="text-xs font-bold truncate">{{ $product['name'] }}</p>
                                <p class="text-xs text-gray-600">SKU: {{ $product['sku'] }}</p>
                                <p class="text-xs font-semibold">{{ number_format($product['price'], 0, ',', ' ') }} FCFA</p>
                                <div class="mt-2">
                                    <img 
                                        src="{{ route('products.barcode', ['sku' => $product['sku']]) }}" 
                                        alt="Code-barres {{ $product['sku'] }}" 
                                        class="mx-auto max-w-full h-auto"
                                        style="max-height: 40px; min-width: 100px;"
                                        onload="this.style.display='block'"
                                        onerror="this.style.display='none'; this.nextElementSibling.style.display='block'"
                                    >
                                    <span class="text-xs text-red-500 hidden">Erreur Code-barres</span>
                                </div>
                            </div>
                        @endfor
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .md\:col-span-2, .md\:col-span-2 * {
                visibility: visible;
            }
            .md\:col-span-2 {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
        }
    </style>
</div>