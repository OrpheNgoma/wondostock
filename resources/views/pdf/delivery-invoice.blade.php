<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #1a1a1a; }

        .header { margin-bottom: 16px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .company-name { font-size: 12pt; font-weight: bold; }
        .company-sub { font-size: 8pt; color: #444; margin-top: 2px; }
        .doc-ref { font-size: 13pt; font-weight: bold; text-align: right; color: #1a1a1a; }

        .client-block { margin: 12px 0; text-align: right; }
        .client-label { font-weight: bold; font-size: 10pt; }
        .client-info { font-size: 9pt; color: #444; }

        table { width: 100%; border-collapse: collapse; margin: 12px 0; }
        th { background: #e8e8e8; font-weight: bold; font-size: 8pt; padding: 5px 6px;
             text-align: left; border: 1px solid #aaa; text-transform: uppercase; }
        td { font-size: 9pt; padding: 5px 6px; border: 1px solid #ccc; vertical-align: middle; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .font-bold { font-weight: bold; }
        .row-total { background: #f5f5f5; font-weight: bold; }

        .totals-row { display: flex; justify-content: flex-end; margin-top: 8px; }
        .totals-box { border: 2px solid #333; padding: 8px 16px; min-width: 200px; }
        .totals-box table td { border: none; padding: 3px 4px; font-size: 9.5pt; }
        .totals-box .grand-total td { font-size: 11pt; font-weight: bold; border-top: 2px solid #333; }

        .signatures { display: flex; justify-content: space-between; margin-top: 40px; }
        .sig-block { text-align: center; font-size: 9pt; }
        .sig-line { border-top: 1px solid #666; width: 100px; margin: 30px auto 4px; }

        .footer { margin-top: 16px; font-size: 7.5pt; color: #777; font-style: italic; }
        .date-line { text-align: right; margin-bottom: 8px; font-size: 8.5pt; }
    </style>
</head>
<body>

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
        <div>
            <div class="doc-ref">FACT {{ $trip->trip_date->format('d/m') }}/{{ $trip->driver->name }}/{{ $trip->trip_date->format('Y') }}</div>
            <div class="date-line">{{ $trip->trip_date->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Destinataire --}}
@php $firstCustomer = $trip->items->whereNotNull('customer_id')->first()?->customer; @endphp
@if($firstCustomer)
<div class="client-block">
    <div class="client-label">CLIENT : {{ strtoupper($firstCustomer->name) }}</div>
    @if($firstCustomer->phone_number)
    <div class="client-info">TEL : {{ $firstCustomer->phone_number }}</div>
    @endif
</div>
@else
<div class="client-block">
    <div class="client-label">CLIENT : ………………………………</div>
    <div class="client-info">TEL : ………………………</div>
</div>
@endif

{{-- Tableau des produits --}}
<table>
    <thead>
        <tr>
            <th style="width:40%;">Désignation</th>
            <th class="text-right" style="width:20%;">Prix unitaire</th>
            <th class="text-center" style="width:15%;">Quantité</th>
            <th class="text-right" style="width:25%;">Montant</th>
        </tr>
    </thead>
    <tbody>
        @foreach($trip->items as $item)
        @if($item->net_qty > 0)
        <tr>
            <td class="font-bold">{{ $item->product_designation }}</td>
            <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
            <td class="text-center font-bold">{{ $item->net_qty }}</td>
            <td class="text-right font-bold">{{ number_format($item->total, 0, ',', ' ') }}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr class="row-total">
            <td colspan="3" class="text-right font-bold" style="font-size:10pt;">TOTAL</td>
            <td class="text-right font-bold" style="font-size:10pt;">{{ number_format($trip->items->sum('total'), 0, ',', ' ') }}</td>
        </tr>
    </tfoot>
</table>

{{-- Zone de paiement --}}
<div class="totals-row">
    <div class="totals-box">
        <table>
            <tr>
                <td>Montant total :</td>
                <td class="text-right">{{ number_format($trip->total_revenue ?? $trip->items->sum('total'), 0, ',', ' ') }} XAF</td>
            </tr>
            <tr>
                <td>Versement :</td>
                <td class="text-right">{{ number_format($trip->total_revenue ?? $trip->items->sum('total'), 0, ',', ' ') }} XAF</td>
            </tr>
            <tr class="grand-total">
                <td>Solde dû :</td>
                <td class="text-right">0 XAF</td>
            </tr>
        </table>
    </div>
</div>

{{-- Signatures --}}
<div class="signatures">
    <div class="sig-block">
        <div class="sig-line"></div>
        <div>"le Client"</div>
        <div style="margin-top:4px;">Mme / Mr ………………………</div>
    </div>
    <div class="sig-block">
        <div class="sig-line"></div>
        <div>Le Livreur</div>
        <div style="margin-top:4px;">MR {{ strtoupper($trip->driver->name) }}</div>
    </div>
</div>

@if($trip->notes)
<p class="footer">Note : {{ $trip->notes }}</p>
@endif

</body>
</html>
