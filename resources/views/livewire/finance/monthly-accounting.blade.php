@section('title', 'Point de Comptabilité Mensuel')

<div class="space-y-8">
    {{-- En-tête gradient slate/gray foncé --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-gray-800 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-slate-700/20 to-gray-800/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Point de Comptabilité Mensuel</h1>
                    <p class="text-slate-300 text-lg">Rapport consolidé par boutique : inventaire, ventes, dépenses et synthèse</p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Formulaire de sélection --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Paramètres du rapport</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Boutique <span class="text-red-500">*</span></label>
                    <select
                        wire:model="store_id"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-slate-600"
                    >
                        <option value="">Sélectionner une boutique</option>
                        @foreach ($stores as $store)
                            <option value="{{ $store->id }}">{{ $store->name }}</option>
                        @endforeach
                    </select>
                    @error('store_id')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Du <span class="text-red-500">*</span></label>
                    <input
                        type="date"
                        wire:model="date_from"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-slate-600"
                    >
                    @error('date_from')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Au <span class="text-red-500">*</span></label>
                    <input
                        type="date"
                        wire:model="date_to"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-slate-600"
                    >
                    @error('date_to')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div class="flex items-end">
                    <button
                        type="button"
                        wire:click="generate"
                        wire:loading.attr="disabled"
                        class="w-full px-4 py-2 bg-slate-700 text-white text-sm font-semibold rounded-lg hover:bg-slate-800 transition-colors disabled:opacity-50"
                    >
                        <span wire:loading.remove wire:target="generate">Générer le rapport</span>
                        <span wire:loading wire:target="generate">Génération...</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    @if ($reportGenerated && $report !== null)
        @php
            $store = $report['store'];
            $period = $report['period'];
            $inventory = $report['inventory_snapshot'];
            $sales = $report['sales'];
            $pending = $report['pending_invoices'];
            $expenses = $report['expenses'];
        @endphp

        {{-- Bouton PDF --}}
        <div class="flex justify-end">
            <a
                href="{{ route('finance.monthly-accounting.pdf', ['store_id' => $store_id, 'date_from' => $date_from, 'date_to' => $date_to]) }}"
                target="_blank"
                class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors"
            >
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                </svg>
                Télécharger PDF
            </a>
        </div>

        {{-- Section 1 : Inventaire de stock --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-100 bg-blue-700 px-6 py-4">
                <h3 class="text-base font-bold text-white uppercase tracking-wide">1. Rapport des produits vendus — Inventaire de stock</h3>
                <p class="text-blue-200 text-sm">{{ $store->name }} | {{ $period['from']->format('d/m/Y') }} au {{ $period['to']->format('d/m/Y') }}</p>
            </div>
            @if ($inventory->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-500">Aucun produit actif dans cette boutique.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-blue-600">
                                <th class="px-4 py-3 text-left text-xs font-bold text-white uppercase tracking-wide">Produit</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase tracking-wide">Tarif</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase tracking-wide">Qté avant</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase tracking-wide">Qté ravitaillement</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase tracking-wide">Qté vendue</th>
                                <th class="px-4 py-3 text-right text-xs font-bold text-white uppercase tracking-wide">Reste en stock</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php
                                $groupedInventory = $inventory->groupBy(fn ($item) => $item->category_name);
                                $rowIndex = 0;
                            @endphp
                            @foreach ($groupedInventory as $categoryName => $items)
                                <tr>
                                    <td colspan="6" class="px-4 py-2 bg-blue-800 text-white text-xs font-bold uppercase tracking-wide">
                                        {{ $categoryName }}
                                    </td>
                                </tr>
                                @foreach ($items as $item)
                                    <tr wire:key="inv-{{ $loop->index }}" class="{{ $rowIndex % 2 === 0 ? 'bg-white' : 'bg-blue-50' }} hover:bg-blue-50 transition-colors">
                                        <td class="px-4 py-3 text-sm text-gray-900">
                                            <div class="font-medium">{{ $item->product_name }}</div>
                                            @if ($item->product_sku)
                                                <div class="text-xs text-gray-400">{{ $item->product_sku }}</div>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">
                                            {{ number_format($item->tarif, 0, ',', ' ') }}
                                            @if ($item->unit_label)
                                                <span class="text-xs text-gray-400">/{{ $item->unit_label }}</span>
                                            @endif
                                        </td>
                                        <td class="px-4 py-3 text-sm text-right text-gray-700">{{ $item->qte_avant }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-teal-700 font-medium">{{ $item->ravitaillement }}</td>
                                        <td class="px-4 py-3 text-sm text-right text-red-700 font-medium">{{ $item->vendu }}</td>
                                        <td class="px-4 py-3 text-sm text-right font-bold text-gray-900">{{ $item->reste }}</td>
                                    </tr>
                                    @php $rowIndex++; @endphp
                                @endforeach
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Section 2 : Entrées (Ventes) --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-100 bg-green-700 px-6 py-4">
                <h3 class="text-base font-bold text-white uppercase tracking-wide">2. Entrées Boutique (Ventes)</h3>
            </div>
            @if ($sales->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-500">Aucune vente sur cette période.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Facture</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produits</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant payé</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Mode paiement</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($sales as $sale)
                                <tr wire:key="sale-{{ $sale->id }}" class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $sale->document_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $sale->customer?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 font-mono">{{ $sale->document_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @foreach ($sale->items->take(2) as $item)
                                            <div>{{ $item->product?->name ?? $item->description }} ×{{ $item->quantity }}</div>
                                        @endforeach
                                        @if ($sale->items->count() > 2)
                                            <div class="text-xs text-gray-400">+{{ $sale->items->count() - 2 }} autre(s)</div>
                                        @endif
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-green-700 text-right whitespace-nowrap">
                                        {{ number_format($sale->paid_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @if ($sale->payments->isNotEmpty())
                                            @foreach ($sale->payments->take(2) as $payment)
                                                <div class="text-xs">{{ $payment->method ?? '—' }}</div>
                                            @endforeach
                                        @else
                                            —
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-green-50">
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-800">Total ventes</td>
                                <td class="px-4 py-3 text-sm font-bold text-green-800 text-right whitespace-nowrap">
                                    {{ number_format($report['sales_total'], 0, ',', ' ') }} FCFA
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Section 3 : Sorties (Dépenses) --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-100 bg-red-700 px-6 py-4">
                <h3 class="text-base font-bold text-white uppercase tracking-wide">3. Sorties Boutique (Dépenses)</h3>
            </div>
            @if ($expenses->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-500">Aucune dépense sur cette période.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Raison / Libellé</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Agent</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catégorie</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Reçu</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($expenses as $expense)
                                <tr wire:key="exp-{{ $expense->id }}" class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $expense->expense_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $expense->label }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $expense->agent ?: '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $expense->category?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm font-semibold text-red-700 text-right whitespace-nowrap">
                                        {{ number_format($expense->amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-4 py-3 text-sm text-gray-600">{{ $expense->receipt_number ?: '—' }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-red-50">
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-800">Total dépenses</td>
                                <td class="px-4 py-3 text-sm font-bold text-red-800 text-right whitespace-nowrap">
                                    {{ number_format($report['expenses_total'], 0, ',', ' ') }} FCFA
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Section 4 : Factures en attente --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-100 bg-amber-600 px-6 py-4">
                <h3 class="text-base font-bold text-white uppercase tracking-wide">4. Factures en attente (Créances)</h3>
            </div>
            @if ($pending->isEmpty())
                <div class="px-6 py-8 text-center text-sm text-gray-500">Aucune facture en attente.</div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Date</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Client</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">N° Facture</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Produits</th>
                                <th class="px-4 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Reste à payer</th>
                                <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100">
                            @foreach ($pending as $doc)
                                <tr wire:key="pend-{{ $doc->id }}" class="{{ $loop->even ? 'bg-gray-50' : 'bg-white' }}">
                                    <td class="px-4 py-3 text-sm text-gray-900 whitespace-nowrap">{{ $doc->document_date->format('d/m/Y') }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">{{ $doc->customer?->name ?? '—' }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700 font-mono">{{ $doc->document_number }}</td>
                                    <td class="px-4 py-3 text-sm text-gray-700">
                                        @foreach ($doc->items->take(2) as $item)
                                            <div>{{ $item->product?->name ?? $item->description }}</div>
                                        @endforeach
                                    </td>
                                    <td class="px-4 py-3 text-sm font-semibold text-amber-700 text-right whitespace-nowrap">
                                        {{ number_format($doc->total_amount - $doc->paid_amount, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-4 py-3 text-sm">
                                        <span class="inline-flex items-center rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800">
                                            {{ $doc->status->label() }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-amber-50">
                                <td colspan="4" class="px-4 py-3 text-sm font-bold text-gray-800">Total en attente</td>
                                <td class="px-4 py-3 text-sm font-bold text-amber-800 text-right whitespace-nowrap">
                                    {{ number_format($report['pending_total'], 0, ',', ' ') }} FCFA
                                </td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            @endif
        </div>

        {{-- Section 5 : Synthèse financière --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
            <div class="border-b border-gray-100 bg-slate-700 px-6 py-4">
                <h3 class="text-base font-bold text-white uppercase tracking-wide">5. Détail de Compte Mensuel — Synthèse Financière</h3>
            </div>
            <div class="p-6">
                <table class="min-w-full">
                    <tbody>
                        <tr class="bg-amber-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">Montant en attente (créances)</td>
                            <td class="px-6 py-4 text-sm font-bold text-amber-700 text-right">{{ number_format($report['pending_total'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="bg-white">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">Montant total dépensé</td>
                            <td class="px-6 py-4 text-sm font-bold text-red-700 text-right">{{ number_format($report['expenses_total'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="bg-orange-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">Montant versé au DG</td>
                            <td class="px-6 py-4 text-sm font-bold text-orange-700 text-right">{{ number_format($report['remittances_total'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="bg-teal-50">
                            <td class="px-6 py-4 text-sm font-semibold text-gray-700">Montant en caisse (solde)</td>
                            <td class="px-6 py-4 text-sm font-bold text-teal-700 text-right">{{ number_format($report['closing_balance'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                        <tr class="bg-blue-700">
                            <td class="px-6 py-4 text-sm font-bold text-white">Montant total vente</td>
                            <td class="px-6 py-4 text-base font-bold text-white text-right">{{ number_format($report['sales_total'], 0, ',', ' ') }} FCFA</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    @endif
</div>
