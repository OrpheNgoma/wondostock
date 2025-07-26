<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="bg-white border-b border-gray-200 px-6 py-8 rounded-lg shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestion des Abonnements
                </h1>
                <p class="mt-2 text-lg text-gray-600">
                    Vue globale et gestion centralisée de tous les abonnements
                </p>
            </div>
            <div class="flex items-center gap-3">
                <button wire:click="quickCreateSubscription" 
                        class="inline-flex items-center gap-2 rounded-lg bg-green-600 px-4 py-3 text-sm font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Création Rapide
                </button>
                <a href="{{ route('admin.subscriptions.create') }}" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nouvel Abonnement
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($this->stats['total_subscriptions']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Actifs</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($this->stats['active_subscriptions']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-green-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Expirés</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ number_format($this->stats['expired_subscriptions']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-yellow-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-red-50 to-red-100 rounded-lg p-4 border border-red-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-red-600">Annulés</p>
                        <p class="text-2xl font-bold text-red-900">{{ number_format($this->stats['cancelled_subscriptions']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-red-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Revenus/Mois</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($this->stats['monthly_revenue'], 2) }}€</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-indigo-50 to-indigo-100 rounded-lg p-4 border border-indigo-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-indigo-600">Entreprises</p>
                        <p class="text-2xl font-bold text-indigo-900">{{ number_format($this->stats['companies_with_subscriptions']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-indigo-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher par entreprise ou plan..."
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm focus:border-red-500 focus:ring-red-500"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
            </div>

            <div>
                <select wire:model.live="statusFilter" class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actifs</option>
                    <option value="expired">Expirés</option>
                    <option value="cancelled">Annulés</option>
                </select>
            </div>

            <div>
                <select wire:model.live="planFilter" class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->id }}">{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button wire:click="$set('search', '')" class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-3 py-2 text-sm font-medium text-gray-700 hover:bg-gray-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des abonnements -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- En-tête du tableau -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Abonnements</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $subscriptions->total() }} abonnement(s) trouvé(s)</p>
                </div>
            </div>
        </div>

        <!-- Tableau desktop -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 pl-6 pr-3 text-left">
                            <button wire:click="sortBy('company_id')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Entreprise
                                @if($sortBy === 'company_id')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left">
                            <button wire:click="sortBy('plan_id')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Plan
                                @if($sortBy === 'plan_id')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left">
                            <button wire:click="sortBy('starts_at')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Période
                                @if($sortBy === 'starts_at')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left">
                            <button wire:click="sortBy('status')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Statut
                                @if($sortBy === 'status')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Revenus
                        </th>
                        <th scope="col" class="relative py-3 pl-3 pr-6 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($subscriptions as $subscription)
                        <tr wire:key="{{ $subscription->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-6 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-gradient-to-r from-red-500 to-orange-500 flex items-center justify-center text-white text-sm font-bold">
                                        {{ strtoupper(substr($subscription->company->name, 0, 2)) }}
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $subscription->company->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $subscription->company->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-6">
                                @php
                                    $planConfig = match($subscription->plan->slug) {
                                        'essentiel' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'ring' => 'ring-blue-600/20'],
                                        'pro' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'ring' => 'ring-purple-600/20'],
                                        'entreprise' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'ring' => 'ring-amber-600/20'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20'],
                                    };
                                @endphp
                                <div>
                                    <span class="inline-flex items-center rounded-full {{ $planConfig['bg'] }} px-3 py-1 text-sm font-medium {{ $planConfig['text'] }} ring-1 {{ $planConfig['ring'] }}">
                                        {{ $subscription->plan->name }}
                                    </span>
                                    <p class="text-xs text-gray-500 mt-1">{{ number_format($subscription->plan->price, 2) }}€/mois</p>
                                </div>
                            </td>
                            <td class="px-3 py-6 text-sm text-gray-900">
                                <div>
                                    <p class="font-medium">Du {{ $subscription->starts_at?->format('d/m/Y') }}</p>
                                    @if($subscription->ends_at)
                                        <p class="text-xs text-gray-500">Au {{ $subscription->ends_at->format('d/m/Y') }}</p>
                                    @else
                                        <p class="text-xs text-green-600">Permanent</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-6">
                                @php
                                    $statusConfig = match($subscription->status) {
                                        'active' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'ring' => 'ring-green-600/20'],
                                        'expired' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'ring' => 'ring-yellow-600/20'],
                                        'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'ring' => 'ring-red-600/20'],
                                        default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20'],
                                    };
                                @endphp
                                <span class="inline-flex items-center rounded-full {{ $statusConfig['bg'] }} px-2 py-1 text-xs font-medium {{ $statusConfig['text'] }} ring-1 {{ $statusConfig['ring'] }}">
                                    {{ ucfirst($subscription->status) }}
                                </span>
                            </td>
                            <td class="px-3 py-6">
                                <div class="text-sm font-semibold text-gray-900">
                                    {{ number_format($subscription->plan->price, 2) }}€
                                </div>
                                <div class="text-xs text-gray-500">
                                    par mois
                                </div>
                            </td>
                            <td class="relative py-6 pl-3 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Bouton Modifier -->
                                    <a href="{{ route('admin.subscriptions.edit', $subscription) }}" 
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Modifier
                                    </a>

                                    @if($subscription->status === 'active')
                                        <!-- Bouton Annuler -->
                                        <button wire:click="cancelSubscription({{ $subscription->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir annuler cet abonnement ?"
                                                class="inline-flex items-center gap-1 rounded-lg bg-orange-100 text-orange-700 hover:bg-orange-200 px-3 py-2 text-xs font-medium transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                            Annuler
                                        </button>
                                    @elseif($subscription->status === 'expired' || $subscription->status === 'cancelled')
                                        <!-- Bouton Renouveler -->
                                        <button wire:click="renewSubscription({{ $subscription->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir renouveler cet abonnement ?"
                                                class="inline-flex items-center gap-1 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 px-3 py-2 text-xs font-medium transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                            </svg>
                                            Renouveler
                                        </button>
                                    @endif

                                    <!-- Bouton Supprimer -->
                                    <button wire:click="deleteSubscription({{ $subscription->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cet abonnement ? Cette action est irréversible."
                                            class="inline-flex items-center gap-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 px-3 py-2 text-xs font-medium transition-colors duration-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Aucun abonnement trouvé</p>
                                        <p class="text-sm text-gray-500 mt-1">Créez le premier abonnement</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($subscriptions->hasPages())
            <div class="border-t border-gray-200 bg-white px-6 py-4">
                {{ $subscriptions->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de création/édition -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveSubscription">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $editingSubscription ? 'Modifier l\'Abonnement' : 'Nouvel Abonnement' }}
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Entreprise</label>
                                    <select wire:model="company_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">Sélectionner une entreprise</option>
                                        @foreach($companies as $company)
                                            <option value="{{ $company->id }}">{{ $company->name }} - {{ $company->email }}</option>
                                        @endforeach
                                    </select>
                                    @error('company_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plan</label>
                                    <select wire:model="plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">Sélectionner un plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }} - {{ number_format($plan->price, 2) }}€/mois</option>
                                        @endforeach
                                    </select>
                                    @error('plan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date de début</label>
                                    <input type="date" wire:model="starts_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('starts_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date de fin (optionnel)</label>
                                    <input type="date" wire:model="ends_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('ends_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    <p class="text-xs text-gray-500 mt-1">Laisser vide pour un abonnement permanent</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Statut</label>
                                    <select wire:model="status" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="active">Actif</option>
                                        <option value="expired">Expiré</option>
                                        <option value="cancelled">Annulé</option>
                                    </select>
                                    @error('status') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingSubscription ? 'Mettre à jour' : 'Créer' }}
                            </button>
                            <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal de création rapide -->
    @if($showQuickCreateModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveSubscription">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                Création Rapide d'Abonnement
                            </h3>

                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Rechercher une entreprise</label>
                                    <div class="relative">
                                        <input type="text" wire:model.live.debounce.300ms="companySearch" 
                                               placeholder="Tapez le nom ou email de l'entreprise..."
                                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        
                                        @if($this->searchCompanies->isNotEmpty())
                                            <div class="absolute z-10 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-sm ring-1 ring-black ring-opacity-5 overflow-auto">
                                                @foreach($this->searchCompanies as $company)
                                                    <button type="button" wire:click="selectCompany({{ $company->id }})"
                                                            class="w-full text-left px-4 py-2 hover:bg-gray-100 focus:bg-gray-100">
                                                        <div class="font-medium text-gray-900">{{ $company->name }}</div>
                                                        <div class="text-gray-500">{{ $company->email }}</div>
                                                    </button>
                                                @endforeach
                                            </div>
                                        @endif
                                    </div>
                                    
                                    @if($selectedCompanyId)
                                        @php $selectedCompany = $companies->find($selectedCompanyId); @endphp
                                        <div class="mt-2 p-3 bg-green-50 rounded-md border border-green-200">
                                            <div class="flex items-center gap-2">
                                                <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                <div>
                                                    <p class="text-sm font-medium text-green-900">{{ $selectedCompany->name }}</p>
                                                    <p class="text-xs text-green-700">{{ $selectedCompany->email }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    @error('company_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Plan</label>
                                    <select wire:model="plan_id" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                        <option value="">Sélectionner un plan</option>
                                        @foreach($plans as $plan)
                                            <option value="{{ $plan->id }}">{{ $plan->name }} - {{ number_format($plan->price, 2) }}€/mois</option>
                                        @endforeach
                                    </select>
                                    @error('plan_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Date de début</label>
                                    <input type="date" wire:model="starts_at" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('starts_at') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Créer Abonnement
                            </button>
                            <button type="button" wire:click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>