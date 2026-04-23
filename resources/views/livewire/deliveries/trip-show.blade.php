<div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('deliveries.index') }}" wire:navigate
               class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">
                    Tournée — {{ $trip->driver->name }}
                </h1>
                <p class="text-sm text-gray-500">
                    {{ $trip->trip_date->translatedFormat('l d MMMM Y') }}
                    @if($trip->zone) · {{ $trip->zone->name }} ({{ $trip->zone->city }}) @endif
                    @if($trip->vehicle) · {{ $trip->vehicle->display_name }} @endif
                </p>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold
                {{ $trip->status->value === 'draft' ? 'bg-gray-100 text-gray-700' : '' }}
                {{ $trip->status->value === 'in_progress' ? 'bg-blue-100 text-blue-700' : '' }}
                {{ $trip->status->value === 'completed' ? 'bg-amber-100 text-amber-700' : '' }}
                {{ $trip->status->value === 'closed' ? 'bg-green-100 text-green-700' : '' }}">
                {{ $trip->status->label() }}
            </span>
            @if($trip->status->value !== 'closed')
            <a href="{{ route('deliveries.edit', $trip) }}" wire:navigate
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Modifier</a>
            @endif
        </div>
    </div>

    {{-- Progress bar workflow --}}
    @php
        $allSteps = [
            ['val' => 'draft',       'num' => '1', 'lbl' => 'Brouillon',  'desc' => 'Ajoutez chaque produit chargé avec sa quantité, puis validez le départ.'],
            ['val' => 'in_progress', 'num' => '2', 'lbl' => 'En route',   'desc' => 'Saisissez les produits vendus, les dépenses et la recette encaissée.'],
            ['val' => 'completed',   'num' => '3', 'lbl' => 'Retourné',   'desc' => 'Vérifiez le résumé financier, puis clôturez la tournée.'],
            ['val' => 'closed',      'num' => '4', 'lbl' => 'Clôturé',    'desc' => 'Tournée finalisée. Rapport et facture disponibles en PDF.'],
        ];
        $statusOrder = ['draft','in_progress','completed','closed'];
        $currentIdx  = array_search($trip->status->value, $statusOrder);
        $currentStep = $allSteps[$currentIdx] ?? null;
    @endphp
    <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
        <div class="px-5 pt-5 pb-4">
            <ol class="flex items-center w-full">
                @foreach($allSteps as $step)
                @php
                    $stepIdx   = array_search($step['val'], $statusOrder);
                    $isDone    = $stepIdx < $currentIdx;
                    $isCurrent = $stepIdx === $currentIdx;
                @endphp
                <li class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                    <span class="flex items-center justify-center w-9 h-9 rounded-full shrink-0 text-sm font-bold transition-all
                        {{ $isDone    ? 'bg-indigo-600 text-white shadow-sm' : '' }}
                        {{ $isCurrent ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-500 ring-offset-2' : '' }}
                        {{ !$isDone && !$isCurrent ? 'bg-gray-100 text-gray-400' : '' }}">
                        @if($isDone)
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                            </svg>
                        @else
                            {{ $step['num'] }}
                        @endif
                    </span>
                    <span class="ms-2.5 text-xs font-semibold hidden sm:block
                        {{ $isCurrent ? 'text-indigo-700' : ($isDone ? 'text-gray-500' : 'text-gray-400') }}">
                        {{ $step['lbl'] }}
                    </span>
                    @if(!$loop->last)
                        <div class="flex-1 mx-3 h-0.5 rounded-full {{ $isDone ? 'bg-indigo-400' : 'bg-gray-200' }}"></div>
                    @endif
                </li>
                @endforeach
            </ol>
        </div>
        {{-- Description de l'étape courante --}}
        @if($currentStep && $trip->status->value !== 'closed')
        <div class="border-t border-indigo-100 bg-indigo-50/60 px-5 py-3 flex items-start gap-2.5">
            <svg class="h-4 w-4 text-indigo-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
            </svg>
            <p class="text-sm text-indigo-700">
                <span class="font-semibold">Étape {{ $currentStep['num'] }} — {{ $currentStep['lbl'] }} : </span>
                {{ $currentStep['desc'] }}
            </p>
        </div>
        @endif
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- ─── ÉTAPE 1 : CHARGEMENT PAR PRODUIT ─── --}}
            @if($trip->canLoad())
            <div class="rounded-xl border border-indigo-200 bg-white"
                 x-data="{
                     get totalQty()    { return $wire.loadingRows.reduce((s, r) => s + (parseInt(r.qty)||0), 0); },
                     get totalAmount() { return $wire.loadingRows.reduce((s, r) => s + (parseInt(r.qty)||0) * (parseInt(r.unit_price)||0), 0); },
                     get totalMargin() { return $wire.loadingRows.reduce((s, r) => s + (parseInt(r.qty)||0) * (parseInt(r.margin_per_unit)||0), 0); },
                     fmt(n) { return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }
                 }">
                <div class="bg-indigo-600 rounded-t-xl px-5 py-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold shrink-0">1</span>
                    <h2 class="text-sm font-semibold text-white">Chargement du véhicule</h2>
                </div>
                <div class="bg-indigo-50 border-b border-indigo-100 px-5 py-3 flex items-start gap-2">
                    <svg class="h-4 w-4 text-indigo-400 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    <p class="text-xs text-indigo-700">
                        Ajoutez chaque produit chargé dans le véhicule avec sa quantité (en cassiers).
                        Le prix et la marge sont pré-remplis selon la zone <strong>{{ $trip->zone?->name ?? 'non définie' }}</strong>.
                        Cliquez sur <strong>« Valider départ »</strong> pour lancer la tournée.
                    </p>
                </div>
                <div class="p-5 space-y-4">

                    {{-- Recherche produit --}}
                    <div class="relative">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ajouter un produit</label>
                        <input type="text"
                               wire:model.live.debounce.300ms="loading_search"
                               placeholder="Tapez le nom du produit (REGAB, CASTEL…)"
                               autocomplete="off"
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-indigo-200 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @if(count($loading_list) > 0)
                        <ul class="absolute z-20 w-full mt-1 bg-white border border-indigo-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-gray-50">
                            @foreach($loading_list as $p)
                            <li wire:click="addToLoading({{ $p['id'] }})"
                                class="px-4 py-2.5 text-sm hover:bg-indigo-50 cursor-pointer flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="font-semibold text-gray-900 block truncate">{{ $p['name'] }}</span>
                                    @if($p['sku'])
                                    <code class="text-xs text-gray-400">{{ $p['sku'] }}</code>
                                    @endif
                                </div>
                                <div class="text-right shrink-0">
                                    @if($p['has_zone_price'])
                                        <span class="block text-xs font-bold text-indigo-700">{{ number_format($p['unit_price'], 0, ',', ' ') }} FCFA</span>
                                        <span class="block text-xs text-emerald-600">marge : {{ number_format($p['margin'], 0, ',', ' ') }} FCFA</span>
                                    @elseif($p['unit_price'])
                                        <span class="block text-xs text-gray-500">{{ number_format($p['unit_price'], 0, ',', ' ') }} FCFA</span>
                                        <span class="block text-xs text-amber-500">pas de prix zone</span>
                                    @endif
                                </div>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @error('loadingRows') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tableau des produits à charger --}}
                    @if(count($loadingRows) > 0)
                    <div class="overflow-x-auto rounded-lg border border-indigo-100">
                        <table class="w-full text-sm">
                            <thead class="bg-indigo-50 text-xs text-indigo-700 uppercase tracking-wide">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Produit</th>
                                    <th class="px-4 py-2.5 text-right">P.U.</th>
                                    <th class="px-4 py-2.5 text-right">Marge/u</th>
                                    <th class="px-4 py-2.5 text-center w-28">Qté <span class="text-red-400">*</span></th>
                                    <th class="px-4 py-2.5 w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-indigo-50 bg-white">
                                @foreach($loadingRows as $i => $row)
                                <tr wire:key="loading-row-{{ $row['product_id'] ?? $i }}">
                                    <td class="px-4 py-2.5">
                                        <span class="font-medium text-gray-900 block">{{ $row['name'] }}</span>
                                        @if($row['sku'])
                                        <code class="text-xs text-gray-400">{{ $row['sku'] }}</code>
                                        @endif
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-gray-700 whitespace-nowrap">
                                        {{ number_format($row['unit_price'], 0, ',', ' ') }}
                                    </td>
                                    <td class="px-4 py-2.5 text-right text-emerald-700 whitespace-nowrap">
                                        {{ number_format($row['margin_per_unit'], 0, ',', ' ') }}
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="number"
                                               wire:model="loadingRows.{{ $i }}.qty"
                                               min="1" placeholder="1"
                                               class="block w-full rounded-lg border-0 py-1.5 px-2.5 text-sm text-center text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-indigo-200 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                                        @error("loadingRows.{$i}.qty") <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-2.5 text-center">
                                        <button wire:click="removeLoadingRow({{ $i }})" type="button"
                                                class="text-red-300 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-indigo-50 border-t-2 border-indigo-200 font-semibold text-sm">
                                <tr>
                                    <td class="px-4 py-2.5 text-xs text-indigo-600 uppercase tracking-wide">Total</td>
                                    <td class="px-4 py-2.5 text-right text-gray-700" x-text="fmt(totalAmount)"></td>
                                    <td class="px-4 py-2.5 text-right text-emerald-700" x-text="fmt(totalMargin)"></td>
                                    <td class="px-4 py-2.5 text-center text-indigo-800 font-bold">
                                        <span x-text="totalQty"></span> <span class="text-indigo-500 font-normal text-xs">cass.</span>
                                    </td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    <div class="flex justify-end pt-1">
                        <button wire:click="loadProducts" type="button"
                                wire:loading.attr="disabled" wire:target="loadProducts"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-60">
                            <svg wire:loading.remove wire:target="loadProducts" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                            <svg wire:loading wire:target="loadProducts" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 14.627 0 12 0v4a8 8 0 00-8 8h4z"></path>
                            </svg>
                            Valider départ
                            <span x-show="totalQty > 0" x-text="'(' + totalQty + ' cassiers)'" class="font-normal text-indigo-200 text-xs"></span>
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─── ÉTAPE 2 : RETOUR PAR PRODUIT ─── --}}
            @if($trip->canReturn())
            <div class="overflow-hidden rounded-xl border border-amber-200 bg-white"
                 x-data="{
                     items: @js($itemsData),
                     get totals() {
                         return this.items.reduce((acc, item) => {
                             const ret  = parseInt($wire.returnQties[String(item.id)]) || 0;
                             const sold = Math.max(0, item.qty_delivered - ret);
                             acc.ret    += ret;
                             acc.sold   += sold;
                             acc.amount += sold * item.unit_price;
                             acc.marge  += sold * item.margin_per_unit;
                             return acc;
                         }, { ret: 0, sold: 0, amount: 0, marge: 0 });
                     },
                     fmt(n) { return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }
                 }">
                <div class="bg-amber-500 px-5 py-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold shrink-0">2</span>
                    <h2 class="text-sm font-semibold text-white">Retour du chauffeur</h2>
                </div>
                <div class="bg-amber-50 border-b border-amber-100 px-5 py-3 flex items-start gap-2">
                    <svg class="h-4 w-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    <p class="text-xs text-amber-800">
                        Indiquez combien de cassiers sont revenus <strong>invendus</strong> pour chaque produit.
                        Les quantités vendues, montants et marges sont calculés automatiquement.
                    </p>
                </div>
                <div class="p-5 space-y-4">
                    @if($trip->items->count() > 0)
                    <div class="overflow-x-auto rounded-lg border border-amber-100">
                        <table class="w-full text-sm">
                            <thead class="bg-amber-50 text-xs text-amber-700 uppercase tracking-wide">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Produit</th>
                                    <th class="px-4 py-2.5 text-right">P.U.</th>
                                    <th class="px-4 py-2.5 text-right">Chargé</th>
                                    <th class="px-4 py-2.5 text-center w-28">Retourné</th>
                                    <th class="px-4 py-2.5 text-right">Vendu</th>
                                    <th class="px-4 py-2.5 text-right">Montant</th>
                                    <th class="px-4 py-2.5 text-right">Marge</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50 bg-white">
                                @foreach($trip->items as $item)
                                <tr wire:key="return-item-{{ $item->id }}"
                                    x-data="{
                                        loaded:    {{ $item->qty_delivered }},
                                        unitPrice: {{ $item->unit_price }},
                                        margin:    {{ $item->margin_per_unit }},
                                        get returned() { return parseInt($wire.returnQties['{{ $item->id }}']) || 0; },
                                        get sold()     { return Math.max(0, this.loaded - this.returned); },
                                        get amount()   { return this.sold * this.unitPrice; },
                                        get marge()    { return this.sold * this.margin; },
                                        fmt(n) { return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }
                                    }">
                                    <td class="px-4 py-3">
                                        <span class="font-medium text-gray-900 block">{{ $item->product_designation }}</span>
                                        @if($item->product_ref)
                                        <code class="text-xs text-gray-400">{{ $item->product_ref }}</code>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-600 whitespace-nowrap">
                                        {{ number_format($item->unit_price, 0, ',', ' ') }}
                                    </td>
                                    <td class="px-4 py-3 text-right text-gray-700 font-medium">{{ $item->qty_delivered }}</td>
                                    <td class="px-4 py-3">
                                        <input type="number"
                                               wire:model="returnQties.{{ $item->id }}"
                                               min="0" max="{{ $item->qty_delivered }}" placeholder="0"
                                               class="block w-full rounded-lg border-0 py-1.5 px-2.5 text-sm text-center text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-amber-200 focus:ring-2 focus:ring-inset focus:ring-amber-500">
                                        @error("returnQties.{$item->id}") <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-indigo-700" x-text="sold"></td>
                                    <td class="px-4 py-3 text-right text-gray-900 whitespace-nowrap" x-text="fmt(amount)"></td>
                                    <td class="px-4 py-3 text-right text-emerald-700 whitespace-nowrap" x-text="fmt(marge)"></td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-amber-50 border-t-2 border-amber-200 font-semibold text-sm">
                                <tr>
                                    <td colspan="2" class="px-4 py-2.5 text-xs text-amber-700 uppercase tracking-wide">Total</td>
                                    <td class="px-4 py-2.5 text-right text-gray-700">{{ $trip->items->sum('qty_delivered') }}</td>
                                    <td class="px-4 py-2.5 text-center text-amber-800" x-text="totals.ret"></td>
                                    <td class="px-4 py-2.5 text-right font-bold text-indigo-800" x-text="totals.sold"></td>
                                    <td class="px-4 py-2.5 text-right text-gray-900 whitespace-nowrap" x-text="fmt(totals.amount)"></td>
                                    <td class="px-4 py-2.5 text-right text-emerald-700 whitespace-nowrap" x-text="fmt(totals.marge)"></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @else
                    <p class="py-6 text-center text-sm text-gray-400">Aucun produit chargé trouvé.</p>
                    @endif

                    <div class="flex justify-end pt-1">
                        <button wire:click="recordReturnFromProducts" type="button"
                                wire:loading.attr="disabled" wire:target="recordReturnFromProducts"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition-colors disabled:opacity-60">
                            <svg wire:loading.remove wire:target="recordReturnFromProducts" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg wire:loading wire:target="recordReturnFromProducts" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 14.627 0 12 0v4a8 8 0 00-8 8h4z"></path>
                            </svg>
                            Valider le retour
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─── RÉSUMÉ PRODUITS (completed / closed) ─── --}}
            @if(!$trip->canLoad() && !$trip->canReturn() && $trip->items->count() > 0)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Détail produits <span class="text-gray-400 font-normal">({{ $trip->items->count() }})</span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">Produit</th>
                                <th class="px-4 py-3 text-right">P.U.</th>
                                <th class="px-4 py-3 text-right">Chargé</th>
                                <th class="px-4 py-3 text-right">Retourné</th>
                                <th class="px-4 py-3 text-right">Vendu</th>
                                <th class="px-4 py-3 text-right">Montant</th>
                                <th class="px-4 py-3 text-right">Marge</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($trip->items as $item)
                            <tr class="hover:bg-gray-50" wire:key="summary-item-{{ $item->id }}">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $item->product_designation }}
                                    @if($item->product_ref)
                                    <span class="text-xs text-gray-400 ml-1">{{ $item->product_ref }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $item->qty_delivered }}</td>
                                <td class="px-4 py-3 text-right text-gray-400">{{ $item->qty_returned ?: '—' }}</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700">{{ $item->net_qty }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format($item->total, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-700">{{ number_format($item->line_total_margin, 0, ',', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm font-semibold border-t-2 border-gray-200">
                            <tr>
                                <td colspan="4" class="px-4 py-3 text-right text-gray-600 uppercase text-xs tracking-wide">Total</td>
                                <td class="px-4 py-3 text-right text-indigo-800">{{ $trip->items->sum('net_qty') }}</td>
                                <td class="px-4 py-3 text-right text-gray-900">{{ number_format($trip->items->sum('total'), 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-emerald-700">{{ number_format($trip->items->sum('line_total_margin'), 0, ',', ' ') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            {{-- Dépenses de tournée --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Dépenses <span class="text-gray-400 font-normal">({{ $trip->expenses->count() }})</span>
                    </h2>
                    @if($trip->status->value !== 'closed')
                    <button wire:click="$toggle('showExpenseForm')"
                            class="text-xs font-medium text-red-600 hover:text-red-800 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Ajouter
                    </button>
                    @endif
                </div>

                @if($showExpenseForm)
                <div class="px-5 py-4 bg-red-50 border-b border-red-100 space-y-3">
                    @if($expenseCategories->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($expenseCategories as $cat)
                        <button type="button" wire:click="selectExpenseCategory({{ $cat->id }})"
                                class="px-3 py-1 text-xs rounded-full border transition-colors
                                    {{ $expense_category_id === $cat->id
                                        ? 'bg-red-600 text-white border-red-600'
                                        : 'bg-white text-gray-700 border-gray-300 hover:border-red-400' }}">
                            {{ $cat->name }}
                        </button>
                        @endforeach
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-3">
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                            <input type="text" wire:model="expense_label" placeholder="Ex: Carburant KW 516 AA"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-red-500">
                            @error('expense_label') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="expense_amount" min="1"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-red-500">
                            @error('expense_amount') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="addExpense"
                                class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">
                            Ajouter
                        </button>
                        <button wire:click="$toggle('showExpenseForm')"
                                class="px-4 py-2 bg-white text-gray-600 text-xs rounded-lg border border-gray-300 hover:bg-gray-50">
                            Annuler
                        </button>
                    </div>
                </div>
                @endif

                @if($trip->expenses->count() > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($trip->expenses as $expense)
                    <li class="flex items-center justify-between px-5 py-3" wire:key="exp-{{ $expense->id }}">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $expense->label }}</span>
                            @if($expense->category)
                            <span class="ml-2 text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $expense->category->name }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-red-700">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</span>
                            @if($trip->status->value !== 'closed')
                            <button wire:click="removeExpense({{ $expense->id }})" wire:confirm="Supprimer ?"
                                    class="text-red-300 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @endif
                        </div>
                    </li>
                    @endforeach
                </ul>
                <div class="px-5 py-3 bg-gray-50 border-t border-gray-200 flex justify-between">
                    <span class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Total dépenses</span>
                    <span class="text-sm font-bold text-red-700">{{ number_format($trip->expenses->sum('amount'), 0, ',', ' ') }} FCFA</span>
                </div>
                @else
                <p class="px-5 py-6 text-center text-sm text-gray-400">Aucune dépense.</p>
                @endif
            </div>

        </div>{{-- /col principale --}}

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Résumé financier --}}
            @php
                $isClosed      = $trip->status->value === 'closed';
                $sousTotal     = $isClosed ? ($trip->total_revenue ?? 0)      : $trip->items->sum('total');
                $liveDépenses  = $isClosed ? ($trip->total_expenses ?? 0)     : $trip->expenses->sum('amount');
                $totalXAF      = max(0, $sousTotal - $liveDépenses);
                $liveMargin    = $isClosed ? ($trip->total_margin ?? 0)       : $trip->items->sum('line_total_margin');
                $bankPct       = $trip->bank_percentage ?? 80;
                $bankAmt       = $isClosed ? ($trip->bank_amount  ?? 0)       : (int) round($liveMargin * $bankPct / 100);
                $cashAmt       = $isClosed ? ($trip->cash_amount  ?? 0)       : $liveMargin - $bankAmt;
                $fondsAmt      = $isClosed ? ($trip->funds_amount ?? 0)       : max(0, $totalXAF - $liveMargin);
            @endphp
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200 flex items-center justify-between">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Résumé financier</h3>
                    @if(!$isClosed)
                    <span class="text-xs text-gray-400 italic">Projection en cours</span>
                    @endif
                </div>
                <div class="divide-y divide-gray-100">
                    {{-- Recette brute --}}
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-600">Recette brute (Sous-total)</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($sousTotal, 0, ',', ' ') }} FCFA</span>
                    </div>
                    {{-- Dépenses --}}
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-600">− Dépenses</span>
                        <span class="text-sm font-semibold text-red-600">{{ number_format($liveDépenses, 0, ',', ' ') }} FCFA</span>
                    </div>
                    {{-- Total XAF --}}
                    <div class="px-4 py-3 flex justify-between items-center bg-gray-50">
                        <div>
                            <span class="text-xs font-semibold text-gray-800">= TOTAL XAF</span>
                            <p class="text-xs text-gray-400">Montant remis par le chauffeur</p>
                        </div>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($totalXAF, 0, ',', ' ') }} FCFA</span>
                    </div>
                    {{-- Marges --}}
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-600">− Marges bénéficiaires</span>
                        <span class="text-sm font-semibold text-emerald-700">{{ number_format($liveMargin, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="px-4 py-2.5 flex justify-between items-center bg-blue-50/70">
                        <span class="text-xs text-blue-700">↳ Banque ({{ $bankPct }}%)</span>
                        <span class="text-xs font-bold text-blue-800">{{ number_format($bankAmt, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="px-4 py-2.5 flex justify-between items-center bg-violet-50/70">
                        <span class="text-xs text-violet-700">↳ Caisse ({{ 100 - $bankPct }}%)</span>
                        <span class="text-xs font-bold text-violet-800">{{ number_format($cashAmt, 0, ',', ' ') }} FCFA</span>
                    </div>
                    {{-- Fonds fournisseur --}}
                    <div class="px-4 py-3 flex justify-between items-center bg-orange-50">
                        <div>
                            <span class="text-xs font-semibold text-orange-800">= FONDS fournisseur</span>
                            <p class="text-xs text-orange-500">Reversement SOBRAGA</p>
                        </div>
                        <span class="text-sm font-bold text-orange-800">{{ number_format($fondsAmt, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @if($trip->mission_allowance_amount)
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-600">Prime de mission</span>
                        <span class="text-sm font-semibold text-indigo-700">{{ number_format($trip->mission_allowance_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Cassiers --}}
            @if($trip->loaded_crates)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Cassiers</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Chargés</span>
                        <span class="text-sm font-semibold">{{ $trip->loaded_crates }}</span>
                    </div>
                    @if($trip->returned_crates !== null)
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Retournés</span>
                        <span class="text-sm font-semibold text-gray-600">{{ $trip->returned_crates }}</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between bg-indigo-50">
                        <span class="text-xs text-indigo-700 font-medium">Vendus</span>
                        <span class="text-sm font-bold text-indigo-800">{{ $trip->sold_crates }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ─── ÉTAPE 3 : CLÔTURE ─── --}}
            @if($trip->canClose())
            <div class="overflow-hidden rounded-xl border border-green-200 bg-white">
                <div class="bg-green-600 px-5 py-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold shrink-0">3</span>
                    <h2 class="text-sm font-semibold text-white">Clôturer la tournée</h2>
                </div>
                <div class="bg-green-50 border-b border-green-100 px-5 py-3 space-y-1.5">
                    <div class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-green-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                        <div class="text-xs text-green-800">
                            <p class="font-semibold mb-0.5">Vérifiez le résumé financier avant de clôturer.</p>
                            <p>La clôture est <strong>irréversible</strong>. Banque, Caisse et Fonds seront calculés et figés définitivement à partir de la recette et des marges saisies.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <button wire:click="close"
                            wire:confirm="Clôturer définitivement cette tournée ? Cette action est irréversible."
                            wire:loading.attr="disabled" wire:target="close"
                            class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors disabled:opacity-60">
                        <svg wire:loading.remove wire:target="close" class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        <svg wire:loading wire:target="close" class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 14.627 0 12 0v4a8 8 0 00-8 8h4z"></path>
                        </svg>
                        Clôturer définitivement
                    </button>
                </div>
            </div>
            @endif

            {{-- PDFs --}}
            @if($trip->status->value === 'closed')
            <div class="bg-white border border-gray-200 rounded-xl p-4 space-y-2">
                <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide mb-3">Documents</h3>
                <a href="{{ route('deliveries.pdf.report', $trip) }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    Rapport de tournée (PDF)
                </a>
                @if($trip->items->count() > 0)
                <a href="{{ route('deliveries.pdf.invoice', $trip) }}" target="_blank"
                   class="flex items-center justify-center gap-2 w-full px-4 py-2.5 bg-gray-100 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-200 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                    </svg>
                    Facture client (PDF)
                </a>
                @endif
            </div>
            @endif

            @if($trip->closedBy)
            <p class="text-xs text-gray-400 text-center">
                Clôturé par {{ $trip->closedBy->name }}<br>
                le {{ $trip->closed_at->format('d/m/Y à H:i') }}
            </p>
            @endif

        </div>{{-- /sidebar --}}
    </div>
</div>
