@section('title', 'Journal d\'audit')

@php
    $modelLabels = [
        \App\Models\Document::class     => 'Vente',
        \App\Models\Expense::class      => 'Dépense',
        \App\Models\SalaryPeriod::class => 'Période salaire',
        \App\Models\DeliveryTrip::class => 'Tournée',
        \App\Models\StockMovement::class => 'Mouvement stock',
    ];

    $eventColors = [
        'created' => 'bg-emerald-100 text-emerald-800',
        'updated' => 'bg-amber-100 text-amber-800',
        'deleted' => 'bg-red-100 text-red-800',
    ];

    $eventLabels = [
        'created' => 'Création',
        'updated' => 'Modification',
        'deleted' => 'Suppression',
    ];
@endphp

<div class="space-y-8">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-slate-700 to-slate-800 p-8 shadow-2xl">
        <div class="absolute inset-0 pointer-events-none bg-gradient-to-br from-slate-600/20 to-slate-900/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h1 class="mb-2 text-3xl font-bold text-white">Journal d'audit</h1>
                    <p class="text-lg text-slate-300">Historique complet des actions effectuées dans l'application</p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    {{-- Filtres --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Événement</label>
                <select
                    wire:model.live="filterEvent"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Tous les événements</option>
                    <option value="created">Création</option>
                    <option value="updated">Modification</option>
                    <option value="deleted">Suppression</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Type d'entité</label>
                <select
                    wire:model.live="filterModel"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Toutes les entités</option>
                    @foreach ($modelTypes as $type)
                        <option value="{{ $type }}">{{ $modelLabels[$type] ?? class_basename($type) }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-700 mb-1">Utilisateur</label>
                <select
                    wire:model.live="filterUser"
                    class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-500"
                >
                    <option value="">Tous les utilisateurs</option>
                    @foreach ($users as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                    @endforeach
                </select>
            </div>
        </div>
    </div>

    {{-- Table --}}
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Date</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Utilisateur</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Événement</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Entité</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">Détails</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold uppercase tracking-wide text-gray-500">IP</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($audits as $audit)
                        <tr wire:key="audit-{{ $audit->id }}" class="hover:bg-gray-50 transition-colors">
                            <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                {{ $audit->created_at->format('d/m/Y H:i') }}
                            </td>

                            <td class="px-4 py-3 text-sm font-medium text-gray-900">
                                {{ $audit->user?->name ?? '—' }}
                            </td>

                            <td class="px-4 py-3">
                                <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold {{ $eventColors[$audit->event] ?? 'bg-gray-100 text-gray-700' }}">
                                    {{ $eventLabels[$audit->event] ?? ucfirst($audit->event) }}
                                </span>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-700">
                                <span class="font-medium">{{ $modelLabels[$audit->auditable_type] ?? class_basename($audit->auditable_type) }}</span>
                                <span class="ml-1 text-xs text-gray-400">#{{ $audit->auditable_id }}</span>
                            </td>

                            <td class="px-4 py-3 text-sm text-gray-600 max-w-xs">
                                @if ($audit->event === 'updated' && $audit->new_values)
                                    <div x-data="{ open: false }">
                                        <button @click="open = !open" type="button" class="text-xs text-indigo-600 hover:underline">
                                            <span x-text="open ? 'Masquer' : 'Voir les modifications'"></span>
                                        </button>
                                        <div x-show="open" x-transition class="mt-2 space-y-1">
                                            @foreach ($audit->new_values as $field => $newVal)
                                                <div class="text-xs">
                                                    <span class="font-medium text-gray-700">{{ $field }}</span>
                                                    @if (isset($audit->old_values[$field]))
                                                        <span class="text-red-500 line-through ml-1">{{ Str::limit((string) $audit->old_values[$field], 30) }}</span>
                                                    @endif
                                                    <span class="text-emerald-600 ml-1">→ {{ Str::limit((string) $newVal, 30) }}</span>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @elseif ($audit->event === 'created' && $audit->new_values)
                                    <span class="text-xs text-gray-400">{{ count($audit->new_values) }} champ(s) enregistré(s)</span>
                                @elseif ($audit->event === 'deleted')
                                    <span class="text-xs text-red-500">Enregistrement supprimé</span>
                                @endif
                            </td>

                            <td class="whitespace-nowrap px-4 py-3 text-xs text-gray-400">
                                {{ $audit->ip_address ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center text-sm text-gray-500">
                                Aucun événement enregistré pour les filtres sélectionnés.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if ($audits->hasPages())
            <div class="border-t border-gray-200 px-4 py-4">
                {{ $audits->links() }}
            </div>
        @endif
    </div>
</div>
