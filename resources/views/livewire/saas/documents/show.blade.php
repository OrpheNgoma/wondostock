<div>
    <!-- En-tête avec les actions -->
    <div x-data="{ showPaymentForm: @entangle('showPaymentForm') }">
    <!-- En-tête avec les actions -->
    <header class="bg-white shadow">
        <div class="mx-auto max-w-7xl px-4 py-4 sm:px-6 lg:px-8">
            <div class="sm:flex sm:items-center sm:justify-between">
                <div class="min-w-0 flex-1">
                     <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        {{ $document->type->label() }} #{{ $document->document_number }}
                    </h1>
                     <p class="mt-1 flex items-center text-sm text-gray-500">
                        Statut : {{ $document->status->label() }}
                        @if($document->validated_at)
                            <span class="mx-2">&middot;</span>
                            Validé le {{ $document->validated_at->format('d/m/Y') }}
                        @endif
                    </p>
                </div>
                <div class="mt-5 flex flex-wrap gap-3 sm:mt-0 sm:ml-4">
                     <a href="{{ route('documents.index') }}"  class="inline-flex items-center rounded-md bg-yellow-500 px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                         Retour
                    </a>
                    
                    {{-- On ne peut valider qu'un brouillon --}}
                    @if($document->status === \App\Enums\DocumentStatus::Draft)
                    <button wire:click="validateDocument" wire:confirm="Êtes-vous sûr ? Cette action va déduire les produits du stock et ne pourra pas être annulée." type="button" class="inline-flex items-center rounded-md bg-green-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 hover:bg-blue-700">
                        Valider le Document
                    </button>
                    @endif

                    <span class="ml-3 hidden sm:block">
                        {{-- Le bouton appelle une méthode Livewire --}}
                        <button wire:click="downloadPdf" type="button" class="inline-flex items-center rounded-md bg-blue-600 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 hover:bg-green-700">
                             <span wire:loading.remove wire:target="downloadPdf">
                                Télécharger PDF
                            </span>
                            <span wire:loading wire:target="downloadPdf">
                                Génération...
                            </span>
                        </button>
                    </span>

                    {{-- Le bouton de conversion --}}
                    @if(in_array($document->type, [\App\Enums\DocumentType::Quote, \App\Enums\DocumentType::Order]) && !$document->convertedToDocument)
                    <span class="sm:ml-3">
                        <button wire:click="convertToInvoice" wire:confirm="Êtes-vous sûr de vouloir convertir ce document en facture ?" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                           Convertir en Facture
                        </button>
                    </span>
                    @endif
                    
                    {{-- On ne peut enregistrer un paiement que si le document est une facture validée et non entièrement payée --}}
                    @if($document->type === \App\Enums\DocumentType::Invoice && $document->status !== \App\Enums\DocumentStatus::Draft && $document->status !== \App\Enums\DocumentStatus::Paid)
                    <span class="sm:ml-3">
                        <button wire:click="openPaymentForm" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                           Enregistrer un paiement
                        </button>
                    </span>
                    @endif

                    {{-- On ne peut créer un avoir que depuis une facture validée ou payée --}}
                    @if(in_array($document->status, [\App\Enums\DocumentStatus::Validated, \App\Enums\DocumentStatus::Paid, \App\Enums\DocumentStatus::PartiallyPaid]) && $document->type === \App\Enums\DocumentType::Invoice)
                        <a href="{{ route('documents.credit-note.create', $document) }}"  class="inline-flex items-center rounded-md bg-green-700 px-3 py-2 text-sm font-semibold text-white shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-green-500 hover:text-gray-900">
                            Créer un Avoir
                        </a>
                    @endif
                </div>
            </div>
        </div>
    </header>

    <!-- Corps de la facture -->
    <main class="py-10 grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="md:col-span-2">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-8">
                <!-- En-tête de la facture : Logo, infos entreprise, client -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8">
                    <div>
                        <img class="h-22 w-auto" src="../images/WondoStock-Logo.png" alt="KaziFlow Logo">
                        <h3 class="mt-6 text-base font-semibold text-gray-900">{{ $document->company->name }}</h3>
                        <address class="mt-2 not-italic text-gray-500">
                            <span class="block">{{ $document->company->address }}</span>
                            <span class="block">{{ $document->company->phone_number }}</span>
                        </address>
                    </div>
                    <div class="mt-8 md:mt-0 md:text-right">
                         <h3 class="text-base font-semibold text-gray-900">Facturé à</h3>
                        <address class="mt-2 not-italic text-gray-500">
                            <strong class="text-gray-900">{{ $document->customer->name }}</strong>
                            <span class="block">{{ $document->customer->address }}</span>
                            <span class="block">{{ $document->customer->phone_number }}</span>
                        </address>
                    </div>
                </div>
                
                <!-- Détails du document : Numéro, date, etc. -->
                <div class="mt-16 grid grid-cols-4 gap-y-4 text-sm">
                    <div>
                        <div class="font-semibold text-gray-900">N° Document</div>
                        <div class="text-gray-500">{{ $document->document_number }}</div>
                    </div>
                     <div>
                        <div class="font-semibold text-gray-900">Date d'émission</div>
                        <div class="text-gray-500">{{ $document->document_date->format('d/m/Y') }}</div>
                    </div>
                     <div>
                        <div class="font-semibold text-gray-900">Magasin</div>
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
                                <th scope="col" class="hidden py-3 pl-8 pr-0 text-right font-semibold sm:table-cell">Prix U.</th>
                                <th scope="col" class="py-3 pl-8 pr-0 text-right font-semibold">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($document->items as $item)
                                <tr class="border-b border-gray-100">
                                    <td class="max-w-0 px-0 py-5 align-top">
                                        <div class="font-medium text-gray-900">{{ $item->product->name }}</div>
                                    </td>
                                    <td class="hidden py-5 pl-8 pr-0 text-right align-top text-gray-700 sm:table-cell">{{ $item->quantity }}</td>
                                    <td class="hidden py-5 pl-8 pr-0 text-right align-top text-gray-700 sm:table-cell">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                    <td class="py-5 pl-8 pr-0 text-right align-top font-semibold text-gray-900">{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr><th scope="row" class="px-0 pb-0 pt-6 font-normal text-gray-700 sm:hidden">Sous-total</th><th scope="row" colspan="3" class="hidden px-0 pb-0 pt-6 text-right font-normal text-gray-700 sm:table-cell">Sous-total</th><td class="pb-0 pl-8 pr-0 pt-6 text-right text-gray-900">{{ number_format($document->sub_total, 0, ',', ' ') }} XAF</td></tr>
                            <tr><th scope="row" class="pt-4 font-normal text-gray-700 sm:hidden">Taxe</th><th scope="row" colspan="3" class="hidden pt-4 text-right font-normal text-gray-700 sm:table-cell">Taxe</th><td class="pl-8 pr-0 pt-4 text-right text-gray-900">{{ number_format($document->tax_amount, 0, ',', ' ') }} XAF</td></tr>
                            <tr><th scope="row" class="pt-4 font-semibold text-gray-900 sm:hidden">Total</th><th scope="row" colspan="3" class="hidden pt-4 text-right font-semibold text-gray-900 sm:table-cell">Total</th><td class="pl-8 pr-0 pt-4 text-right font-semibold text-gray-900">{{ number_format($document->total_amount, 0, ',', ' ') }} XAF</td></tr>
                        </tfoot>
                    </table>
                </div>

                <!-- Notes -->
                @if($document->notes)
                    <div class="mt-10 border-t border-gray-200 pt-6">
                        <h3 class="text-base font-semibold text-gray-900">Notes</h3>
                        <p class="mt-2 text-sm text-gray-500">{{ $document->notes }}</p>
                    </div>
                @endif
            </div>
        </div>
    <!-- Colonne latérale : Paiements -->
        <div class="md:col-span-1">
            <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl p-6">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Historique des Paiements</h3>
                <dl class="mt-6 space-y-4">
                    @forelse($document->payments as $payment)
                        <div class="flex items-start justify-between">
                            <div>
                                <dt class="text-sm font-medium text-gray-900">{{ number_format($payment->amount, 0, ',', ' ') }} FCFA</dt>
                                <dd class="text-xs text-gray-500">{{ $payment->payment_date->format('d/m/Y') }} via {{ $payment->payment_method->label() }}</dd>
                                {{-- On affiche la référence si elle existe --}}
                                @if($payment->reference)
                                <dd class="text-xs text-gray-500">Réf: {{ $payment->reference }}</dd>
                                @endif
                                <dd class="text-xs text-gray-500">par {{ $payment->user->name }}</dd>
                            </div>
                            <button type="button" class="text-xs text-red-500 hover:text-red-700">Supprimer</button>
                        </div>
                    @empty
                         <p class="text-sm text-gray-500">Aucun paiement enregistré.</p>
                    @endforelse
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <dt class="text-sm font-medium text-gray-900">Total Payé</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ number_format($document->paid_amount, 0, ',', ' ') }} FCFA</dd>
                    </div>
                    <div class="flex items-center justify-between border-t border-gray-200 pt-4">
                        <dt class="text-base font-semibold text-gray-900">Solde Restant</dt>
                        <dd class="text-base font-semibold text-red-600">{{ number_format($document->total_amount - $document->paid_amount, 0, ',', ' ') }} FCFA</dd>
                    </div>
                </dl>
            </div>
        </div>
    </main>
    
    <!-- Panneau latéral pour enregistrer un paiement -->
    <div x-show="showPaymentForm" x-cloak class="relative z-10">
        <div x-show="showPaymentForm" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showPaymentForm = false" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showPaymentForm" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" class="pointer-events-auto w-screen max-w-md">
                        <form wire:submit.prevent="recordPayment" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll py-6">
                                <div class="px-4 sm:px-6"><div class="flex items-start justify-between"><h2 class="text-base font-semibold leading-6 text-gray-900">Enregistrer un Paiement</h2><div class="ml-3 flex h-7 items-center"><button @click="showPaymentForm = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button></div></div></div>
                                <div x-data="{ paymentMethod: @entangle('payment_method') }" class="relative mt-6 flex-1 px-4 sm:px-6 space-y-6">
                                    <div><label for="payment_amount" class="block text-sm font-medium text-gray-900">Montant (FCFA) *</label><input type="number" wire:model="payment_amount" id="payment_amount" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('payment_amount')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="payment_date" class="block text-sm font-medium text-gray-900">Date du paiement *</label><input type="date" wire:model="payment_date" id="payment_date" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('payment_date')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="payment_method" class="block text-sm font-medium text-gray-900">Méthode *</label><select wire:model.live="payment_method" id="payment_method" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@foreach(\App\Enums\PaymentMethod::cases() as $method)<option value="{{ $method->value }}">{{ $method->label() }}</option>@endforeach</select></div>
                                    
                                    {{-- Champ conditionnel pour la référence de transaction --}}
                                    <div x-show="paymentMethod === 'airtel_money' || paymentMethod === 'moov_money' || paymentMethod === 'bank_transfer'" x-transition>
                                        <label for="payment_reference" class="block text-sm font-medium text-gray-900">Référence de la transaction</label>
                                        <input type="text" wire:model="payment_reference" id="payment_reference" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                                    </div>

                                    <div><label for="payment_notes" class="block text-sm font-medium text-gray-900">Notes</label><textarea wire:model="payment_notes" id="payment_notes" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea></div>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end px-4 py-4"><button @click="showPaymentForm = false" type="button" class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</button><button type="submit" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Enregistrer Paiement</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>