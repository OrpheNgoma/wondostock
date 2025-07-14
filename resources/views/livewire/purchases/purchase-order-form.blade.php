<form wire:submit.prevent="save">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <h2 class="text-2xl font-bold leading-7 text-gray-900">
            {{ $document->exists ? 'Modifier le Bon de Commande' : 'Nouveau Bon de Commande' }}
        </h2>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <a href="{{ route('purchases.index') }}"  class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Sauvegarder</button>
        </div>
    </div>
    
    <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <!-- Colonne de Gauche : Infos & Items -->
        <div class="md:col-span-2 space-y-8">
             <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3 relative">
                        <label for="supplier_search" class="block text-sm font-medium leading-6 text-gray-900">Fournisseur *</label>
                        <input type="text" wire:model.live.debounce.300ms="supplier_search" id="supplier_search" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        @if(count($suppliers_list) > 0)
                        <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-auto">
                            @foreach($suppliers_list as $supplier)
                            <li wire:click="selectSupplier({{ $supplier->id }})" class="p-2 hover:bg-gray-100 cursor-pointer">{{ $supplier->name }}</li>
                            @endforeach
                        </ul>
                        @endif
                        @error('supplier_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                     <div class="sm:col-span-3">
                        <label for="document_date" class="block text-sm font-medium leading-6 text-gray-900">Date de commande *</label>
                        <input type="date" wire:model="document_date" id="document_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    </div>
                    <div class="sm:col-span-3">
                        <label for="store_id" class="block text-sm font-medium leading-6 text-gray-900">Magasin de destination *</label>
                        <select wire:model="store_id" id="store_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @foreach($stores as $store) <option value="{{ $store->id }}">{{ $store->name }}</option> @endforeach
                        </select>
                        @error('store_id') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="flow-root">
                     <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50"><tr><th class="py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">Produit</th><th class="px-3 py-2 text-left text-sm font-semibold text-gray-900">Qté</th><th class="px-3 py-2 text-left text-sm font-semibold text-gray-900">Prix d'Achat</th><th class="px-3 py-2 text-left text-sm font-semibold text-gray-900">Total</th><th></th></tr></thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($items as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">{{ $item['name'] }}</td>
                                    <td class="px-3 py-2"><input type="number" wire:model.live="items.{{ $index }}.quantity" class="w-20 rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-indigo-600"></td>
                                    <td class="px-3 py-2"><input type="number" wire:model.live="items.{{ $index }}.unit_price" class="w-32 rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-indigo-600"></td>
                                    <td class="px-3 py-2 text-sm text-gray-500">{{ number_format($item['quantity'] * $item['unit_price'], 0, ',', ' ') }}</td>
                                    <td class="pr-3"><button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center py-4 text-gray-500">Commencez par ajouter un produit.</td></tr>
                            @endforelse
                        </tbody>
                     </table>
                </div>
                <!-- Add Product -->
                <div class="mt-4 relative">
                    <input type="text" wire:model.live.debounce.300ms="product_search" placeholder="Rechercher un produit à ajouter..." class="w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @if(count($products_list) > 0)
                    <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-auto">
                        @foreach($products_list as $product)
                        <li wire:click="addProduct({{ $product->id }})" class="p-2 hover:bg-gray-100 cursor-pointer">{{ $product->name }}</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
            </div>
        </div>
        <!-- Colonne de Droite : Totaux -->
        <div class="grid grid-cols-1 gap-y-8">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Résumé</h3>
                <dl class="mt-6 space-y-4">
                    <div class="flex items-center justify-between"><dt class="text-sm text-gray-600">Sous-total</dt><dd class="text-sm font-medium text-gray-900">{{ number_format($sub_total, 0, ',', ' ') }} XAF</dd></div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4"><dt class="text-base font-semibold text-gray-900">Total</dt><dd class="text-base font-semibold text-gray-900">{{ number_format($total_amount, 0, ',', ' ') }} XAF</dd></div>
                </dl>
            </div>
        </div>
    </div>
</form>