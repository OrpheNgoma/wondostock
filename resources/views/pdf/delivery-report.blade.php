<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; background: #fff; }

        .header { background: #c8dff0; padding: 8px 12px; margin-bottom: 8px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .company-name { font-size: 11pt; font-weight: bold; }
        .company-sub { font-size: 8pt; color: #444; }
        .doc-title { font-size: 12pt; font-weight: bold; text-align: center; border: 2px solid #333; padding: 4px 16px; }
        .doc-num { font-size: 8pt; text-align: right; margin-top: 2px; }

        .meta-row { display: flex; gap: 16px; margin-bottom: 6px; font-size: 8.5pt; }
        .meta-item { display: flex; gap: 4px; }
        .meta-label { font-weight: bold; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 8px; }
        th { background: #c8dff0; font-weight: bold; font-size: 8pt; padding: 4px 5px; text-align: center; border: 1px solid #999; }
        td { font-size: 8.5pt; padding: 3px 5px; border: 1px solid #ccc; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .bg-total { background: #f0f0f0; }
        .bg-highlight { background: #fffde7; }

        .section-grid { display: flex; gap: 8px; margin-bottom: 6px; }
        .section-box { flex: 1; border: 1px solid #999; padding: 6px; }
        .section-box-title { font-weight: bold; font-size: 8pt; text-decoration: underline; margin-bottom: 4px; }

        .finance-table { width: 100%; border-collapse: collapse; }
        .finance-table td { padding: 3px 6px; font-size: 8.5pt; border: 1px solid #ccc; }
        .finance-table .label { font-weight: bold; }
        .finance-table .amount { text-align: right; font-weight: bold; }
        .finance-row-bank { background: #dbeafe; }
        .finance-row-cash { background: #ede9fe; }
        .finance-row-funds { background: #fff7ed; }
        .finance-row-margin { background: #dcfce7; }

        .signatures { display: flex; justify-content: space-between; margin-top: 20px; }
        .sig-block { text-align: center; }
        .sig-line { border-top: 1px solid #666; width: 120px; margin: 40px auto 4px; }

        .footer-note { font-size: 7.5pt; color: #666; font-style: italic; margin-top: 4px; }
        .page-date { text-align: right; font-size: 7.5pt; color: #888; }
    </style>
</head>
<body>

{{-- En-tête --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="company-name">{{ $trip->company->name }}</div>
            @if($trip->company->address)
            <div class="company-sub">{{ $trip->company->address }}</div>
            @endif
            @if($trip->company->phone)
            <div class="company-sub">Tél : {{ $trip->company->phone }}</div>
            @endif
        </div>
        <div style="text-align:center;">
            <div class="doc-title">RAPPORT DE LIVRAISON-VENTES</div>
            <div class="doc-num">N° ……………………</div>
        </div>
        <div class="page-date">{{ $trip->trip_date->format('d/m/Y') }}</div>
    </div>
</div>

{{-- Infos tournée --}}
<div class="meta-row">
    <div class="meta-item"><span class="meta-label">Livreur :</span> {{ $trip->driver->name }}</div>
    @if($trip->vehicle) <div class="meta-item"><span class="meta-label">Véhicule :</span> {{ $trip->vehicle->display_name }}</div> @endif
    @if($trip->zone) <div class="meta-item"><span class="meta-label">Zone :</span> {{ $trip->zone->name }} — {{ $trip->zone->city }}</div> @endif
    <div class="meta-item"><span class="meta-label">Date :</span> {{ $trip->trip_date->format('d/m/Y') }}</div>
</div>

{{-- Tableau principal lignes --}}
@if($trip->items->count() > 0)
<table>
    <thead>
        <tr>
            <th rowspan="2">DÉSIGNATION</th>
            <th rowspan="2">P. UNITAIRE</th>
            <th colspan="2">CHARGEMENT</th>
            <th colspan="2">FACTURE DE VENTE</th>
            <th rowspan="2">DÉSIGNATION</th>
            <th rowspan="2">MARGE</th>
            <th rowspan="2">TOTAL MARGES</th>
        </tr>
        <tr>
            <th>CHARGE</th>
            <th>RETOUR</th>
            <th>QTE</th>
            <th>MONTANT</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trip->items as $item)
        <tr>
            <td class="font-bold">{{ $item->product_designation }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
            <td class="text-center">{{ $item->qty_delivered ?: '' }}</td>
            <td class="text-center">{{ $item->qty_returned ?: '' }}</td>
            <td class="text-center font-bold">{{ $item->net_qty > 0 ? $item->net_qty : '' }}</td>
            <td class="text-right">{{ $item->net_qty > 0 ? number_format($item->total, 0, ',', ' ') : '-' }}</td>
            <td class="font-bold">{{ $item->product_designation }}</td>
            <td class="text-right">{{ number_format($item->margin_per_unit, 0, ',', ' ') }}</td>
            <td class="text-right">{{ $item->net_qty > 0 ? number_format($item->line_total_margin, 0, ',', ' ') : '-' }}</td>
        </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr class="bg-total font-bold">
            <td></td>
            <td></td>
            <td class="text-center">{{ $trip->loaded_crates }}</td>
            <td class="text-center">{{ $trip->returned_crates }}</td>
            <td class="text-center font-bold">{{ $trip->sold_crates }}</td>
            <td class="text-right">{{ number_format($trip->items->sum('total'), 0, ',', ' ') }}</td>
            <td colspan="2" class="text-right">TOTAL MARGE BÉNÉFICIAIRE</td>
            <td class="text-right">{{ number_format($trip->total_margin ?? $trip->items->sum('line_total_margin'), 0, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>
@endif

<div class="section-grid">

    {{-- Dépenses --}}
    <div class="section-box" style="max-width:220px;">
        <div class="section-box-title">NOTE : DÉPENSES</div>
        @if($trip->expenses->count() > 0)
        @foreach($trip->expenses as $expense)
        <div style="display:flex; justify-content:space-between; padding:2px 0; font-size:8.5pt;">
            <span>{{ $expense->label }}</span>
            <span class="font-bold">{{ number_format($expense->amount, 0, ',', ' ') }}</span>
        </div>
        @endforeach
        @endif
        <div style="display:flex; justify-content:space-between; padding:3px 0; margin-top:4px; border-top:2px solid #333; font-size:9pt;">
            <span class="font-bold">TOTAL DÉPENSES</span>
            <span class="font-bold">{{ number_format($trip->total_expenses ?? 0, 0, ',', ' ') }}</span>
        </div>
    </div>

    {{-- Paiement reçu --}}
    <div class="section-box" style="max-width:180px;">
        <table class="finance-table">
            <tr><td class="label">Sous-total</td><td class="amount">{{ number_format($trip->items->sum('total'), 0, ',', ' ') }}</td></tr>
            <tr><td class="label">TOTAL XAF</td><td class="amount">{{ number_format($trip->total_revenue ?? 0, 0, ',', ' ') }}</td></tr>
            <tr><td class="label">Paiement</td><td class="amount">{{ number_format($trip->total_revenue ?? 0, 0, ',', ' ') }}</td></tr>
            <tr class="bg-highlight"><td class="label">Solde dû</td><td class="amount">-</td></tr>
        </table>
    </div>

    {{-- Synthèse financière --}}
    <div class="section-box">
        <table class="finance-table">
            <tr><td class="label">RECETTE :</td><td class="amount">{{ number_format($trip->total_revenue ?? 0, 0, ',', ' ') }}</td></tr>
            <tr class="finance-row-margin"><td class="label">MARGE</td><td class="amount">{{ number_format($trip->total_margin ?? 0, 0, ',', ' ') }}</td></tr>
            <tr class="finance-row-bank">
                <td class="label">BANQUE ({{ $trip->bank_percentage }}%)</td>
                <td class="amount">{{ number_format($trip->bank_amount ?? 0, 0, ',', ' ') }}</td>
            </tr>
            <tr class="finance-row-cash">
                <td class="label">CAISSE ({{ 100 - $trip->bank_percentage }}%)</td>
                <td class="amount">{{ number_format($trip->cash_amount ?? 0, 0, ',', ' ') }}</td>
            </tr>
            <tr class="finance-row-funds"><td class="label">FONDS</td><td class="amount">{{ number_format($trip->funds_amount ?? 0, 0, ',', ' ') }}</td></tr>
            @if($trip->mission_allowance_amount)
            <tr><td class="label">Prime de mission</td><td class="amount">{{ number_format($trip->mission_allowance_amount, 0, ',', ' ') }}</td></tr>
            @endif
        </table>
    </div>
</div>

{{-- Signatures --}}
<div class="signatures">
    <div class="sig-block">
        <div class="sig-line"></div>
        <div>Le Gérant</div>
    </div>
    <div class="sig-block">
        <div class="sig-line"></div>
        <div>Le Livreur ({{ $trip->driver->name }})</div>
    </div>
</div>

@if($trip->notes)
<p class="footer-note">Note : {{ $trip->notes }}</p>
@endif

</body>
</html>
