@section('title', 'Période — ' . $period->label)

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-violet-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-violet-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('salaries.index') }}"
                       class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">{{ $period->label }}</h1>
                        @php
                            $colorMap = ['gray' => 'bg-white/20 text-white', 'blue' => 'bg-blue-200/30 text-blue-100', 'green' => 'bg-green-200/30 text-green-100'];
                            $badgeClass = $colorMap[$period->status->color()] ?? 'bg-white/20 text-white';
                        @endphp
                        <span class="inline-flex items-center rounded-full px-3 py-0.5 text-sm font-medium {{ $badgeClass }}">
                            {{ $period->status->label() }}
                        </span>
                    </div>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    <button
                        wire:click="generateSlips"
                        type="button"
                        class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        {{ $period->slips->isEmpty() ? 'Générer les bulletins' : 'Régénérer les bulletins' }}
                    </button>
                    @if ($period->isDraft() && $period->slips->isNotEmpty())
                        <button
                            wire:click="validatePeriod"
                            wire:confirm="Valider cette période de paie ?"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-blue-500/80 px-4 py-2.5 text-sm font-semibold text-white hover:bg-blue-600 transition-all duration-200"
                        >
                            Valider la période
                        </button>
                    @endif
                    @if ($period->isValidated())
                        <button
                            wire:click="markPaid"
                            wire:confirm="Marquer cette période comme payée ?"
                            type="button"
                            class="inline-flex items-center gap-2 rounded-xl bg-green-500/80 px-4 py-2.5 text-sm font-semibold text-white hover:bg-green-600 transition-all duration-200"
                        >
                            Marquer payée
                        </button>
                    @endif
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Totaux --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4">
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Brut total</p>
            <p class="mt-2 text-xl font-bold text-gray-900">{{ number_format($period->total_gross, 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-purple-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Déductions</p>
            <p class="mt-2 text-xl font-bold text-red-600">{{ number_format($period->total_deductions, 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-red-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-5">
            <p class="text-xs font-medium text-gray-500 uppercase tracking-wide">Avances déduites</p>
            <p class="mt-2 text-xl font-bold text-orange-600">{{ number_format($period->total_advances, 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-orange-200"></div>
        </div>
        <div class="overflow-hidden rounded-2xl bg-purple-50 border border-purple-100 shadow-sm p-5">
            <p class="text-xs font-medium text-purple-700 uppercase tracking-wide">Net à payer</p>
            <p class="mt-2 text-xl font-bold text-purple-700">{{ number_format($period->total_net, 0, ',', ' ') }} FCFA</p>
            <div class="mt-2 h-1 w-8 rounded-full bg-purple-300"></div>
        </div>
    </div>

    {{-- Table des bulletins --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Bulletins de salaire ({{ $period->slips->count() }})</h3>
        </div>

        @if ($period->slips->isEmpty())
            <div class="px-6 py-12 text-center">
                <p class="text-sm text-gray-500">Aucun bulletin généré. Cliquez sur "Générer les bulletins" pour créer les bulletins automatiquement.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-100">
                    <thead>
                        <tr class="bg-gray-50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-gray-500 uppercase tracking-wide">Employé</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Brut</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Déductions</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Avances</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Net</th>
                            <th class="px-6 py-3 text-right text-xs font-semibold text-gray-500 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100 bg-white">
                        @foreach ($period->slips as $slip)
                            <tr wire:key="slip-{{ $slip->id }}" class="hover:bg-gray-50 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="text-sm font-semibold text-gray-900">{{ $slip->employee_name }}</p>
                                    @if ($slip->trips_count > 0)
                                        <p class="text-xs text-gray-500">{{ $slip->trips_count }} tournée(s)</p>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-700 text-right">
                                    {{ number_format($slip->gross_salary, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-sm text-red-600 text-right">
                                    {{ $slip->total_deductions > 0 ? '- '.number_format($slip->total_deductions, 0, ',', ' ').' FCFA' : '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm text-orange-600 text-right">
                                    {{ $slip->total_advances > 0 ? '- '.number_format($slip->total_advances, 0, ',', ' ').' FCFA' : '—' }}
                                </td>
                                <td class="px-6 py-4 text-sm font-bold text-purple-700 text-right">
                                    {{ number_format($slip->net_salary, 0, ',', ' ') }} FCFA
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a
                                            href="{{ route('salaries.slip.show', $slip) }}"
                                            class="rounded-lg p-1.5 text-gray-400 hover:text-purple-600 hover:bg-purple-50 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z"/>
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                            </svg>
                                        </a>
                                        <a
                                            href="{{ route('salaries.slip.pdf', $slip) }}"
                                            target="_blank"
                                            class="rounded-lg p-1.5 text-gray-400 hover:text-red-600 hover:bg-red-50 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="bg-gray-50 border-t-2 border-gray-200">
                            <td class="px-6 py-4 text-sm font-bold text-gray-900">TOTAUX</td>
                            <td class="px-6 py-4 text-sm font-bold text-gray-900 text-right">{{ number_format($period->total_gross, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 text-sm font-bold text-red-600 text-right">{{ number_format($period->total_deductions, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 text-sm font-bold text-orange-600 text-right">{{ number_format($period->total_advances, 0, ',', ' ') }} FCFA</td>
                            <td class="px-6 py-4 text-sm font-bold text-purple-700 text-right">{{ number_format($period->total_net, 0, ',', ' ') }} FCFA</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        @endif
    </div>
</div>
