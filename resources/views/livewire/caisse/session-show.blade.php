@section('title', 'Session de Caisse')

<div class="space-y-8" x-data="{ showMovementForm: @entangle('showMovementForm') }">
    {{-- En-tête --}}
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-teal-600 to-emerald-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-teal-600/20 to-emerald-700/20 backdrop-blur-sm pointer-events-none"></div>
        <div class="relative">
            <div class="flex items-center gap-4">
                <a href="{{ route('caisse.index') }}"
                   class="inline-flex items-center justify-center h-10 w-10 rounded-xl bg-white/10 text-white hover:bg-white/20 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-3xl font-bold text-white mb-1">Session de Caisse</h1>
                    <p class="text-teal-100">
                        {{ $session->store?->name }} — {{ $session->session_date->format('d/m/Y') }}
                        @if ($session->isOpen())
                            <span class="ml-2 inline-flex items-center rounded-full bg-green-400/20 px-2.5 py-0.5 text-xs font-medium text-green-100">Ouverte</span>
                        @else
                            <span class="ml-2 inline-flex items-center rounded-full bg-gray-400/20 px-2.5 py-0.5 text-xs font-medium text-gray-100">Clôturée</span>
                        @endif
                    </p>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl pointer-events-none"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl pointer-events-none"></div>
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Colonne principale : liste des mouvements --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Boutons d'action (session ouverte uniquement) --}}
            @if ($session->isOpen())
                <div class="flex flex-wrap items-center gap-3">
                    <button
                        type="button"
                        wire:click="openMovementForm('cash_in')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Encaissement
                    </button>
                    <button
                        type="button"
                        wire:click="openMovementForm('cash_out')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-red-600 text-white text-sm font-semibold rounded-lg hover:bg-red-700 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                        </svg>
                        Sortie espèces
                    </button>
                    <button
                        type="button"
                        wire:click="openMovementForm('remittance')"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-orange-500 text-white text-sm font-semibold rounded-lg hover:bg-orange-600 transition-colors"
                    >
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25M2.25 6v9m18-10.5v.75c0 .414.336.75.75.75h.75m-1.5-1.5h.375c.621 0 1.125.504 1.125 1.125v9.75c0 .621-.504 1.125-1.125 1.125h-.375m1.5-1.5H21a.75.75 0 0 0-.75.75v.75m0 0H3.75m0 0h-.375a1.125 1.125 0 0 1-1.125-1.125V15m1.5 1.5v-.75A.75.75 0 0 0 3 15h-.75" />
                        </svg>
                        Versement DG
                    </button>
                </div>
            @endif

            {{-- Formulaire ajout de mouvement (slide-down) --}}
            <div
                x-show="showMovementForm"
                x-transition:enter="transition ease-out duration-200"
                x-transition:enter-start="opacity-0 -translate-y-2"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-150"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 -translate-y-2"
                class="overflow-hidden rounded-2xl bg-white shadow-sm border border-teal-200"
            >
                <div class="border-b border-teal-100 bg-teal-50 px-6 py-4 flex items-center justify-between">
                    <h3 class="text-base font-semibold text-teal-900">Nouveau mouvement</h3>
                    <button type="button" wire:click="closeMovementForm" class="text-teal-600 hover:text-teal-800">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <div class="p-6">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Type</label>
                            <select
                                wire:model="movementType"
                                class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                            >
                                @foreach ($movementTypes as $type)
                                    <option value="{{ $type->value }}">{{ $type->label() }}</option>
                                @endforeach
                            </select>
                            @error('movementType')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Montant (FCFA)</label>
                            <input
                                type="number"
                                wire:model="movementAmount"
                                min="1"
                                placeholder="0"
                                class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                            >
                            @error('movementAmount')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Libellé</label>
                            <input
                                type="text"
                                wire:model="movementLabel"
                                placeholder="Description..."
                                class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                            >
                            @error('movementLabel')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div class="mt-4 flex items-center gap-3">
                        <button
                            type="button"
                            wire:click="addMovement"
                            wire:loading.attr="disabled"
                            class="px-4 py-2 bg-teal-600 text-white text-sm font-semibold rounded-lg hover:bg-teal-700 transition-colors disabled:opacity-60"
                        >
                            <span wire:loading.remove wire:target="addMovement">Enregistrer</span>
                            <span wire:loading wire:target="addMovement">Enregistrement…</span>
                        </button>
                        <button
                            type="button"
                            wire:click="closeMovementForm"
                            class="px-4 py-2 text-sm font-medium text-gray-600 hover:text-gray-900 transition-colors"
                        >
                            Annuler
                        </button>
                    </div>
                </div>
            </div>

            {{-- Liste des mouvements --}}
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                    <h3 class="text-lg font-semibold text-gray-900">Mouvements de caisse</h3>
                    <p class="text-sm text-gray-500 mt-0.5">{{ $movements->count() }} mouvement(s) enregistré(s)</p>
                </div>

                @if ($movements->isEmpty())
                    <div class="p-12 text-center">
                        <svg class="mx-auto h-12 w-12 text-gray-300" fill="none" viewBox="0 0 24 24" stroke-width="1" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 0 1 3 6h-.75m0 0v-.375c0-.621.504-1.125 1.125-1.125H20.25" />
                        </svg>
                        <p class="mt-3 text-sm text-gray-500">Aucun mouvement pour cette session.</p>
                    </div>
                @else
                    <div class="divide-y divide-gray-100">
                        @foreach ($movements as $movement)
                            <div class="flex items-center justify-between px-6 py-4 hover:bg-gray-50 transition-colors" wire:key="movement-{{ $movement->id }}">
                                <div class="flex items-center gap-4 min-w-0">
                                    {{-- Icône type --}}
                                    <div class="shrink-0">
                                        @if ($movement->type === \App\Enums\CashMovementType::CashIn)
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-teal-100">
                                                <svg class="h-4 w-4 text-teal-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                                </svg>
                                            </span>
                                        @elseif ($movement->type === \App\Enums\CashMovementType::CashOut)
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-red-100">
                                                <svg class="h-4 w-4 text-red-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14" />
                                                </svg>
                                            </span>
                                        @else
                                            <span class="inline-flex h-9 w-9 items-center justify-center rounded-full bg-orange-100">
                                                <svg class="h-4 w-4 text-orange-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0 1 15.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75" />
                                                </svg>
                                            </span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-gray-900 truncate">{{ $movement->label }}</p>
                                        <p class="text-xs text-gray-500">
                                            {{ $movement->type->label() }}
                                            @if ($movement->expense_id)
                                                · <span class="text-indigo-600">Dépense liée</span>
                                            @endif
                                            @if ($movement->user)
                                                · {{ $movement->user->name }}
                                            @endif
                                            · {{ $movement->movement_date->format('d/m/Y') }}
                                        </p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-4 shrink-0 ml-4">
                                    <span @class([
                                        'text-sm font-bold tabular-nums',
                                        'text-teal-700' => $movement->type === \App\Enums\CashMovementType::CashIn,
                                        'text-red-700' => $movement->type === \App\Enums\CashMovementType::CashOut,
                                        'text-orange-700' => $movement->type === \App\Enums\CashMovementType::Remittance,
                                    ])>
                                        {{ $movement->type->isDebit() ? '−' : '+' }} {{ number_format($movement->amount, 0, ',', ' ') }} FCFA
                                    </span>
                                    @if ($session->isOpen() && $movement->expense_id === null)
                                        <button
                                            type="button"
                                            wire:click="deleteMovement({{ $movement->id }})"
                                            wire:confirm="Supprimer ce mouvement ?"
                                            class="text-gray-400 hover:text-red-600 transition-colors"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Notes de session --}}
            @if ($session->isOpen())
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <h3 class="text-base font-semibold text-gray-900">Notes</h3>
                    </div>
                    <div class="p-6">
                        <textarea
                            wire:model="notes"
                            rows="3"
                            placeholder="Remarques sur la session..."
                            class="block w-full rounded-lg border-0 py-2 px-3 text-sm text-gray-900 bg-white shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-teal-500"
                        ></textarea>
                        <div class="mt-4 flex items-center gap-3">
                            <button
                                type="button"
                                wire:click="saveNotes"
                                class="px-4 py-2 bg-gray-700 text-white text-sm font-semibold rounded-lg hover:bg-gray-800 transition-colors"
                            >
                                Sauvegarder les notes
                            </button>
                            <button
                                type="button"
                                wire:click="close"
                                wire:confirm="Clôturer définitivement cette session ?"
                                class="px-4 py-2 bg-orange-600 text-white text-sm font-semibold rounded-lg hover:bg-orange-700 transition-colors"
                            >
                                Clôturer la session
                            </button>
                        </div>
                    </div>
                </div>
            @else
                @if ($session->notes)
                    <div class="rounded-2xl bg-white shadow-sm border border-gray-200 p-6">
                        <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-2">Notes</p>
                        <p class="text-sm text-gray-700">{{ $session->notes }}</p>
                    </div>
                @endif
                @if ($session->closed_at)
                    <p class="text-xs text-gray-400 text-center">Clôturée le {{ $session->closed_at->format('d/m/Y à H:i') }}</p>
                @endif
            @endif
        </div>

        {{-- Sidebar récapitulatif financier --}}
        <div class="space-y-4">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200">
                <div class="border-b border-gray-100 bg-gradient-to-r from-teal-50 to-white px-6 py-4">
                    <h3 class="text-base font-semibold text-gray-900">Récapitulatif financier</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-gray-600">Solde d'ouverture</span>
                        <span class="text-sm font-semibold text-gray-900">{{ number_format($session->opening_balance, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-teal-700">+ Encaissements</span>
                        <span class="text-sm font-semibold text-teal-700">{{ number_format($session->cash_in, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-red-700">— Sorties espèces</span>
                        <span class="text-sm font-semibold text-red-700">{{ number_format($session->cash_out, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                        <span class="text-sm text-orange-700">— Versements DG</span>
                        <span class="text-sm font-semibold text-orange-700">{{ number_format($session->remittances, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="flex items-center justify-between py-3 rounded-lg bg-teal-50 px-3">
                        <span class="text-sm font-bold text-teal-800">= Solde de clôture</span>
                        <span class="text-base font-bold text-teal-800">{{ number_format($session->closing_balance, 0, ',', ' ') }} FCFA</span>
                    </div>
                </div>
            </div>

            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-200 p-5">
                <p class="text-xs font-medium text-gray-500 uppercase tracking-wide mb-3">Informations</p>
                <dl class="space-y-2">
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Boutique</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $session->store?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Date</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $session->session_date->format('d/m/Y') }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Ouvert par</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $session->user?->name ?? '—' }}</dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Statut</dt>
                        <dd>
                            @if ($session->isOpen())
                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Ouverte</span>
                            @else
                                <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700">Clôturée</span>
                            @endif
                        </dd>
                    </div>
                    <div class="flex items-center justify-between">
                        <dt class="text-sm text-gray-600">Mouvements</dt>
                        <dd class="text-sm font-medium text-gray-900">{{ $movements->count() }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</div>
