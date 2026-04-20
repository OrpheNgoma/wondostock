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
    <div class="bg-white border border-gray-200 rounded-xl p-4">
        <ol class="flex items-center w-full">
            @foreach([['draft','1','Brouillon'],['in_progress','2','En route'],['completed','3','Retourné'],['closed','4','Clôturé']] as [$val,$num,$lbl])
            @php
                $statuses = ['draft','in_progress','completed','closed'];
                $currentIdx = array_search($trip->status->value, $statuses);
                $stepIdx = array_search($val, $statuses);
                $isDone = $stepIdx < $currentIdx;
                $isCurrent = $stepIdx === $currentIdx;
            @endphp
            <li class="flex items-center {{ $loop->last ? '' : 'flex-1' }}">
                <span class="flex items-center justify-center w-8 h-8 rounded-full shrink-0 text-sm font-bold
                    {{ $isDone ? 'bg-indigo-600 text-white' : ($isCurrent ? 'bg-indigo-100 text-indigo-700 ring-2 ring-indigo-500' : 'bg-gray-100 text-gray-400') }}">
                    @if($isDone)
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
                    @else
                        {{ $num }}
                    @endif
                </span>
                <span class="ms-2 text-xs font-medium {{ $isCurrent ? 'text-indigo-700' : ($isDone ? 'text-gray-500' : 'text-gray-400') }}">{{ $lbl }}</span>
                @if(!$loop->last)
                    <div class="flex-1 mx-3 h-0.5 {{ $isDone ? 'bg-indigo-400' : 'bg-gray-200' }}"></div>
                @endif
            </li>
            @endforeach
        </ol>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Colonne principale --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Chargement --}}
            @if($trip->canLoad())
            <div class="bg-white border border-indigo-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Enregistrer le chargement</h2>
                <div class="flex gap-3">
                    <div class="flex-1">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cassiers chargés <span class="text-red-500">*</span></label>
                        <input type="number" wire:model="loaded_crates" min="1"
                               class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @error('loaded_crates') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div class="flex items-end gap-2">
                        <button wire:click="saveProgress" type="button"
                                class="px-4 py-2 bg-white text-indigo-600 text-sm font-medium rounded-lg ring-1 ring-inset ring-indigo-300 hover:bg-indigo-50 transition-colors">
                            Sauvegarder
                        </button>
                        <button wire:click="load" type="button"
                                class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors">
                            Valider départ
                        </button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Retour --}}
            @if($trip->canReturn())
            <div class="bg-white border border-amber-200 rounded-xl p-5">
                <h2 class="text-sm font-semibold text-gray-900 mb-4">Enregistrer le retour</h2>
                <div class="grid grid-cols-2 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">Cassiers retournés</label>
                        <input type="number" wire:model="returned_crates" min="0"
                               class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @error('returned_crates') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-700 mb-1">
                            Recette totale (XAF)
                            @if($trip->items->count() > 0)
                            <button type="button" wire:click="syncRevenue"
                                    class="ml-1 text-indigo-500 hover:text-indigo-700 text-xs underline">↻ Depuis lignes</button>
                            @endif
                        </label>
                        <input type="number" wire:model="total_revenue" min="0"
                               class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @error('total_revenue') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <button wire:click="saveProgress" type="button"
                            class="px-4 py-2 bg-white text-amber-700 text-sm font-medium rounded-lg ring-1 ring-inset ring-amber-300 hover:bg-amber-50 transition-colors">
                        Sauvegarder
                    </button>
                    <button wire:click="recordReturn" type="button"
                            class="px-4 py-2 bg-amber-500 text-white text-sm font-semibold rounded-lg hover:bg-amber-600 transition-colors">
                        Valider retour
                    </button>
                </div>
            </div>
            @endif

            {{-- Lignes de livraison --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100">
                    <h2 class="text-sm font-semibold text-gray-900">
                        Lignes de livraison <span class="text-gray-400 font-normal">({{ $trip->items->count() }})</span>
                    </h2>
                    @if($trip->status->value !== 'closed')
                    <button wire:click="$toggle('showItemForm')"
                            class="text-xs font-medium text-indigo-600 hover:text-indigo-800 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                        </svg>
                        Ajouter
                    </button>
                    @endif
                </div>

                @if($showItemForm)
                <div class="px-5 py-4 bg-indigo-50 border-b border-indigo-100 space-y-3">
                    {{-- Sélection produit --}}
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Produit / Désignation <span class="text-red-500">*</span></label>
                        <input type="text" wire:model.live="product_search"
                               placeholder="Rechercher un produit (REGAB, CASTEL…)"
                               class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @if(count($products_list) > 0)
                        <ul class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-48 overflow-y-auto">
                            @foreach($products_list as $p)
                            <li wire:click="selectProduct({{ $p['id'] }}, @js($p['name']), {{ $p['selling_price'] ?? 0 }})"
                                class="px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer flex justify-between">
                                <span>
                                    <span class="font-medium">{{ $p['name'] }}</span>
                                    @if($p['sku']) <span class="text-gray-400 text-xs ml-1">{{ $p['sku'] }}</span> @endif
                                </span>
                                @if($p['selling_price'])
                                <span class="text-indigo-600 text-xs">{{ number_format($p['selling_price'], 0, ',', ' ') }} XAF</span>
                                @endif
                            </li>
                            @endforeach
                        </ul>
                        @endif
                        @if(empty($item_product_id))
                        <input type="text" wire:model="item_product_designation"
                               placeholder="Ou saisir la désignation manuellement"
                               class="block w-full mt-1 rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @endif
                        @error('item_product_designation') <p class="mt-1 text-xs text-red-600">{{ $message }}</p> @enderror
                    </div>

                    {{-- Sélection client --}}
                    <div class="relative">
                        <label class="block text-xs font-medium text-gray-700 mb-1">Client (optionnel)</label>
                        <input type="text" wire:model.live="customer_search"
                               placeholder="Rechercher un client…"
                               class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        @if(count($customers_list) > 0)
                        <ul class="absolute z-10 w-full mt-1 bg-white border border-gray-200 rounded-lg shadow-lg max-h-40 overflow-y-auto">
                            @foreach($customers_list as $c)
                            <li wire:click="selectCustomer({{ $c['id'] }}, @js($c['name']))"
                                class="px-4 py-2 text-sm hover:bg-indigo-50 cursor-pointer">{{ $c['name'] }}</li>
                            @endforeach
                        </ul>
                        @endif
                    </div>

                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Qté chargée <span class="text-red-500">*</span></label>
                            <input type="number" wire:model="item_qty_delivered" min="0"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Qté retournée</label>
                            <input type="number" wire:model="item_qty_returned" min="0"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Prix unitaire (XAF)</label>
                            <input type="number" wire:model="item_unit_price" min="0"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Marge / unité (XAF)</label>
                            <input type="number" wire:model="item_margin_per_unit" min="0"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-xs font-medium text-gray-700 mb-1">Note (optionnel)</label>
                            <input type="text" wire:model="item_notes"
                                   class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500">
                        </div>
                    </div>
                    <div class="flex gap-2">
                        <button wire:click="addItem"
                                class="px-4 py-2 bg-indigo-600 text-white text-xs font-semibold rounded-lg hover:bg-indigo-700">
                            Ajouter
                        </button>
                        <button wire:click="$toggle('showItemForm')"
                                class="px-4 py-2 bg-white text-gray-600 text-xs rounded-lg border border-gray-300 hover:bg-gray-50">
                            Annuler
                        </button>
                    </div>
                </div>
                @endif

                @if($trip->items->count() > 0)
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead class="bg-gray-50 text-xs text-gray-500 uppercase tracking-wide">
                            <tr>
                                <th class="px-4 py-3 text-left">Produit</th>
                                <th class="px-4 py-3 text-left">Client</th>
                                <th class="px-4 py-3 text-right">Chargé</th>
                                <th class="px-4 py-3 text-right">Retour</th>
                                <th class="px-4 py-3 text-right">Vendu</th>
                                <th class="px-4 py-3 text-right">P.U.</th>
                                <th class="px-4 py-3 text-right">Marge/u</th>
                                <th class="px-4 py-3 text-right">Total</th>
                                <th class="px-4 py-3 text-right">Marge</th>
                                @if($trip->status->value !== 'closed') <th class="px-4 py-3"></th> @endif
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach($trip->items as $item)
                            <tr class="hover:bg-gray-50" wire:key="item-{{ $item->id }}">
                                <td class="px-4 py-3 font-medium text-gray-900">
                                    {{ $item->product_designation }}
                                    @if($item->product_ref)
                                    <span class="text-xs text-gray-400 ml-1">{{ $item->product_ref }}</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-gray-600 text-xs">{{ $item->customer?->name ?? '—' }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ $item->qty_delivered }}</td>
                                <td class="px-4 py-3 text-right text-gray-400">{{ $item->qty_returned ?: '—' }}</td>
                                <td class="px-4 py-3 text-right font-bold text-indigo-700">{{ $item->net_qty }}</td>
                                <td class="px-4 py-3 text-right text-gray-700">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-emerald-700">{{ number_format($item->margin_per_unit, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-gray-900">{{ number_format($item->total, 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right font-medium text-emerald-700">{{ number_format($item->line_total_margin, 0, ',', ' ') }}</td>
                                @if($trip->status->value !== 'closed')
                                <td class="px-4 py-3">
                                    <button wire:click="removeItem({{ $item->id }})" wire:confirm="Supprimer cette ligne ?"
                                            class="text-red-400 hover:text-red-600">
                                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </td>
                                @endif
                            </tr>
                            @endforeach
                        </tbody>
                        <tfoot class="bg-gray-50 text-sm font-semibold border-t-2 border-gray-200">
                            <tr>
                                <td colspan="{{ $trip->status->value !== 'closed' ? 7 : 6 }}" class="px-4 py-3 text-right text-gray-600 uppercase text-xs tracking-wide">Total</td>
                                <td class="px-4 py-3 text-right text-gray-900">{{ number_format($trip->items->sum('total'), 0, ',', ' ') }}</td>
                                <td class="px-4 py-3 text-right text-emerald-700">{{ number_format($trip->items->sum('line_total_margin'), 0, ',', ' ') }}</td>
                                @if($trip->status->value !== 'closed') <td></td> @endif
                            </tr>
                        </tfoot>
                    </table>
                </div>
                @else
                <p class="px-5 py-8 text-center text-sm text-gray-400">Aucune ligne saisie.</p>
                @endif
            </div>

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
                            <label class="block text-xs font-medium text-gray-700 mb-1">Montant (XAF) <span class="text-red-500">*</span></label>
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
                            <span class="text-sm font-bold text-red-700">{{ number_format($expense->amount, 0, ',', ' ') }} XAF</span>
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
                    <span class="text-sm font-bold text-red-700">{{ number_format($trip->expenses->sum('amount'), 0, ',', ' ') }} XAF</span>
                </div>
                @else
                <p class="px-5 py-6 text-center text-sm text-gray-400">Aucune dépense.</p>
                @endif
            </div>

        </div>{{-- /col principale --}}

        {{-- Sidebar --}}
        <div class="space-y-4">

            {{-- Résumé financier --}}
            <div class="bg-white border border-gray-200 rounded-xl overflow-hidden">
                <div class="px-4 py-3 bg-gray-50 border-b border-gray-200">
                    <h3 class="text-xs font-semibold text-gray-600 uppercase tracking-wide">Résumé financier</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @php
                        $liveRevenue = $trip->total_revenue ?? $trip->items->sum('total');
                        $liveMargin = $trip->status->value === 'closed' ? ($trip->total_margin ?? 0) : $trip->items->sum('line_total_margin');
                        $liveExpenses = $trip->status->value === 'closed' ? ($trip->total_expenses ?? 0) : $trip->expenses->sum('amount');
                    @endphp
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Recettes</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($liveRevenue, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Marge bénéficiaire</span>
                        <span class="text-sm font-semibold text-emerald-700">{{ number_format($liveMargin, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Dépenses</span>
                        <span class="text-sm font-semibold text-red-600">{{ number_format($liveExpenses, 0, ',', ' ') }} XAF</span>
                    </div>
                    @if($trip->status->value === 'closed')
                    <div class="px-4 py-3 flex justify-between bg-blue-50">
                        <span class="text-xs text-blue-700 font-medium">Banque ({{ $trip->bank_percentage }}%)</span>
                        <span class="text-sm font-bold text-blue-800">{{ number_format($trip->bank_amount ?? 0, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between bg-violet-50">
                        <span class="text-xs text-violet-700 font-medium">Caisse ({{ 100 - $trip->bank_percentage }}%)</span>
                        <span class="text-sm font-bold text-violet-800">{{ number_format($trip->cash_amount ?? 0, 0, ',', ' ') }} XAF</span>
                    </div>
                    <div class="px-4 py-3 flex justify-between bg-orange-50">
                        <span class="text-xs text-orange-700 font-medium">Fonds fournisseur</span>
                        <span class="text-sm font-bold text-orange-800">{{ number_format($trip->funds_amount ?? 0, 0, ',', ' ') }} XAF</span>
                    </div>
                    @if($trip->mission_allowance_amount)
                    <div class="px-4 py-3 flex justify-between">
                        <span class="text-xs text-gray-600">Prime de mission</span>
                        <span class="text-sm font-semibold text-indigo-700">{{ number_format($trip->mission_allowance_amount, 0, ',', ' ') }} XAF</span>
                    </div>
                    @endif
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

            {{-- Clôturer --}}
            @if($trip->canClose())
            <div class="bg-green-50 border border-green-200 rounded-xl p-4">
                <p class="text-xs text-green-700 mb-3">
                    Prêt à clôturer. Banque/Caisse/Fonds seront calculés automatiquement.
                </p>
                <button wire:click="close" wire:confirm="Clôturer définitivement cette tournée ?"
                        class="w-full px-4 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-colors">
                    Clôturer la tournée
                </button>
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
