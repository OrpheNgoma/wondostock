<!DOCTYPE html>
<html lang="fr">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>{{ $document->type->label() }} #{{ $document->document_number }}</title>
    <style>
        @page { margin: 0px; }
        body { margin: 0px; font-family: 'Helvetica', sans-serif; font-size: 12px; color: #333; }
        .page-container { padding: 40px; }
        .header { display: table; width: 100%; border-bottom: 1px solid #dee2e6; padding-bottom: 20px; }
        .header-left, .header-right { display: table-cell; vertical-align: middle; }
        .header-left { width: 60%; }
        .header-right { text-align: right; }
        .company-logo { max-width: 150px; max-height: 80px; }
        .company-name { font-size: 24px; font-weight: 600; margin: 0; }
        .company-address { font-style: normal; }
        .document-title { font-size: 28px; font-weight: bold; margin: 0; }
        .document-info { margin-top: 10px; }
        .customer-info { margin-top: 40px; margin-bottom: 40px; }
        .items-table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        .items-table th, .items-table td { padding: 10px; text-align: left; border-bottom: 1px solid #dee2e6; }
        .items-table thead th { background-color: #f8f9fa; font-weight: 600; }
        .items-table .text-right { text-align: right; }
        .totals { margin-top: 20px; float: right; width: 45%; }
        .totals table { width: 100%; }
        .totals td { padding: 8px; }
        .totals .grand-total { font-weight: bold; font-size: 14px; border-top: 2px solid #333; }
        .footer { position: fixed; bottom: 0; left: 0; right: 0; height: 80px; background-color: #f8f9fa; padding: 20px 40px; border-top: 1px solid #dee2e6; font-size: 10px; text-align: center; }
        .clear-fix { clear: both; }
        
        /* Styles pour les images personnalisées */
        .custom-header { text-align: center; margin-bottom: 20px; border-bottom: 1px solid #dee2e6; padding-bottom: 10px; }
        .header-image { max-width: 150%; height: auto; max-height: 150px; }
        .document-info-section { text-align: right; margin-bottom: 20px; }
        .document-info-section .document-title { font-size: 28px; font-weight: bold; margin: 0 0 10px 0; }
        .document-info-section .document-info { font-size: 14px; }
        
        .custom-footer { position: fixed; bottom: 0; left: 0; right: 0; text-align: center; }
        .footer-image { max-width: 200%; height: auto; max-height: 180px; }
        
        /* Mode impression sans en-tête/pied de page */
        .print-content-only .custom-header,
        .print-content-only .custom-footer,
        .print-content-only .header,
        .print-content-only .footer { display: none !important; }
        .print-content-only .page-container { padding: 20px; }
        .print-content-only .document-info-section { 
            background: transparent; 
            border: none; 
            padding: 10px 0;
        }
    </style>
</head>
<body class="{{ ($printMode ?? 'standard') === 'content_only' ? 'print-content-only' : '' }}">
    <div class="page-container">
        @if($useStoreData && $headerImageBase64)
            <!-- En-tête personnalisé pour les branches pays -->
            <div class="custom-header">
                <img src="{{ $headerImageBase64 }}" alt="En-tête personnalisé" class="header-image">
            </div>
            
            <!-- Informations du document uniquement -->
            <div class="document-info-section">
                <h2 class="document-title">{{ $document->type->label() }}</h2>
                <div class="document-info">
                    <strong>#{{ $document->document_number }}</strong><br>
                    Date : {{ $document->document_date->format('d/m/Y') }}
                </div>
            </div>
        @else
            <!-- En-tête classique pour les entreprises normales -->
            <div class="header">
                <div class="header-left">
                    @if($logoBase64)
                        <img src="{{ $logoBase64 }}" alt="Logo" class="company-logo">
                    @else
                        <h1 class="company-name">{{ $document->company->name }}</h1>
                    @endif
                    <address class="company-address">
                        {{ $document->company->address }}<br>
                        {{ $document->company->phone_number }}
                    </address>
                </div>
                <div class="header-right">
                    <h2 class="document-title">{{ $document->type->label() }}</h2>
                    <div class="document-info">
                        <strong>#{{ $document->document_number }}</strong><br>
                        Date : {{ $document->document_date->format('d/m/Y') }}
                    </div>
                </div>
            </div>
        @endif

        <div class="customer-info">
            <strong>Facturé à :</strong><br>
            <address class="not-italic">
                <strong>{{ $document->customer->name }}</strong><br>
                @if($document->customer->company_name) {{ $document->customer->company_name }}<br> @endif
                {{ $document->customer->address }}<br>
                {{ $document->customer->phone_number }}
            </address>
        </div>

        <table class="items-table">
            <thead>
                <tr>
                    <th>Produit / Service</th>
                    <th class="text-right">Qté</th>
                    <th class="text-right">Prix U. HT</th>
                    <th class="text-right">Total HT</th>
                </tr>
            </thead>
            <tbody>
                @foreach($document->items as $item)
                    <tr>
                        <td>{{ $item->product->name }}</td>
                        <td class="text-right">{{ $item->quantity }}</td>
                        <td class="text-right">{{ number_format($item->unit_price, 0, ',', ' ') }}</td>
                        <td class="text-right">{{ number_format($item->quantity * $item->unit_price, 0, ',', ' ') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Sous-total :</td>
                    <td class="text-right">{{ number_format($document->sub_total, 0, ',', ' ') }} FCFA</td>
                </tr>
                 <tr>
                    <td>Taxe :</td>
                    <td class="text-right">{{ number_format($document->tax_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
                 <tr class="grand-total">
                    <td>Total TTC :</td>
                    <td class="text-right">{{ number_format($document->total_amount, 0, ',', ' ') }} FCFA</td>
                </tr>
            </table>
        </div>

        <div class="clear-fix"></div>
    </div>


    @if($useStoreData && $footerImageBase64)
        <!-- Pied de page personnalisé pour les branches pays - PLEINE LARGEUR -->
        <div class="custom-footer">
            <img src="{{ $footerImageBase64 }}" alt="Pied de page personnalisé" class="footer-image">
        </div>
    @else
        <!-- Pied de page classique -->
        <div class="footer">
            {{ $document->company->name }} - NIF : {{ $document->company->nif ?? 'N/A' }} - RCCM : {{ $document->company->rccm ?? 'N/A' }}<br>
            Merci de votre confiance ! <strong>WondoStock conçu par: Pixel Parfait</strong>
        </div>
    @endif
</body>
</html>