<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; background: #fff; }

        .header { background: #3B5998; padding: 10px 14px; margin-bottom: 10px; color: #fff; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .company-name { font-size: 12pt; font-weight: bold; color: #fff; }
        .company-sub { font-size: 8pt; color: #c8d5f0; }
        .doc-title { font-size: 13pt; font-weight: bold; text-align: center; border: 2px solid #fff; padding: 4px 16px; color: #fff; margin: 4px auto; display: inline-block; }
        .store-period { font-size: 9pt; color: #dce8ff; text-align: center; margin-top: 4px; }

        .section-title { font-size: 10pt; font-weight: bold; text-align: center; padding: 6px; text-transform: uppercase; letter-spacing: 0.5px; margin: 10px 0 0 0; }
        .section-title-blue { background: #3B5998; color: #fff; }
        .section-title-green { background: #1a7a3a; color: #fff; }
        .section-title-red { background: #b91c1c; color: #fff; }
        .section-title-amber { background: #b45309; color: #fff; }
        .section-title-slate { background: #334155; color: #fff; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; font-size: 8pt; }
        th { background: #3B5998; color: #fff; font-weight: bold; font-size: 7.5pt; padding: 4px 5px; text-align: center; border: 1px solid #2d4373; }
        td { font-size: 8pt; padding: 3px 5px; border: 1px solid #ccc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .row-even { background: #f0f4ff; }
        .row-odd { background: #fff; }
        .category-row { background: #3B5998; color: #fff; font-weight: bold; font-size: 8pt; }
        .tfoot-row { background: #e8f0fe; font-weight: bold; }
        .tfoot-row-green { background: #dcfce7; font-weight: bold; }
        .tfoot-row-red { background: #fee2e2; font-weight: bold; }
        .tfoot-row-amber { background: #fef3c7; font-weight: bold; }

        .summary-table td { padding: 5px 8px; border: 1px solid #e0e0e0; font-size: 9pt; }
        .summary-pending { background: #fef3c7; }
        .summary-expenses { background: #fee2e2; }
        .summary-remittances { background: #fff7ed; }
        .summary-balance { background: #ccfbf1; }
        .summary-total { background: #3B5998; color: #fff; font-weight: bold; font-size: 10pt; }

        .footer { margin-top: 16px; border-top: 1px solid #999; padding-top: 6px; font-size: 7.5pt; color: #555; text-align: center; }
        .page-break { page-break-before: always; }
        .no-data { text-align: center; color: #888; font-style: italic; padding: 8px; }
    </style>
</head>
<body>

@php
    $store = $report['store'];
    $company = $store->company;
    $period = $report['period'];
    $inventory = $report['inventory_snapshot'];
    $sales = $report['sales'];
    $pending = $report['pending_invoices'];
    $expenses = $report['expenses'];
@endphp

{{-- En-tête --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="company-name">{{ $company->name ?? '' }}</div>
            @if (!empty($company->address))
                <div class="company-sub">{{ $company->address }}</div>
            @endif
            @if (!empty($company->phone))
                <div class="company-sub">Tél : {{ $company->phone }}</div>
            @endif
        </div>
        <div style="text-align:center; flex:1; padding:0 12px;">
            <div class="doc-title">POINT DE COMPTABILITE MENSUEL</div>
            <div class="store-period">{{ $store->name }} — du {{ $period['from']->format('d/m/Y') }} au {{ $period['to']->format('d/m/Y') }}</div>
        </div>
        <div style="text-align:right;">
            <div class="company-sub">Édité le {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Section 1 : Inventaire --}}
<div class="section-title section-title-blue">RAPPORT DES PRODUITS VENDUS</div>

@if ($inventory->isEmpty())
    <div class="no-data">Aucun produit actif dans cette boutique.</div>
@else
    @php
        $groupedInventory = $inventory->groupBy(fn ($item) => $item->category_name);
        $invRowIndex = 0;
    @endphp
    <table>
        <thead>
            <tr>
                <th style="text-align:left; width:30%;">PRODUITS</th>
                <th>CAPACITÉS / TARIF</th>
                <th>QTÉ AVANT</th>
                <th>QTÉ RAVITAILLEMENT</th>
                <th>QTÉ VENDU</th>
                <th>QTÉ EN STOCK</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($groupedInventory as $categoryName => $items)
                <tr>
                    <td colspan="6" class="category-row">{{ strtoupper($categoryName) }}</td>
                </tr>
                @foreach ($items as $item)
                    <tr class="{{ $invRowIndex % 2 === 0 ? 'row-odd' : 'row-even' }}">
                        <td class="font-bold">
                            {{ $item->product_name }}
                            @if ($item->product_sku)
                                <br><span style="font-size:7pt;color:#666;">{{ $item->product_sku }}</span>
                            @endif
                        </td>
                        <td class="text-right">{{ number_format($item->tarif, 0, ',', ' ') }}</td>
                        <td class="text-center">{{ $item->qte_avant }}</td>
                        <td class="text-center">{{ $item->ravitaillement }}</td>
                        <td class="text-center font-bold">{{ $item->vendu }}</td>
                        <td class="text-center font-bold">{{ $item->reste }}</td>
                    </tr>
                    @php $invRowIndex++; @endphp
                @endforeach
            @endforeach
        </tbody>
    </table>
@endif

{{-- Section 2 : Entrées (Ventes) --}}
<div class="section-title section-title-green" style="margin-top:12px;">ENTRÉES BOUTIQUE — VENTES</div>

@if ($sales->isEmpty())
    <div class="no-data">Aucune vente sur cette période.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="background:#1a7a3a;">DATES</th>
                <th style="background:#1a7a3a;">CLIENTS</th>
                <th style="background:#1a7a3a;">PRODUITS</th>
                <th style="background:#1a7a3a;">QTÉ</th>
                <th style="background:#1a7a3a;">P.U</th>
                <th style="background:#1a7a3a;">MONTANTS PAYÉS</th>
                <th style="background:#1a7a3a;">MODE DE PAIEMENT</th>
            </tr>
        </thead>
        <tbody>
            @php $saleRowIndex = 0; @endphp
            @foreach ($sales as $sale)
                @php
                    $firstItem = $sale->items->first();
                    $paymentMethod = $sale->payments->first()?->method ?? '—';
                @endphp
                <tr class="{{ $saleRowIndex % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td class="text-center">{{ $sale->document_date->format('d/m/Y') }}</td>
                    <td>{{ $sale->customer?->name ?? '—' }}</td>
                    <td>
                        @foreach ($sale->items->take(3) as $item)
                            {{ $item->product?->name ?? $item->description }}@if (!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="text-center">
                        @foreach ($sale->items->take(3) as $item)
                            {{ $item->quantity }}@if (!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="text-right">
                        @if ($firstItem)
                            {{ number_format($firstItem->unit_price, 0, ',', ' ') }}
                        @endif
                    </td>
                    <td class="text-right font-bold">{{ number_format($sale->paid_amount, 0, ',', ' ') }}</td>
                    <td class="text-center">{{ $paymentMethod }}</td>
                </tr>
                @php $saleRowIndex++; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-row-green">
                <td colspan="5" class="font-bold">TOTAL VENTES</td>
                <td class="text-right font-bold">{{ number_format($report['sales_total'], 0, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- Section 3 : Sorties (Dépenses) --}}
<div class="section-title section-title-red" style="margin-top:12px;">SORTIES BOUTIQUE — DÉPENSES</div>

@if ($expenses->isEmpty())
    <div class="no-data">Aucune dépense sur cette période.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="background:#b91c1c;">DATES</th>
                <th style="background:#b91c1c;">RAISON DE SORTIE</th>
                <th style="background:#b91c1c;">AGENT</th>
                <th style="background:#b91c1c;">QTÉ</th>
                <th style="background:#b91c1c;">P.U</th>
                <th style="background:#b91c1c;">MONTANTS PAYÉS</th>
                <th style="background:#b91c1c;">N° REÇU</th>
            </tr>
        </thead>
        <tbody>
            @php $expRowIndex = 0; @endphp
            @foreach ($expenses as $expense)
                <tr class="{{ $expRowIndex % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td class="text-center">{{ $expense->expense_date->format('d/m/Y') }}</td>
                    <td>{{ $expense->label }}</td>
                    <td>{{ $expense->agent ?: '—' }}</td>
                    <td class="text-center">1</td>
                    <td class="text-right">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
                    <td class="text-right font-bold">{{ number_format($expense->amount, 0, ',', ' ') }}</td>
                    <td class="text-center">{{ $expense->receipt_number ?: '—' }}</td>
                </tr>
                @php $expRowIndex++; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-row-red">
                <td colspan="5" class="font-bold">TOTAL DÉPENSES</td>
                <td class="text-right font-bold">{{ number_format($report['expenses_total'], 0, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- Section 4 : Factures en attente --}}
<div class="section-title section-title-amber" style="margin-top:12px;">FACTURES EN ATTENTE — CRÉANCES</div>

@if ($pending->isEmpty())
    <div class="no-data">Aucune facture en attente.</div>
@else
    <table>
        <thead>
            <tr>
                <th style="background:#b45309;">DATES</th>
                <th style="background:#b45309;">CLIENTS</th>
                <th style="background:#b45309;">N° FACTURE</th>
                <th style="background:#b45309;">PRODUITS</th>
                <th style="background:#b45309;">MONTANT</th>
                <th style="background:#b45309;">STATUT</th>
            </tr>
        </thead>
        <tbody>
            @php $pendRowIndex = 0; @endphp
            @foreach ($pending as $doc)
                <tr class="{{ $pendRowIndex % 2 === 0 ? 'row-odd' : 'row-even' }}">
                    <td class="text-center">{{ $doc->document_date->format('d/m/Y') }}</td>
                    <td>{{ $doc->customer?->name ?? '—' }}</td>
                    <td class="text-center font-bold">{{ $doc->document_number }}</td>
                    <td>
                        @foreach ($doc->items->take(2) as $item)
                            {{ $item->product?->name ?? $item->description }}@if (!$loop->last), @endif
                        @endforeach
                    </td>
                    <td class="text-right font-bold">{{ number_format($doc->total_amount - $doc->paid_amount, 0, ',', ' ') }}</td>
                    <td class="text-center">{{ $doc->status->label() }}</td>
                </tr>
                @php $pendRowIndex++; @endphp
            @endforeach
        </tbody>
        <tfoot>
            <tr class="tfoot-row-amber">
                <td colspan="4" class="font-bold">TOTAL EN ATTENTE</td>
                <td class="text-right font-bold">{{ number_format($report['pending_total'], 0, ',', ' ') }}</td>
                <td></td>
            </tr>
        </tfoot>
    </table>
@endif

{{-- Section 5 : Synthèse DÉTAIL DE COMPTE --}}
<div class="section-title section-title-slate" style="margin-top:14px;">DÉTAIL DE COMPTE MENSUEL</div>

<table class="summary-table" style="margin-top:0;">
    <tbody>
        <tr class="summary-pending">
            <td class="font-bold" style="width:60%;">MONTANT EN ATTENTE (CRÉANCES)</td>
            <td class="text-right font-bold">{{ number_format($report['pending_total'], 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="summary-expenses">
            <td class="font-bold">MONTANT TOTAL DÉPENSÉ</td>
            <td class="text-right font-bold">{{ number_format($report['expenses_total'], 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="summary-remittances">
            <td class="font-bold">MONTANT VERSÉ AU DIRECTEUR GÉNÉRAL</td>
            <td class="text-right font-bold">{{ number_format($report['remittances_total'], 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="summary-balance">
            <td class="font-bold">MONTANT EN CAISSE (SOLDE)</td>
            <td class="text-right font-bold">{{ number_format($report['closing_balance'], 0, ',', ' ') }} FCFA</td>
        </tr>
        <tr class="summary-total">
            <td>MONTANT TOTAL VENTE</td>
            <td class="text-right">{{ number_format($report['sales_total'], 0, ',', ' ') }} FCFA</td>
        </tr>
    </tbody>
</table>

{{-- Pied de page --}}
<div class="footer">
    @if (!empty($company->name))
        <strong>{{ $company->name }}</strong>
        @if (!empty($company->phone)) — Tél : {{ $company->phone }} @endif
        @if (!empty($company->email)) — Email : {{ $company->email }} @endif
        @if (!empty($company->address)) — {{ $company->address }} @endif
        @if (!empty($company->nif)) — NIF : {{ $company->nif }} @endif
    @endif
    <br>Document généré le {{ now()->format('d/m/Y à H:i') }}
</div>

</body>
</html>
