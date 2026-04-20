@section('title', 'Bulletin — ' . $slip->employee_name)

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-600 to-violet-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-purple-600/20 to-violet-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div class="flex items-center gap-4">
                    <a href="{{ route('salaries.period.show', $slip->period) }}"
                       class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-white mb-1">{{ $slip->employee_name }}</h1>
                        <p class="text-purple-100">Bulletin — {{ $slip->period->label }}</p>
                    </div>
                </div>
                <a
                    href="{{ route('salaries.slip.pdf', $slip) }}"
                    target="_blank"
                    class="inline-flex items-center gap-2 rounded-xl bg-white/10 px-4 py-2.5 text-sm font-semibold text-white ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200"
                >
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3"/>
                    </svg>
                    Télécharger PDF
                </a>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Détail du bulletin --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Détail de la rémunération</h3>
        </div>
        <div class="p-6">
            <table class="min-w-full divide-y divide-gray-100">
                <tbody class="divide-y divide-gray-100">
                    <tr>
                        <td class="py-3 text-sm text-gray-700">Salaire de base</td>
                        <td class="py-3 text-sm font-semibold text-gray-900 text-right">{{ number_format($slip->base_salary, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @if ($slip->total_commissions > 0)
                    <tr>
                        <td class="py-3 text-sm text-gray-700">
                            Commissions tournées
                            @if ($slip->trips_count > 0)
                                <span class="text-xs text-gray-500">({{ $slip->trips_count }} tournée(s))</span>
                            @endif
                        </td>
                        <td class="py-3 text-sm text-gray-900 text-right">{{ number_format($slip->total_commissions, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endif
                    @if ($slip->mission_allowances > 0)
                    <tr>
                        <td class="py-3 text-sm text-gray-700">Primes de mission</td>
                        <td class="py-3 text-sm text-gray-900 text-right">{{ number_format($slip->mission_allowances, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endif

                    @foreach ($slip->deductions->where('type', 'bonus') as $bonus)
                    <tr class="bg-green-50">
                        <td class="py-3 text-sm text-green-700">{{ $bonus->label }} <span class="text-xs">(prime)</span></td>
                        <td class="py-3 text-sm font-medium text-green-700 text-right">+ {{ number_format($bonus->amount, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endforeach

                    <tr class="bg-blue-50">
                        <td class="py-3 text-sm font-bold text-blue-800">SALAIRE BRUT</td>
                        <td class="py-3 text-sm font-bold text-blue-800 text-right">{{ number_format($slip->gross_salary, 0, ',', ' ') }} FCFA</td>
                    </tr>

                    @foreach ($slip->deductions->where('type', 'deduction') as $deduction)
                    <tr class="bg-red-50">
                        <td class="py-3 text-sm text-red-700">{{ $deduction->label }} <span class="text-xs">(retenue)</span></td>
                        <td class="py-3 text-sm font-medium text-red-700 text-right">
                            - {{ number_format($deduction->amount, 0, ',', ' ') }} FCFA
                            <button
                                wire:click="removeDeduction({{ $deduction->id }})"
                                wire:confirm="Supprimer cette ligne ?"
                                type="button"
                                class="ml-2 text-red-400 hover:text-red-600"
                            >
                                <svg class="h-3.5 w-3.5 inline" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </td>
                    </tr>
                    @endforeach

                    @if ($slip->total_advances > 0)
                    <tr class="bg-orange-50">
                        <td class="py-3 text-sm text-orange-700">Avances déduites</td>
                        <td class="py-3 text-sm font-medium text-orange-700 text-right">- {{ number_format($slip->total_advances, 0, ',', ' ') }} FCFA</td>
                    </tr>
                    @endif

                    <tr class="bg-purple-600">
                        <td class="py-4 px-2 text-base font-bold text-white rounded-bl-lg">NET À PAYER</td>
                        <td class="py-4 px-2 text-base font-bold text-white text-right rounded-br-lg">{{ number_format($slip->net_salary, 0, ',', ' ') }} FCFA</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    {{-- Ajouter prime/déduction --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <h3 class="text-lg font-semibold text-gray-900">Ajouter une prime ou retenue</h3>
        </div>
        <form wire:submit="addDeduction" class="p-6">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                <div class="sm:col-span-1">
                    <label class="block text-sm font-medium text-gray-700 mb-1">Libellé <span class="text-red-500">*</span></label>
                    <input
                        type="text"
                        wire:model="deductionLabel"
                        placeholder="Ex: CNSS, Prime transport..."
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('deductionLabel')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                    <select
                        wire:model="deductionType"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                        <option value="deduction">Retenue / Déduction</option>
                        <option value="bonus">Prime / Bonus</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA) <span class="text-red-500">*</span></label>
                    <input
                        type="number"
                        wire:model="deductionAmount"
                        min="1"
                        placeholder="0"
                        class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                    >
                    @error('deductionAmount')
                        <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                    @enderror
                </div>
            </div>
            <div class="mt-4">
                <button
                    type="submit"
                    class="px-4 py-2 bg-indigo-600 text-white text-sm font-semibold rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    Ajouter la ligne
                </button>
            </div>
        </form>
    </div>
</div>
