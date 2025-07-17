<div class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-indigo-50">
<form wire:submit.prevent="save" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- En-tête modernisé -->
    <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-8 mb-8">
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-r from-orange-500 to-red-500 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                        </svg>
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold text-gray-900">
                            Créer un Avoir
                        </h2>
                        <p class="mt-1 text-sm text-gray-600">
                            Basé sur la facture 
                            <a href="{{ route('documents.show', $sourceDocument) }}" class="inline-flex items-center font-medium text-indigo-600 hover:text-indigo-800 transition-colors duration-200">
                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                {{ $sourceDocument->document_number }}
                            </a>
                        </p>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex space-x-3 sm:mt-0 sm:ml-6">
                <a href="{{ route('documents.show', $sourceDocument) }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors duration-200 shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                    </svg>
                    Annuler
                </a>
                <button type="submit" class="inline-flex items-center px-6 py-2 bg-gradient-to-r from-orange-600 to-red-600 text-white rounded-lg text-sm font-medium shadow-lg hover:from-orange-700 hover:to-red-700 transition-all duration-200 hover:shadow-xl">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                    </svg>
                    Créer l'Avoir
                </button>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Colonne de Gauche : Items modernisée -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-8 py-6 border-b border-gray-200">
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                            </svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-bold text-gray-900">Articles à retourner / créditer</h3>
                            <p class="text-sm text-gray-600 mt-1">Ajustez les quantités des produits retournés. Mettez la quantité à 0 pour ne pas inclure un produit.</p>
                        </div>
                    </div>
                </div>
                <div class="p-8">
                    <div class="overflow-hidden rounded-xl border border-gray-200">
                        <table class="min-w-full">
                            <thead class="bg-gradient-to-r from-gray-50 to-gray-100">
                                <tr>
                                    <th class="px-6 py-4 text-left text-sm font-semibold text-gray-900">Produit</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Quantité à retourner</th>
                                    <th class="px-6 py-4 text-center text-sm font-semibold text-gray-900">Prix unitaire</th>
                                    <th class="px-6 py-4 text-right text-sm font-semibold text-gray-900">Total à créditer</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200 bg-white">
                                @foreach ($items as $index => $item)
                                    <tr wire:key="item-{{ $index }}" class="hover:bg-gray-50 transition-colors duration-150">
                                        <td class="px-6 py-4">
                                            <div class="flex items-center space-x-3">
                                                <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                                    </svg>
                                                </div>
                                                <div>
                                                    <div class="font-medium text-gray-900">{{ $item['name'] }}</div>
                                                    <div class="text-sm text-gray-500">Disponible: {{ $item['max_quantity'] }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="flex items-center justify-center space-x-2">
                                                <input type="number" wire:model.live="items.{{ $index }}.quantity" 
                                                       class="w-20 rounded-lg border-gray-300 text-center text-sm focus:border-orange-500 focus:ring-orange-500" 
                                                       min="0" max="{{ $item['max_quantity'] }}">
                                                <span class="text-xs text-gray-500">/ {{ $item['max_quantity'] }}</span>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-center text-sm text-gray-700">
                                            {{ number_format($item['unit_price'] ?? 0, 0, ',', ' ') }} FCFA
                                        </td>
                                        <td class="px-6 py-4 text-right text-sm font-semibold text-gray-900">
                                            {{ number_format(($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0), 0, ',', ' ') }} FCFA
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne de Droite : Options modernisées -->
        <div class="lg:col-span-1 space-y-6">
            <!-- Options de l'avoir -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-indigo-500 to-purple-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 100 4m0-4v2m0-6V4"></path>
                        </svg>
                        Options de l'Avoir
                    </h3>
                </div>
                <div class="p-6 space-y-6">
                    <div>
                        <label for="document_date" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Date de l'avoir
                        </label>
                        <input type="date" wire:model="document_date" id="document_date" 
                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                            </svg>
                            Notes (Raison du retour, etc.)
                        </label>
                        <textarea wire:model="notes" id="notes" rows="4" 
                                  class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                                  placeholder="Décrivez la raison du retour ou d'autres informations importantes..."></textarea>
                    </div>
                </div>
            </div>
            
            <!-- Récapitulatif -->
            <div class="bg-white rounded-2xl shadow-xl border border-gray-200 overflow-hidden">
                <div class="bg-gradient-to-r from-emerald-500 to-teal-600 px-6 py-4">
                    <h3 class="text-lg font-semibold text-white flex items-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                        </svg>
                        Récapitulatif
                    </h3>
                </div>
                <div class="p-6">
                    <div class="space-y-3">
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Articles sélectionnés</span>
                            <span class="text-sm font-bold text-gray-900">{{ collect($items)->where('quantity', '>', 0)->count() }}</span>
                        </div>
                        <div class="flex justify-between items-center py-2 border-b border-gray-200">
                            <span class="text-sm font-medium text-gray-600">Montant total à créditer</span>
                            <span class="text-lg font-bold text-emerald-600">
                                {{ number_format(collect($items)->sum(function($item) { return ($item['quantity'] ?? 0) * ($item['unit_price'] ?? 0); }), 0, ',', ' ') }} FCFA
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>