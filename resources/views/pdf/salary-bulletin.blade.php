<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9.5pt; color: #1a1a1a; background: #fff; }

        .header { border-bottom: 2px solid #374151; padding-bottom: 12px; margin-bottom: 16px; }
        .header-top { display: flex; justify-content: space-between; align-items: flex-start; }
        .company-name { font-size: 13pt; font-weight: bold; color: #111827; }
        .company-sub { font-size: 8pt; color: #6b7280; margin-top: 2px; }
        .doc-title { font-size: 13pt; font-weight: bold; text-align: right; color: #374151; }
        .doc-period { font-size: 9pt; text-align: right; color: #6b7280; margin-top: 3px; }

        .section { margin-bottom: 14px; }
        .section-title { font-size: 8.5pt; font-weight: bold; text-transform: uppercase; letter-spacing: 0.5px; color: #6b7280; border-bottom: 1px solid #e5e7eb; padding-bottom: 4px; margin-bottom: 8px; }

        .employee-box { background: #f9fafb; border: 1px solid #e5e7eb; padding: 10px 14px; border-radius: 4px; }
        .employee-name { font-size: 12pt; font-weight: bold; color: #111827; }
        .employee-role { font-size: 8.5pt; color: #6b7280; margin-top: 2px; }

        table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        th { background: #f3f4f6; font-weight: bold; font-size: 8pt; padding: 5px 8px; text-align: left; border: 1px solid #d1d5db; }
        td { font-size: 9pt; padding: 5px 8px; border: 1px solid #e5e7eb; }
        .text-right { text-align: right; }
        .font-bold { font-weight: bold; }
        .bg-subtotal { background: #f3f4f6; }
        .bg-net { background: #1f2937; color: #fff; }
        .bg-deduction { background: #fef9f9; }
        .bg-bonus { background: #f0fdf4; }
        .row-gross { background: #eff6ff; }

        .meta-row { display: flex; gap: 20px; margin-bottom: 8px; font-size: 8.5pt; color: #374151; }
        .meta-item .label { font-weight: bold; }

        .signatures { display: flex; justify-content: space-between; margin-top: 40px; }
        .sig-block { text-align: center; width: 180px; }
        .sig-label { font-size: 8.5pt; font-weight: bold; color: #374151; margin-bottom: 4px; }
        .sig-line { border-top: 1px solid #9ca3af; margin-top: 50px; }
        .sig-name { font-size: 8pt; color: #6b7280; margin-top: 4px; }

        .footer { margin-top: 20px; border-top: 1px solid #e5e7eb; padding-top: 8px; font-size: 7.5pt; color: #9ca3af; text-align: center; }
        .total-net-amount { font-size: 12pt; font-weight: bold; }
    </style>
</head>
<body>

@php
    $period = $slip->period;
    $company = $period->company;
    $isDriver = $slip->driver_id !== null;
    $monthLabel = \Illuminate\Support\Carbon::createFromDate($period->year, $period->month, 1)->translatedFormat('F Y');
@endphp

{{-- En-tête --}}
<div class="header">
    <div class="header-top">
        <div>
            <div class="company-name">{{ $company->name }}</div>
            @if($company->address)
                <div class="company-sub">{{ $company->address }}</div>
            @endif
            @if($company->phone)
                <div class="company-sub">Tél : {{ $company->phone }}</div>
            @endif
        </div>
        <div>
            <div class="doc-title">BULLETIN DE PAIE</div>
            <div class="doc-period">Période : {{ $monthLabel }}</div>
            <div class="doc-period">Date d'émission : {{ now()->format('d/m/Y') }}</div>
        </div>
    </div>
</div>

{{-- Informations employé --}}
<div class="section">
    <div class="section-title">Employé</div>
    <div class="employee-box">
        <div class="employee-name">{{ $slip->employee_name }}</div>
        <div class="employee-role">{{ $isDriver ? 'Chauffeur' : 'Employé' }}</div>
    </div>
</div>

{{-- Tableau de calcul --}}
<div class="section">
    <div class="section-title">Détail de la rémunération</div>
    <table>
        <thead>
            <tr>
                <th style="width: 60%">Désignation</th>
                <th style="width: 25%" class="text-right">Détail</th>
                <th style="width: 15%" class="text-right">Montant (FCFA)</th>
            </tr>
        </thead>
        <tbody>
            {{-- Salaire de base --}}
            <tr>
                <td>Salaire de base</td>
                <td class="text-right"></td>
                <td class="text-right font-bold">{{ number_format($slip->base_salary, 0, ',', ' ') }}</td>
            </tr>

            @if ($isDriver)
            {{-- Commissions tournées --}}
            <tr>
                <td>
                    Commissions tournées
                    @if ($slip->trips_count > 0)
                        <span style="font-size: 8pt; color: #6b7280;">({{ $slip->trips_count }} tournée(s))</span>
                    @endif
                </td>
                <td class="text-right" style="font-size: 8pt; color: #6b7280;">0,15% recettes</td>
                <td class="text-right">{{ number_format($slip->total_commissions, 0, ',', ' ') }}</td>
            </tr>

            {{-- Primes de mission --}}
            @if ($slip->mission_allowances > 0)
            <tr>
                <td>Primes de mission</td>
                <td class="text-right"></td>
                <td class="text-right">{{ number_format($slip->mission_allowances, 0, ',', ' ') }}</td>
            </tr>
            @endif
            @endif

            {{-- Primes/Bonus --}}
            @foreach ($slip->deductions->where('type', 'bonus') as $bonus)
            <tr class="bg-bonus">
                <td>{{ $bonus->label }} <span style="font-size: 7.5pt; color: #059669;">(prime)</span></td>
                <td class="text-right"></td>
                <td class="text-right">+ {{ number_format($bonus->amount, 0, ',', ' ') }}</td>
            </tr>
            @endforeach

            {{-- Sous-total Brut --}}
            <tr class="row-gross bg-subtotal">
                <td class="font-bold">SALAIRE BRUT</td>
                <td class="text-right"></td>
                <td class="text-right font-bold">{{ number_format($slip->gross_salary, 0, ',', ' ') }}</td>
            </tr>

            {{-- Déductions --}}
            @foreach ($slip->deductions->where('type', 'deduction') as $deduction)
            <tr class="bg-deduction">
                <td>{{ $deduction->label }} <span style="font-size: 7.5pt; color: #dc2626;">(retenue)</span></td>
                <td class="text-right"></td>
                <td class="text-right">- {{ number_format($deduction->amount, 0, ',', ' ') }}</td>
            </tr>
            @endforeach

            {{-- Avances sur salaire --}}
            @if ($slip->total_advances > 0)
            <tr class="bg-deduction">
                <td>Avances sur salaire déduites</td>
                <td class="text-right"></td>
                <td class="text-right">- {{ number_format($slip->total_advances, 0, ',', ' ') }}</td>
            </tr>
            @endif

            {{-- NET À PAYER --}}
            <tr class="bg-net">
                <td class="font-bold total-net-amount" colspan="2" style="color: #fff;">NET À PAYER</td>
                <td class="text-right font-bold total-net-amount" style="color: #fff;">
                    {{ number_format($slip->net_salary, 0, ',', ' ') }} FCFA
                </td>
            </tr>
        </tbody>
    </table>
</div>

@if ($slip->notes)
<div class="section">
    <div class="section-title">Observations</div>
    <p style="font-size: 8.5pt; color: #374151;">{{ $slip->notes }}</p>
</div>
@endif

{{-- Signatures --}}
<div class="signatures">
    <div class="sig-block">
        <div class="sig-label">L'Employé</div>
        <div class="sig-line"></div>
        <div class="sig-name">{{ $slip->employee_name }}</div>
    </div>
    <div class="sig-block">
        <div class="sig-label">L'Employeur</div>
        <div class="sig-line"></div>
        <div class="sig-name">{{ $company->name }}</div>
    </div>
</div>

{{-- Pied de page --}}
<div class="footer">
    Document généré le {{ now()->format('d/m/Y à H:i') }} — {{ $company->name }}
</div>

</body>
</html>
