<div>
    <!-- En-tête avec les actions -->
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                     <h1 class="text-2xl font-bold leading-7 text-gray-900">Commande Fournisseur #{{ $document->document_number }}</h1>
                     <p class="mt-1 text-sm text-gray-500">Statut : {{ $document->status->label() }}</p>
                </div>
                <div class="mt-5 flex flex-wrap gap-3 sm:mt-0 sm:ml-4">
                    <a href="{{ route('purchases.index') }}" wire:navigate class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                         Retour
                    </a>
                    @if($document->status === \App\Enums\DocumentStatus::Draft)
                        <button wire:click="markAsOrdered" type="button" class="inline-flex items-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Marquer comme Commandé</button>
                    @endif
                    @if($document->status === \App\Enums\DocumentStatus::Ordered)
                        <button wire:click="receiveStock" wire:confirm="Confirmez-vous la réception de tous les articles de cette commande ? Cette action mettra à jour votre stock." type="button" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Réceptionner le Stock</button>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Corps du document -->
    <main class="py-10">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-8">
                <!-- En-tête : Infos entreprise et fournisseur -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                    <div>
                        <h3 class="text-base font-semibold text-gray-900">{{ $document->company->name }}</h3>
                        <address class="mt-2 not-italic text-gray-500">
                            <span class="block">{{ $document->company->address }}</span>
                            <span class="block">{{ $document->company->phone_number }}</span>
                        </address>
                    </div>
                    <div class="mt-8 md:mt-0 md:text-right">
                         <h3 class="text-base font-semibold text-gray-900">Fournisseur</h3>
                        <address class="mt-2 not-italic text-gray-500">
                            <strong class="text-gray-900">{{ $document->supplier->name }}</strong>
                            <span class="block">{{ $document->supplier->address }}</span>
                            <span class="block">{{ $document->supplier->phone_number }}</span>
                        </address>
                    </div>
                </div>
                
                <!-- Détails du document -->
                <div class="mt-16 grid grid-cols-4 gap-y-4 text-sm">
                    <div>
                        <div class="font-semibold text-gray-900">N° Commande</div>
                        <div class="text-gray-500">{{ $document->document_number }}</div>
                    </div>
                     <div>
                        <div class="font-semibold text-gray-900">Date de commande</div>
                        <div class="text-gray-500">{{ $document->document_date->format('d/m/Y') }}</div>
                    </div>
                     <div>
                        <div class="font-semibold text-gray-900">Magasin de destination</div>
                        <div class="text-gray-500">{{ $document->store->name }}</div>
                    </div>
                </div>

                <!-- Tableau des articles -->
                <div class="mt-10">
                    <table class="w-full whitespace-nowrap text-left text-sm leading-6">
                        <thead class="border-b border-gray-200 text-gray-900">
                            <tr>
                                <th scope="col" class="px-0 py-3 font-semibold">Produit</th>
                                <th scope="col" class="hidden py-3 pl-8 pr-0 text-right font-semibold sm:table-cell">Quantité</th>
                                <th scope="col" class="hidden py-3 pl-8 pr-0 text-right font-semibold sm:table-cell">Prix d'Achat U.</th>
                                <th scope="col" class="py-3 pl-8 pr-0 text-right font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($document->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="max-w-0 px-0 py-5 align-top">
                                        <div class="font-medium text-gray-900">{{ $item->description }}</div>
                                    </td>
                                    <td class="hidden py-5 pl-8 pr-0 text-right align-top text-gray-700 sm:table-cell">{{ $item->quantity }}</td>
                                    <td class="hidden py-5 pl-8 pr-0 text-right align-top text-gray-700 sm:table-cell">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                    <td class="py-5 pl-8 pr-0 text-right align-top font-semibold text-gray-900">{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th scope="row" colspan="3" class="hidden px-0 pb-0 pt-6 text-right font-normal text-gray-700 sm:table-cell">Total</th><td class="pb-0 pl-8 pr-0 pt-6 text-right font-semibold text-gray-900">{{ number_format($document->total_amount, 0, ',', ' ') }} XAF</td></tr>
                        </tfoot>
                    </table>
                </div>
            </div>
        </div>
    </main>
</div>