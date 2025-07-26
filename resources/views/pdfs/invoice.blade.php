<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Facture {{ $invoice->invoice_number }}</title>
    <style>
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 12px;
            line-height: 1.4;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        
        .header {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .header-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 50%;
            text-align: right;
            vertical-align: top;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 5px;
        }
        
        .subtitle {
            color: #6b7280;
            margin-bottom: 15px;
        }
        
        .invoice-title {
            font-size: 20px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .invoice-details {
            color: #4b5563;
        }
        
        .client-info {
            display: table;
            width: 100%;
            margin-bottom: 30px;
        }
        
        .client-left {
            display: table-cell;
            width: 50%;
            vertical-align: top;
        }
        
        .client-right {
            display: table-cell;
            width: 50%;
            vertical-align: top;
            padding-left: 20px;
        }
        
        .section-title {
            font-size: 14px;
            font-weight: bold;
            color: #1f2937;
            margin-bottom: 10px;
        }
        
        .client-name {
            font-size: 16px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .invoice-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            border: 1px solid #e5e7eb;
        }
        
        .invoice-table th {
            background-color: #f9fafb;
            padding: 12px;
            text-align: left;
            font-weight: bold;
            color: #374151;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .invoice-table th.text-right {
            text-align: right;
        }
        
        .invoice-table td {
            padding: 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .invoice-table td.text-right {
            text-align: right;
        }
        
        .totals {
            width: 300px;
            margin-left: auto;
            margin-bottom: 30px;
        }
        
        .totals-table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .totals-table td {
            padding: 8px 12px;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .totals-table .total-row {
            font-weight: bold;
            font-size: 14px;
            border-top: 2px solid #1f2937;
            background-color: #f9fafb;
        }
        
        .status-box {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 30px;
            border-width: 1px;
            border-style: solid;
        }
        
        .status-paid {
            background-color: #ecfdf5;
            border-color: #10b981;
            color: #047857;
        }
        
        .status-pending {
            background-color: #fffbeb;
            border-color: #f59e0b;
            color: #92400e;
        }
        
        .status-overdue {
            background-color: #fef2f2;
            border-color: #ef4444;
            color: #dc2626;
        }
        
        .conditions {
            border-top: 1px solid #e5e7eb;
            padding-top: 20px;
        }
        
        .conditions ul {
            list-style-type: disc;
            padding-left: 20px;
            margin: 0;
        }
        
        .conditions li {
            margin-bottom: 5px;
        }
        
        .notes {
            margin-top: 15px;
            padding: 10px;
            background-color: #f9fafb;
            border-radius: 4px;
        }
        
        .contact-info {
            color: #6b7280;
            font-size: 11px;
        }
        
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <!-- En-tête -->
    <div class="header">
        <div class="header-left">
            <div class="logo">WondoStock</div>
            <div class="subtitle">Plateforme SaaS de gestion d'inventaire</div>
            <div class="contact-info">
                <div>www.wondostock.com</div>
                <div>contact@wondostock.com</div>
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">FACTURE</div>
            <div class="invoice-details">
                <div><strong>N°:</strong> {{ $invoice->invoice_number }}</div>
                <div><strong>Date d'émission:</strong> {{ $invoice->issue_date->format('d/m/Y') }}</div>
                <div><strong>Date d'échéance:</strong> {{ $invoice->due_date->format('d/m/Y') }}</div>
            </div>
        </div>
    </div>

    <!-- Informations client -->
    <div class="client-info">
        <div class="client-left">
            <div class="section-title">Facturé à:</div>
            <div class="client-name">{{ $invoice->company->name }}</div>
            @if($invoice->billing_address)
                @foreach($invoice->billing_address as $line)
                    <div>{{ $line }}</div>
                @endforeach
            @endif
        </div>
        <div class="client-right">
            <div class="section-title">Abonnement:</div>
            @if($invoice->subscription && $invoice->subscription->plan)
                <div style="font-weight: bold;">{{ $invoice->subscription->plan->name }}</div>
                <div style="font-size: 11px; color: #6b7280;">{{ $invoice->subscription->plan->description }}</div>
                <div style="margin-top: 8px; font-size: 11px;">
                    <strong>Période:</strong> 
                    {{ $invoice->subscription->starts_at->format('d/m/Y') }} - 
                    {{ $invoice->subscription->ends_at->format('d/m/Y') }}
                </div>
            @else
                <div style="color: #6b7280;">Aucun abonnement associé</div>
            @endif
        </div>
    </div>

    <!-- Détails de la facture -->
    <table class="invoice-table">
        <thead>
            <tr>
                <th style="width: 50%;">Description</th>
                <th class="text-right" style="width: 16%;">Montant HT</th>
                <th class="text-right" style="width: 16%;">TVA</th>
                <th class="text-right" style="width: 18%;">Montant TTC</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    @if($invoice->subscription && $invoice->subscription->plan)
                        <div style="font-weight: bold;">Abonnement {{ $invoice->subscription->plan->name }}</div>
                        <div style="font-size: 11px; color: #6b7280;">
                            Période: {{ $invoice->subscription->starts_at->format('d/m/Y') }} - 
                            {{ $invoice->subscription->ends_at->format('d/m/Y') }}
                        </div>
                    @else
                        <div style="font-weight: bold;">Service d'abonnement WondoStock</div>
                    @endif
                </td>
                <td class="text-right">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
                <td class="text-right">
                    @if($invoice->tax_amount > 0)
                        {{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA
                    @else
                        -
                    @endif
                </td>
                <td class="text-right" style="font-weight: bold;">
                    {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Totaux -->
    <div class="totals">
        <table class="totals-table">
            <tr>
                <td style="font-weight: bold;">Sous-total HT:</td>
                <td style="text-align: right;">{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</td>
            </tr>
            @if($invoice->tax_amount > 0)
                <tr>
                    <td style="font-weight: bold;">TVA:</td>
                    <td style="text-align: right;">{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            @endif
            <tr class="total-row">
                <td>Total TTC:</td>
                <td style="text-align: right;">{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</td>
            </tr>
        </table>
    </div>

    <!-- Statut de paiement -->
    <div class="status-box @if($invoice->isPaid()) status-paid @elseif($invoice->isOverdue()) status-overdue @else status-pending @endif">
        @if($invoice->isPaid())
            <strong>✓ Facture payée</strong>
            @if($invoice->paid_at)
                - Payée le {{ $invoice->paid_at->format('d/m/Y') }}
            @endif
        @elseif($invoice->isOverdue())
            <strong>⚠ Facture en retard</strong> ({{ $invoice->due_date->diffInDays(now()) }} jour(s) de retard)
        @else
            <strong>⏱ En attente de paiement</strong>
        @endif
    </div>

    <!-- Conditions de paiement -->
    <div class="conditions">
        <div class="section-title">Conditions de paiement</div>
        <ul>
            <li>Paiement par virement bancaire ou carte de crédit</li>
            <li>Aucun escompte accordé en cas de paiement anticipé</li>
            <li>En cas de retard de paiement, des intérêts de retard pourront être appliqués</li>
            <li>Tout retard de paiement supérieur à 30 jours peut entraîner la suspension du service</li>
        </ul>
        
        @if($invoice->notes)
            <div class="notes">
                <div style="font-weight: bold; margin-bottom: 5px;">Notes:</div>
                <div>{{ $invoice->notes }}</div>
            </div>
        @endif
    </div>

    <!-- Pied de page -->
    <div style="margin-top: 40px; text-align: center; font-size: 10px; color: #6b7280; border-top: 1px solid #e5e7eb; padding-top: 20px;">
        <div>Facture générée automatiquement par WondoStock le {{ now()->format('d/m/Y à H:i') }}</div>
        <div style="margin-top: 5px;">Pour toute question concernant cette facture, contactez-nous à contact@wondostock.com</div>
    </div>
</body>
</html>