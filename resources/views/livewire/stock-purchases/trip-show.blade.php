<div class="max-w-6xl mx-auto px-4 py-6 space-y-6">

    {{-- En-tête --}}
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            <a href="{{ route('stock-purchases.index') }}" wire:navigate
               class="text-gray-400 hover:text-gray-600 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-xl font-bold text-gray-900">Achat de stock — {{ $trip->driver->name }}</h1>
                <p class="text-sm text-gray-500">
                    {{ $trip->trip_date->format('d/m/Y') }}
                    @if($trip->store) · Dépôt : {{ $trip->store->name }} @endif
                    @if($trip->supplier) · Fournisseur : {{ $trip->supplier->name }} @endif
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
            @if($trip->status->value === 'draft')
            @can('edit_stock_purchases')
            <a href="{{ route('stock-purchases.edit', $trip) }}" wire:navigate
               class="text-xs text-indigo-600 hover:text-indigo-800 font-medium">Modifier</a>
            @endcan
            @endif
        </div>
    </div>

    {{-- Progress bar workflow --}}
    @php
        $allSteps = [
            ['val' => 'draft',       'num' => '1', 'lbl' => 'Brouillon',       'desc' => 'Indiquez le nombre de casiers vides emportés, puis validez le départ.'],
            ['val' => 'in_progress', 'num' => '2', 'lbl' => 'En route',        'desc' => 'À son retour, saisissez les produits achetés, leur coût et les casiers pleins.'],
            ['val' => 'completed',   'num' => '3', 'lbl' => 'Revenu (pleins)', 'desc' => 'Vérifiez les quantités, puis clôturez pour ajouter le stock à l\'inventaire.'],
            ['val' => 'closed',      'num' => '4', 'lbl' => 'Clôturé',          'desc' => 'Voyage finalisé. Le stock a été ajouté au dépôt de destination.'],
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

            {{-- ─── ÉTAPE 1 : DÉPART (casiers vides) ─── --}}
            @if($trip->canLoad())
            <div class="rounded-xl border border-indigo-200 bg-white">
                <div class="bg-indigo-600 rounded-t-xl px-5 py-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold shrink-0">1</span>
                    <h2 class="text-sm font-semibold text-white">Départ — casiers vides</h2>
                </div>
                <div class="p-5 space-y-4">
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre de casiers vides emportés <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="empty_crates_out" min="1" placeholder="Ex: 40"
                               class="block w-full sm:w-48 rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-indigo-200 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @error('empty_crates_out') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex justify-end">
                        <button wire:click="startTrip" type="button"
                                wire:loading.attr="disabled" wire:target="startTrip"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors disabled:opacity-60">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14M12 5l7 7-7 7"/>
                            </svg>
                            Valider le départ
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- ─── ÉTAPE 2 : RETOUR (produits achetés) ─── --}}
            @if($trip->canReturn())
            <div class="overflow-hidden rounded-xl border border-amber-200 bg-white"
                 x-data="{
                     get totalQty()    { return $wire.purchaseRows.reduce((s, r) => s + (parseInt(r.qty)||0), 0); },
                     get totalCost()   { return $wire.purchaseRows.reduce((s, r) => s + (parseInt(r.qty)||0) * (parseInt(r.unit_cost)||0), 0); },
                     fmt(n) { return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }
                 }">
                <div class="bg-amber-500 px-5 py-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold shrink-0">2</span>
                    <h2 class="text-sm font-semibold text-white">Retour — produits achetés</h2>
                </div>
                <div class="bg-amber-50 border-b border-amber-100 px-5 py-3 flex items-start gap-2">
                    <svg class="h-4 w-4 text-amber-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z"/>
                    </svg>
                    <p class="text-xs text-amber-800">
                        Ajoutez chaque produit ramené (casiers pleins) avec sa quantité et son coût d'achat unitaire.
                        Le coût est pré-rempli depuis le prix d'achat du produit.
                    </p>
                </div>
                <div class="p-5 space-y-4">
                    {{-- Recherche produit --}}
                    <div class="relative">
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Ajouter un produit acheté</label>
                        <input type="text" wire:model.live.debounce.300ms="purchase_search"
                               placeholder="Tapez le nom ou la référence du produit"
                               autocomplete="off"
                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-amber-200 focus:ring-2 focus:ring-inset focus:ring-amber-500">
                        @if(count($purchase_list) > 0)
                        <ul class="absolute z-20 w-full mt-1 bg-white border border-amber-200 rounded-xl shadow-xl max-h-56 overflow-y-auto divide-y divide-gray-50">
                            @foreach($purchase_list as $p)
                            <li wire:click="addToPurchase({{ $p['id'] }})"
                                class="px-4 py-2.5 text-sm hover:bg-amber-50 cursor-pointer flex items-center justify-between gap-3">
                                <div class="min-w-0">
                                    <span class="font-semibold text-gray-900 block truncate">{{ $p['name'] }}</span>
                                    @if($p['sku'])<code class="text-xs text-gray-400">{{ $p['sku'] }}</code>@endif
                                </div>
                                <span class="block text-xs text-gray-500 shrink-0">{{ number_format($p['purchase_price'], 0, ',', ' ') }} FCFA</span>
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @error('purchaseRows') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Tableau des produits achetés --}}
                    @if(count($purchaseRows) > 0)
                    <div class="overflow-x-auto rounded-lg border border-amber-100">
                        <table class="w-full text-sm">
                            <thead class="bg-amber-50 text-xs text-amber-700 uppercase tracking-wide">
                                <tr>
                                    <th class="px-4 py-2.5 text-left">Produit</th>
                                    <th class="px-4 py-2.5 text-center w-32">Coût u. <span class="text-red-400">*</span></th>
                                    <th class="px-4 py-2.5 text-center w-24">Qté <span class="text-red-400">*</span></th>
                                    <th class="px-4 py-2.5 text-right">Total</th>
                                    <th class="px-4 py-2.5 w-8"></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-amber-50 bg-white">
                                @foreach($purchaseRows as $i => $row)
                                <tr wire:key="purchase-row-{{ $row['product_id'] ?? $i }}"
                                    x-data="{
                                        get lineTotal() { return (parseInt($wire.purchaseRows[{{ $i }}].qty)||0) * (parseInt($wire.purchaseRows[{{ $i }}].unit_cost)||0); },
                                        fmt(n) { return new Intl.NumberFormat('fr-FR').format(n) + ' FCFA'; }
                                    }">
                                    <td class="px-4 py-2.5">
                                        <span class="font-medium text-gray-900 block">{{ $row['name'] }}</span>
                                        @if($row['sku'])<code class="text-xs text-gray-400">{{ $row['sku'] }}</code>@endif
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="number" wire:model="purchaseRows.{{ $i }}.unit_cost" min="0"
                                               class="block w-full rounded-lg border-0 py-1.5 px-2.5 text-sm text-center text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-amber-200 focus:ring-2 focus:ring-inset focus:ring-amber-500">
                                        @error("purchaseRows.{$i}.unit_cost") <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-2.5">
                                        <input type="number" wire:model="purchaseRows.{{ $i }}.qty" min="1" placeholder="1"
                                               class="block w-full rounded-lg border-0 py-1.5 px-2.5 text-sm text-center text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-amber-200 focus:ring-2 focus:ring-inset focus:ring-amber-500">
                                        @error("purchaseRows.{$i}.qty") <p class="mt-0.5 text-xs text-red-600">{{ $message }}</p> @enderror
                                    </td>
                                    <td class="px-4 py-2.5 text-right font-medium text-gray-900 whitespace-nowrap" x-text="fmt(lineTotal)"></td>
                                    <td class="px-4 py-2.5 text-center">
                                        <button wire:click="removePurchaseRow({{ $i }})" type="button"
                                                class="text-red-300 hover:text-red-600 transition-colors">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                            </svg>
                                        </button>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot class="bg-amber-50 border-t-2 border-amber-200 font-semibold text-sm">
                                <tr>
                                    <td class="px-4 py-2.5 text-xs text-amber-600 uppercase tracking-wide">Total</td>
                                    <td></td>
                                    <td class="px-4 py-2.5 text-center text-amber-800 font-bold"><span x-text="totalQty"></span></td>
                                    <td class="px-4 py-2.5 text-right text-gray-900" x-text="fmt(totalCost)"></td>
                                    <td></td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                    @endif

                    {{-- Casiers pleins --}}
                    <div>
                        <label class="block text-xs font-semibold text-gray-700 mb-1.5">Nombre de casiers pleins ramenés <span class="text-red-400">*</span></label>
                        <input type="number" wire:model="full_crates_in" min="0" placeholder="Ex: 40"
                               class="block w-full sm:w-48 rounded-lg border-0 py-2.5 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-amber-200 focus:ring-2 focus:ring-inset focus:ring-amber-500">
                        @error('full_crates_in') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="flex justify-end pt-1">
                        <button wire:click="recordReturn" type="button"
                                wire:loading.attr="disabled" wire:target="recordReturn"
                                class="inline-flex items-center gap-2 px-5 py-2.5 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition-colors disabled:opacity-60">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
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
                        Produits achetés <span class="text-gray-400 font-normal">({{ $trip->items->count() }})</span>
                    </h2>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">Produit</th>
                                <th class="px-4 py-3 text-right">Coût u.</th>
                                <th class="px-4 py-3 text-right">Quantité</th>
                                <th class="px-4 py-3 text-right">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($trip->items as $item)
                            <tr class="hover:bg-gray-50" wire:key="sp-summary-{{ $item->id }}">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $item->product_designation }}
                                    @if($item->product_ref)<span class="text-xs text-gray-400 ml-1">{{ $item->product_ref }}</span>@endif
                                </td>
                                <td class="px-4 py-3 text-right text-gray-600">{{ number_format($item->unit_cost, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700">{{ $item->qty_purchased }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format($item->line_total, 0, ',', ' ') }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm font-semibold border-t-2 border-gray-200">
                            <tr>
                                <td colspan="2" class="px-4 py-3 text-right text-gray-600 uppercase text-xs tracking-wide">Total</td>
                                <td class="px-4 py-3 text-right text-indigo-800">{{ $trip->items->sum('qty_purchased') }}</td>
                                <td class="px-4 py-3 text-right text-gray-900">{{ number_format($trip->items->sum('line_total'), 0, ',', ' ') }}</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>
            @endif

            {{-- Dépenses de voyage --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Dépenses <span class="text-gray-400 font-normal">({{ $trip->expenses->count() }})</span>
                    </h2>
                    @if($trip->status->value !== 'closed')
                    @can('edit_stock_purchases')
                    <button wire:click="$toggle('showExpenseForm')"
                            class="text-xs font-medium text-red-600 hover:text-red-800 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Ajouter
                    </button>
                    @endcan
                    @endif
                </div>

                @if($showExpenseForm)
                <div class="px-5 py-4 bg-red-50 border-b border-red-100 space-y-3">
                    @if($expenseCategories->count() > 0)
                    <div class="flex flex-wrap gap-2">
                        @foreach($expenseCategories as $cat)
                        <button type="button" wire:click="selectExpenseCategory({{ $cat->id }})"
                                class="px-3 py-1 text-xs rounded-full border transition-colors
                                    {{ $expense_category_id === $cat->id ? 'bg-red-600 text-white border-red-600' : 'bg-white text-gray-700 border-gray-300 hover:border-red-400' }}">
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
                        <button wire:click="addExpense" class="px-4 py-2 bg-red-600 text-white text-xs font-semibold rounded-lg hover:bg-red-700">Ajouter</button>
                        <button wire:click="$toggle('showExpenseForm')" class="px-4 py-2 bg-white text-gray-600 text-xs rounded-lg border border-gray-300 hover:bg-gray-50">Annuler</button>
                    </div>
                </div>
                @endif

                @if($trip->expenses->count() > 0)
                <ul class="divide-y divide-gray-100">
                    @foreach($trip->expenses as $expense)
                    <li class="flex items-center justify-between px-5 py-3" wire:key="sp-exp-{{ $expense->id }}">
                        <div>
                            <span class="text-sm font-medium text-gray-900">{{ $expense->label }}</span>
                            @if($expense->category)<span class="ml-2 text-xs text-gray-400 bg-gray-100 px-2 py-0.5 rounded-full">{{ $expense->category->name }}</span>@endif
                        </div>
                        <div class="flex items-center gap-3">
                            <span class="text-sm font-bold text-red-700">{{ number_format($expense->amount, 0, ',', ' ') }} FCFA</span>
                            @if($trip->status->value !== 'closed')
                            @can('edit_stock_purchases')
                            <button wire:click="removeExpense({{ $expense->id }})" wire:confirm="Supprimer ?" class="text-red-300 hover:text-red-600">
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                            @endcan
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
                $coutAchat = $trip->total_purchase_cost ?? $trip->items->sum('line_total');
                $depenses  = $trip->total_expenses ?? $trip->expenses->sum('amount');
                $prime     = $trip->mission_allowance_amount ?? 0;
            @endphp
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Résumé du voyage</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-600">Coût d'achat des produits</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($coutAchat, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center">
                        <span class="text-xs text-gray-600">Dépenses du voyage</span>
                        <span class="text-sm font-semibold text-red-600">{{ number_format($depenses, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center bg-indigo-50/70">
                        <span class="text-xs text-indigo-700">Prime de déplacement</span>
                        <span class="text-sm font-bold text-indigo-800">{{ number_format($prime, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between items-center bg-gray-50">
                        <span class="text-xs font-semibold text-gray-800">= Coût total du voyage</span>
                        <span class="text-sm font-bold text-gray-900">{{ number_format($coutAchat + $depenses + $prime, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            {{-- Casiers --}}
            @if($trip->empty_crates_out !== null)
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Casiers</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Vides (aller)</span>
                        <span class="text-sm font-semibold">{{ $trip->empty_crates_out }}</span>
                    </div>
                    @if($trip->full_crates_in !== null)
                    <div class="px-4 py-3 flex justify-between bg-indigo-50">
                        <span class="text-xs text-indigo-700 font-medium">Pleins (retour)</span>
                        <span class="text-sm font-bold text-indigo-800">{{ $trip->full_crates_in }}</span>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- ─── ÉTAPE 3 : CLÔTURE ─── --}}
            @if($trip->canClose())
            @can('close_stock_purchases')
            <div class="overflow-hidden rounded-xl border border-green-200 bg-white">
                <div class="bg-green-600 px-5 py-3 flex items-center gap-2">
                    <span class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 text-white text-xs font-bold shrink-0">3</span>
                    <h2 class="text-sm font-semibold text-white">Clôturer + entrer en stock</h2>
                </div>
                <div class="bg-green-50 border-b border-green-100 px-5 py-3">
                    <div class="flex items-start gap-2">
                        <svg class="h-4 w-4 text-green-500 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                        </svg>
                        <div class="text-xs text-green-800">
                            <p class="font-semibold mb-0.5">Vérifiez les quantités avant de confirmer.</p>
                            <p>La clôture est <strong>irréversible</strong> : les produits achetés seront ajoutés à l'inventaire du dépôt <strong>{{ $trip->store?->name }}</strong>.</p>
                        </div>
                    </div>
                </div>
                <div class="p-5">
                    <button wire:click="close"
                            wire:confirm="Clôturer ce voyage et ajouter le stock à l'inventaire ? Cette action est irréversible."
                            wire:loading.attr="disabled" wire:target="close"
                            class="inline-flex items-center justify-center gap-2 w-full px-4 py-3 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors disabled:opacity-60">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-6.75a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v6.75a2.25 2.25 0 002.25 2.25z"/>
                        </svg>
                        Clôturer et entrer en stock
                    </button>
                </div>
            </div>
            @endcan
            @endif

            {{-- Confirmation de clôture --}}
            @if($trip->status->value === 'closed')
            <div class="bg-white border border-green-200 rounded-xl p-4">
                <div class="flex items-center gap-2 text-green-700">
                    <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span class="text-sm font-semibold">Stock ajouté à l'inventaire</span>
                </div>
                @if($trip->stock_applied_at)
                <p class="mt-2 text-xs text-gray-500">Entrée appliquée au dépôt {{ $trip->store?->name }} le {{ $trip->stock_applied_at->format('d/m/Y à H:i') }}.</p>
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
