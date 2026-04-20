@section('title', 'Salaires')

<div class="space-y-8">
    {{-- En-tête gradient violet/purple --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-violet-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-violet-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">Gestion des salaires</h1>
                    <p class="text-purple-100 text-lg">Périodes de paie, bulletins et avances</p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('salaries.advances.index') }}"
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 00-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 01-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 003 15h-.75M15 10.5a3 3 0 11-6 0 3 3 0 016 0zm3 0h.008v.008H18V10.5zm-12 0h.008v.008H6V10.5z" />
                        </svg>
                        Avances
                    </a>
                    <button
                        wire:click="openCurrentPeriod"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Ouvrir {{ \Illuminate\Support\Carbon::now()->translatedFormat('F Y') }}
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Périodes enregistrées</p>
            <p class="mt-2 text-3xl font-bold text-gray-900">{{ $stats['total_periods'] }}</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-purple-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-purple-50 border border-purple-100 shadow-sm p-5">
            <p class="text-xs font-medium text-purple-700 uppercase tracking-wide">Masse salariale {{ now()->year }}</p>
            <p class="mt-2 text-2xl font-bold text-purple-700">{{ number_format($stats['ytd_total'], 0, ',', ' ') }} <span class="text-sm font-medium">FCFA</span></p>
            <div class="mt-2 h-1 w-8 rounded-full bg-purple-300"></div>
        </div>
    </div>

    {{-- Liste des périodes --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Périodes de paie</h3>
        </div>

        @if ($periods->isEmpty())
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5" />
                </svg>
                <p class="mt-4 text-sm text-gray-500">Aucune période de paie. Cliquez sur "Ouvrir" pour commencer.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Période</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Statut</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Brut total</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Net total</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($periods as $period)
                            <tr wire:key="period-{{ $period->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-900">{{ $period->label }}</p>
                                    <p class="text-xs text-gray-500">{{ $period->slips_count ?? 0 }} bulletin(s)</p>
                                </td>
                                <td class="px-6 py-4">
                                    @php
                                        $colorMap = ['gray' => 'bg-gray-100 text-gray-700', 'blue' => 'bg-blue-100 text-blue-700', 'green' => 'bg-green-100 text-green-700'];
                                        $badgeClass = $colorMap[$period->status->color()] ?? 'bg-gray-100 text-gray-700';
                                    @endphp
                                    <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium {{ $badgeClass }}">
                                        {{ $period->status->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-600 text-right">
                                    {{ number_format($period->total_gross, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-sm font-semibold text-gray-900 text-right">
                                    {{ number_format($period->total_net, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a
                                        href="{{ route('salaries.period.show', $period) }}"
                                        class="inline-flex items-center gap-1 rounded-lg px-3 py-1.5 text-xs font-semibold text-purple-600 bg-purple-50 hover:bg-purple-100 transition-colors"
                                    >
                                        Voir détail
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                        </svg>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
