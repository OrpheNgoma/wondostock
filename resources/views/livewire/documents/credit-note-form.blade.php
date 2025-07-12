<form wire:submit.prevent="save">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Créer un Avoir
            </h2>
            <p class="mt-1 text-sm text-gray-500">
                Basé sur la facture <a href="{{ route('documents.show', $sourceDocument) }}" class="font-medium text-indigo-600 hover:underline">{{ $sourceDocument->document_number }}</a>
            </p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <a href="{{ route('documents.show', $sourceDocument) }}" wire:navigate class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</a>
            <button type="submit" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Créer l'Avoir</button>
        </div>
    </div>
    
    <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <!-- Colonne de Gauche : Items -->
        <div class="md:col-span-2">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Articles à retourner / créditer</h3>
                <p class="text-sm text-gray-500">Ajustez les quantités des produits retournés. Mettez la quantité à 0 pour ne pas inclure un produit.</p>
                <div class="mt-6 flow-root">
                     <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="py-2 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-3">Produit</th>
                                <th class="px-3 py-2 text-left text-sm font-semibold text-gray-900">Quantité à retourner</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @foreach ($items as $index => $item)
                                <tr wire:key="item-{{ $index }}">
                                    <td class="py-2 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-3">{{ $item['name'] }}</td>
                                    <td class="px-3 py-2">
                                        <input type="number" wire:model.live="items.{{ $index }}.quantity" class="w-24 rounded-md border-0 py-1 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-indigo-600" min="0" max="{{ $item['max_quantity'] }}">
                                        <span class="text-xs text-gray-500">/ {{ $item['max_quantity'] }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                     </table>
                </div>
            </div>
        </div>

        <!-- Colonne de Droite : Options -->
        <div class="grid grid-cols-1 gap-y-8">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Options de l'Avoir</h3>
                 <div class="mt-6">
                    <label for="document_date" class="block text-sm font-medium leading-6 text-gray-900">Date de l'avoir</label>
                    <input type="date" wire:model="document_date" id="document_date" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                 </div>
                 <div class="mt-6">
                    <label for="notes" class="block text-sm font-medium leading-6 text-gray-900">Notes (Raison du retour, etc.)</label>
                    <textarea wire:model="notes" id="notes" rows="4" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                 </div>
            </div>
        </div>
    </div>
</form>