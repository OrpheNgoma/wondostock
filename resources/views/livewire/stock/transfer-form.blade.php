<form wire:submit.prevent="save">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Transfert de Stock
            </h2>
            <p class="mt-1 text-sm text-gray-500">Déplacez des produits d'un magasin à un autre.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <a href="{{ route('dashboard') }}"  class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Enregistrer le Transfert</button>
        </div>
    </div>
    
    <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-8">
        <div class="space-y-8">
             <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-2">
                        <label for="from_store_id" class="block text-sm font-medium leading-6 text-gray-900">Magasin d'origine *</label>
                        <select wire:model.live="from_store_id" id="from_store_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @foreach($stores as $store) <option value="{{ $store->id }}">{{ $store->name }}</option> @endforeach
                        </select>
                    </div>
                    <div class="sm:col-span-2">
                        <label for="to_store_id" class="block text-sm font-medium leading-6 text-gray-900">Magasin de destination *</label>
                        <select wire:model="to_store_id" id="to_store_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                            @foreach($stores as $store) <option value="{{ $store->id }}">{{ $store->name }}</option> @endforeach
                        </select>
                        @error('to_store_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="sm:col-span-2">
                        <label for="transfer_date" class="block text-sm font-medium leading-6 text-gray-900">Date du transfert *</label>
                        <input type="date" wire:model="transfer_date" id="transfer_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    </div>
                     <div class="sm:col-span-full">
                        <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notes / Référence</label>
                        <input type="text" wire:model="notes" id="notes" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>
            </div>

            <!-- Items -->
            <div class="bg-white mt-8 p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="flow-root">
                     <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50"><tr><th class="py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">Produit</th><th class="px-3 py-2 text-left text-sm font-semibold text-gray-900">Quantité à transférer</th><th></th></tr></thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($items as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">{{ $item['name'] }} <span class="text-gray-500">({{ $item['sku'] }})</span></td>
                                    <td class="px-3 py-2"><input type="number" wire:model.live="items.{{ $index }}.quantity" max="{{ $item['max_quantity'] }}" class="w-24 rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-indigo-600"></td>
                                    <td class="pr-3"><button type="button" wire:click="removeItem({{ $index }})" class="text-red-500 hover:text-red-700">&times;</button></td>
                                </tr>
                            @empty
                                <tr><td colspan="3" class="text-center py-4 text-gray-500">Commencez par ajouter un produit.</td></tr>
                            @endforelse
                        </tbody>
                     </table>
                </div>
                <!-- Add Product -->
                <div class="mt-4 relative">
                    <input type="text" wire:model.live.debounce.300ms="product_search" placeholder="Rechercher un produit (nom ou SKU)..." class="w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @if(count($products_list) > 0)
                    <ul class="absolute z-10 w-full bg-white border border-gray-300 rounded-md mt-1 max-h-60 overflow-auto">
                        @foreach($products_list as $product)
                        <li wire:click="addProduct({{ $product->id }})" class="p-2 hover:bg-gray-100 cursor-pointer">{{ $product->name }} (Stock: {{ $product->stores->first()->pivot->quantity }})</li>
                        @endforeach
                    </ul>
                    @endif
                </div>
                @error('items')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
            </div>
        </div>
    </div>
</form>