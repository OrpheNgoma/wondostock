@section('title', 'Dashboard Financier')

@php
    /** @param float|null $pct */
    function evoClass(?float $pct, bool $invertColors = false): string {
        if ($pct === null) return 'text-gray-400';
        if ($pct > 0) return $invertColors ? 'text-red-600' : 'text-green-600';
        if ($pct < 0) return $invertColors ? 'text-green-600' : 'text-red-600';
        return 'text-gray-400';
    }
    function evoIcon(?float $pct): string {
        if ($pct === null || $pct == 0) return '→';
        return $pct > 0 ? '↑' : '↓';
    }
    function evoLabel(?float $pct): string {
        if ($pct === null) return 'Nouveau';
        return ($pct >= 0 ? '+' : '') . $pct . '%';
    }
@endphp

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-600 to-teal-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-600/20 to-teal-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Dashboard Financier</h1>
                    <p class="text-emerald-100 text-lg">Vue consolidée recettes, charges et résultat net</p>
                </div>
                <div class="flex items-center gap-3">
                    <select wire:model.live="selectedMonth"
                        class="rounded-xl bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white shadow-lg ring-1 ring-white/20 focus:outline-none focus:bg-white/20 transition-all duration-200">
                        @foreach ($months as $num => $name)
                            <option value="{{ $num }}" class="text-gray-900 bg-white">{{ $name }}</option>
                        @endforeach
                    </select>
                    <select wire:model.live="selectedYear"
                        class="rounded-xl bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-medium text-white shadow-lg ring-1 ring-white/20 focus:outline-none focus:bg-white/20 transition-all duration-200">
                        @foreach ($years as $year)
                            <option value="{{ $year }}" class="text-gray-900 bg-white">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- KPI Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-3 lg:grid-cols-6">
        @php
            $cards = [
                ['label' => 'Recettes Ventes',     'value' => $summary['documents_revenue'],  'color' => 'green',  'evo' => null, 'invertEvo' => false],
                ['label' => 'Recettes Livraisons', 'value' => $summary['deliveries_revenue'],  'color' => 'indigo', 'evo' => null, 'invertEvo' => false],
                ['label' => 'Total Recettes',      'value' => $summary['total_revenue'],       'color' => 'emerald','evo' => $summary['evolution']['revenue'],         'invertEvo' => false],
                ['label' => 'Dépenses Générales',  'value' => $summary['expenses_general'],    'color' => 'red',    'evo' => $summary['evolution']['expenses_general'], 'invertEvo' => true],
                ['label' => 'Dépenses Tournées',   'value' => $summary['expenses_delivery'],   'color' => 'orange', 'evo' => null, 'invertEvo' => true],
                ['label' => 'Masse Salariale',     'value' => $summary['salaries_net'],        'color' => 'purple', 'evo' => $summary['evolution']['salaries_net'],    'invertEvo' => true],
            ];
        @endphp
        @foreach ($cards as $card)
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-4">
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-2 w-2 rounded-full bg-{{ $card['color'] }}-500 shrink-0"></div>
                    <p class="text-xs font-medium text-gray-500 uppercase tracking-wide leading-tight">{{ $card['label'] }}</p>
                </div>
                <p class="text-lg font-bold text-{{ $card['color'] }}-700 tabular-nums">
                    {{ number_format($card['value'], 0, ',', ' ') }} <span class="text-xs font-normal">FCFA</span>
                </p>
                @if ($card['evo'] !== null)
                    <p class="mt-1 text-xs font-semibold {{ evoClass($card['evo'], $card['invertEvo']) }}">
                        {{ evoIcon($card['evo']) }} {{ evoLabel($card['evo']) }} vs mois préc.
                    </p>
                @elseif ($card['evo'] === null && isset($summary['evolution']['revenue']))
                    <p class="mt-1 text-xs text-gray-300">—</p>
                @endif
            </div>
        @endforeach
    </div>

    {{-- Résultat Net + Marge --}}
    @php $isPositive = $summary['net_result'] >= 0; @endphp
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        {{-- Résultat net --}}
        <div class="overflow-hidden rounded-2xl shadow-sm border p-6 {{ $isPositive ? 'bg-green-50 border-green-200' : 'bg-red-50 border-red-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold {{ $isPositive ? 'text-green-700' : 'text-red-700' }} uppercase tracking-wide">
                        Résultat Net du mois
                    </p>
                    <p class="text-xs {{ $isPositive ? 'text-green-600' : 'text-red-600' }} mt-1">
                        Recettes − Dépenses − Tournées − Salaires
                    </p>
                    @if ($summary['evolution']['net_result'] !== null)
                        <p class="mt-2 text-xs font-semibold {{ evoClass($summary['evolution']['net_result']) }}">
                            {{ evoIcon($summary['evolution']['net_result']) }} {{ evoLabel($summary['evolution']['net_result']) }} vs mois précédent
                        </p>
                    @endif
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold {{ $isPositive ? 'text-green-700' : 'text-red-700' }} tabular-nums">
                        {{ $isPositive ? '+' : '' }}{{ number_format($summary['net_result'], 0, ',', ' ') }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">FCFA</p>
                </div>
            </div>
        </div>

        {{-- Taux de marge --}}
        <div class="overflow-hidden rounded-2xl shadow-sm border p-6 {{ $isPositive ? 'bg-teal-50 border-teal-200' : 'bg-gray-50 border-gray-200' }}">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-semibold text-teal-700 uppercase tracking-wide">Taux de marge nette</p>
                    <p class="text-xs text-teal-600 mt-1">Résultat net / Total recettes</p>
                    <p class="text-xs text-gray-500 mt-2">
                        Total charges : {{ number_format($summary['total_charges'], 0, ',', ' ') }} FCFA
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-3xl font-bold {{ $summary['margin_rate'] >= 0 ? 'text-teal-700' : 'text-red-700' }} tabular-nums">
                        {{ $summary['total_revenue'] > 0 ? ($summary['margin_rate'] >= 0 ? '+' : '') . $summary['margin_rate'] . '%' : '—' }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- Tableaux --}}
    <div class="grid grid-cols-1 gap-6 lg:grid-cols-2">
        {{-- Dépenses par catégorie --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Dépenses par catégorie</h3>
            </div>
            @if ($summary['expenses_by_category']->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Aucune dépense ce mois-ci.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Catégorie</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Montant</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">%</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @php $totalExp = max(1, $summary['expenses_by_category']->sum('month_total')); @endphp
                            @foreach ($summary['expenses_by_category'] as $cat)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm text-gray-900">
                                        <div class="flex items-center gap-2">
                                            @if ($cat->color)
                                                <div class="h-2.5 w-2.5 rounded-full shrink-0" style="background-color: {{ $cat->color }}"></div>
                                            @endif
                                            {{ $cat->name }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900 text-right tabular-nums">
                                        {{ number_format($cat->month_total, 0, ',', ' ') }} FCFA
                                    </td>
                                    <td class="px-6 py-3 text-sm text-gray-500 text-right">
                                        {{ round($cat->month_total / $totalExp * 100, 1) }}%
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>

        {{-- Top 5 chauffeurs --}}
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
            <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                <h3 class="text-lg font-semibold text-gray-900">Top 5 chauffeurs</h3>
            </div>
            @if ($summary['top_drivers']->isEmpty())
                <div class="px-6 py-8 text-center">
                    <p class="text-sm text-gray-500">Aucune tournée clôturée ce mois-ci.</p>
                </div>
            @else
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-100">
                        <thead>
                            <tr class="bg-gray-50">
                                <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Chauffeur</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Tournées</th>
                                <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Recette</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 bg-white">
                            @foreach ($summary['top_drivers'] as $driver)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 text-sm font-medium text-gray-900">{{ $driver->name }}</td>
                                    <td class="px-6 py-3 text-sm text-gray-600 text-right">{{ $driver->trips_month }}</td>
                                    <td class="px-6 py-3 text-sm font-semibold text-indigo-700 text-right tabular-nums">
                                        {{ number_format($driver->revenue_month ?? 0, 0, ',', ' ') }} FCFA
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>

    {{-- Tendance 6 mois (inclut salaires dans le résultat) --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Tendance — 6 derniers mois</h3>
            <p class="text-xs text-gray-500 mt-0.5">Résultat = Recettes − Dépenses − Tournées − Salaires</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead>
                    <tr class="bg-gray-50">
                        <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Mois</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Recettes</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Charges totales</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Résultat net</th>
                        <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Marge</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @foreach ($summary['monthly_trend'] as $trend)
                        @php
                            $isLastMonth = $loop->last;
                            $marginPct = $trend['revenue'] > 0
                                ? round($trend['net_result'] / $trend['revenue'] * 100, 1)
                                : 0;
                        @endphp
                        <tr class="hover:bg-gray-50 {{ $isLastMonth ? 'bg-emerald-50/40' : '' }}">
                            <td class="px-6 py-3 text-sm font-medium text-gray-900">
                                {{ $trend['month_label'] }}
                                @if ($isLastMonth)
                                    <span class="ml-1 text-xs text-emerald-600 font-normal">(actuel)</span>
                                @endif
                            </td>
                            <td class="px-6 py-3 text-sm font-semibold text-green-700 text-right tabular-nums">
                                {{ number_format($trend['revenue'], 0, ',', ' ') }}
                            </td>
                            <td class="px-6 py-3 text-sm font-semibold text-red-600 text-right tabular-nums">
                                {{ number_format($trend['charges'], 0, ',', ' ') }}
                            </td>
                            <td class="px-6 py-3 text-sm font-bold text-right tabular-nums {{ $trend['net_result'] >= 0 ? 'text-green-700' : 'text-red-700' }}">
                                {{ $trend['net_result'] >= 0 ? '+' : '' }}{{ number_format($trend['net_result'], 0, ',', ' ') }}
                            </td>
                            <td class="px-6 py-3 text-sm text-right {{ $marginPct >= 0 ? 'text-teal-700' : 'text-red-600' }}">
                                {{ $trend['revenue'] > 0 ? ($marginPct >= 0 ? '+' : '') . $marginPct . '%' : '—' }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
