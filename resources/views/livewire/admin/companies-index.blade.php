<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="bg-white border-b border-gray-200 px-6 py-8 rounded-lg shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestion des Entreprises Clientes
                </h1>
                <p class="mt-2 text-lg text-gray-600">
                    Administration globale des {{ $this->stats['total'] }} entreprises WondoStock
                </p>
            </div>
            <div>
                <a href="{{ route('admin.companies.create') }}" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nouvelle Entreprise
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Entreprises</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($this->stats['total']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-blue-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-4 border border-green-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-green-600">Entreprises Actives</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($this->stats['active']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-green-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Inactives</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ number_format($this->stats['inactive']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-yellow-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/>
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Avec Abonnement</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($this->stats['with_subscription']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Recherche -->
            <div class="relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher une entreprise..."
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm focus:border-red-500 focus:ring-red-500"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
            </div>

            <!-- Filtre statut -->
            <div>
                <select wire:model.live="statusFilter" class="block w-full rounded-lg border-gray-300 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les statuts</option>
                    <option value="active">Actives</option>
                    <option value="inactive">Inactives</option>
                    <option value="deleted">Supprimées</option>
                </select>
            </div>

            <!-- Filtre plan -->
            <div>
                <select wire:model.live="planFilter" class="block w-full rounded-lg border-gray-300 py-3 text-sm focus:border-red-500 focus:ring-red-500">
                    <option value="">Tous les plans</option>
                    @foreach($plans as $plan)
                        <option value="{{ $plan->slug }}">{{ $plan->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Bouton export -->
            <div>
                <button class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    Exporter CSV
                </button>
            </div>
        </div>
    </div>

    <!-- Tableau des entreprises -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- En-tête du tableau -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Entreprises Clientes</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $companies->total() }} entreprise(s) trouvée(s)</p>
                </div>
            </div>
        </div>

        <!-- Tableau desktop -->
        <div class="hidden lg:block overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th scope="col" class="py-3 pl-6 pr-3 text-left">
                            <button wire:click="sortBy('name')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Entreprise
                                @if($sortBy === 'name')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Propriétaire
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Plan
                        </th>
                        <th scope="col" class="px-3 py-3 text-left">
                            <button wire:click="sortBy('users_count')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Utilisateurs
                                @if($sortBy === 'users_count')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Activité
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Statut
                        </th>
                        <th scope="col" class="relative py-3 pl-3 pr-6 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($companies as $company)
                        <tr wire:key="{{ $company->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-red-700">
                                            {{ strtoupper(substr($company->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ $company->name }}</p>
                                        <p class="text-xs text-gray-500">{{ $company->email }}</p>
                                        <p class="text-xs text-gray-400">
                                            Créée: {{ $company->created_at->format('d/m/Y') }}
                                        </p>
                                        @if($company->deleted_at)
                                            <p class="text-xs text-red-600 font-medium">
                                                Supprimée: {{ $company->deleted_at->format('d/m/Y') }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                @if($company->owner)
                                    <div class="text-sm text-gray-700">{{ $company->owner->name }}</div>
                                    <div class="text-xs text-gray-500">{{ $company->owner->email }}</div>
                                @else
                                    <span class="text-xs text-gray-400">Aucun propriétaire</span>
                                @endif
                            </td>
                            <td class="px-3 py-4">
                                {!! $company->plan_badge !!}
                                @if($company->subscription)
                                    <div class="text-xs text-gray-500 mt-1">
                                        {{ number_format($company->revenue, 2) }}€/mois
                                    </div>
                                @endif
                            </td>
                            <td class="px-3 py-4">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $company->users_count }} utilisateur(s)
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ $company->products_count }} produits • {{ $company->documents_count }} docs
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="text-xs text-gray-500">
                                    {{ $company->last_login ?? 'Aucune connexion' }}
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                {!! $company->status_badge !!}
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if(!$company->deleted_at)
                                        <!-- Bouton Voir -->
                                        <a href="{{ route('admin.companies.show', $company) }}" 
                                           class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Voir
                                        </a>
                                        
                                        <!-- Bouton Modifier -->
                                        <a href="{{ route('admin.companies.edit', $company) }}" 
                                                class="inline-flex items-center gap-1 rounded-lg bg-purple-100 px-3 py-2 text-xs font-medium text-purple-700 hover:bg-purple-200 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Modifier
                                        </a>
                                        
                                        <!-- Bouton Activer/Désactiver -->
                                        <button wire:click="toggleCompanyStatus({{ $company->id }})" 
                                                wire:confirm="Êtes-vous sûr de vouloir {{ $company->is_active ? 'désactiver' : 'activer' }} cette entreprise ?"
                                                class="inline-flex items-center gap-1 rounded-lg {{ $company->is_active ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} px-3 py-2 text-xs font-medium transition-colors duration-200">
                                            @if($company->is_active)
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636" />
                                                </svg>
                                                Désactiver
                                            @else
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Activer
                                            @endif
                                        </button>

                                        <!-- Bouton Supprimer -->
                                        <button wire:click="deleteCompany({{ $company->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir supprimer cette entreprise ? Cette action peut être annulée."
                                                class="inline-flex items-center gap-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 px-3 py-2 text-xs font-medium transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                            Supprimer
                                        </button>
                                    @else
                                        <!-- Bouton Restaurer -->
                                        <button wire:click="restoreCompany({{ $company->id }})"
                                                wire:confirm="Êtes-vous sûr de vouloir restaurer cette entreprise ?"
                                                class="inline-flex items-center gap-1 rounded-lg bg-green-100 text-green-700 hover:bg-green-200 px-3 py-2 text-xs font-medium transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6M3 9h12a6 6 0 010 12h-3" />
                                            </svg>
                                            Restaurer
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Aucune entreprise trouvée</p>
                                        <p class="text-sm text-gray-500 mt-1">Modifiez vos critères de recherche ou ajoutez votre première entreprise</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Version mobile responsive -->
        <div class="lg:hidden divide-y divide-gray-200">
            @forelse ($companies as $company)
                <div wire:key="mobile-{{ $company->id }}" class="p-6">
                    <div class="flex items-start justify-between mb-4">
                        <div class="flex items-center gap-3 flex-1">
                            <div class="h-12 w-12 rounded-full bg-red-100 flex items-center justify-center">
                                <span class="text-sm font-semibold text-red-700">
                                    {{ strtoupper(substr($company->name, 0, 2)) }}
                                </span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-semibold text-gray-900">{{ $company->name }}</p>
                                <p class="text-xs text-gray-500">{{ $company->email }}</p>
                            </div>
                        </div>
                        {!! $company->status_badge !!}
                    </div>
                    
                    <div class="grid grid-cols-2 gap-3 text-xs mb-4">
                        <div>
                            <span class="text-gray-500">Propriétaire:</span>
                            <p class="text-gray-900 font-medium">{{ $company->owner?->name ?? 'N/A' }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Plan:</span>
                            <div class="mt-1">{!! $company->plan_badge !!}</div>
                        </div>
                        <div>
                            <span class="text-gray-500">Utilisateurs:</span>
                            <p class="text-gray-900 font-medium">{{ $company->users_count }}</p>
                        </div>
                        <div>
                            <span class="text-gray-500">Revenus:</span>
                            <p class="text-gray-900 font-medium">{{ number_format($company->revenue, 2) }}€</p>
                        </div>
                    </div>
                    
                    <div class="flex gap-2">
                        <button class="flex-1 text-xs bg-blue-100 text-blue-700 px-3 py-2 rounded-lg hover:bg-blue-200 transition-colors text-center font-medium">
                            Voir Détails
                        </button>
                        @if(!$company->deleted_at)
                            <button wire:click="toggleCompanyStatus({{ $company->id }})" 
                                    class="flex-1 text-xs {{ $company->is_active ? 'bg-orange-100 text-orange-700 hover:bg-orange-200' : 'bg-green-100 text-green-700 hover:bg-green-200' }} px-3 py-2 rounded-lg transition-colors text-center font-medium">
                                {{ $company->is_active ? 'Désactiver' : 'Activer' }}
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="py-16 text-center">
                    <div class="flex flex-col items-center gap-4">
                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                            </svg>
                        </div>
                        <div>
                            <p class="text-lg font-medium text-gray-900">Aucune entreprise</p>
                            <p class="text-sm text-gray-500 mt-1">Les entreprises apparaîtront ici</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        @if($companies->hasPages())
            <div class="border-t border-gray-200 bg-white px-6 py-4">
                {{ $companies->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de création/édition d'entreprise -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit="saveCompany">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $editingCompany ? 'Modifier l\'Entreprise' : 'Nouvelle Entreprise' }}
                            </h3>

                            <div class="space-y-6">
                                <!-- Informations de l'entreprise -->
                                <div>
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Informations de l'entreprise</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nom de l'entreprise *</label>
                                            <input type="text" wire:model="companyName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('companyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Raison sociale</label>
                                            <input type="text" wire:model="companyLegalName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('companyLegalName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email *</label>
                                            <input type="email" wire:model="companyEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('companyEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                                            <input type="text" wire:model="companyPhone" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('companyPhone') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">Adresse</label>
                                            <textarea wire:model="companyAddress" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500"></textarea>
                                            @error('companyAddress') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">RCCM</label>
                                            <input type="text" wire:model="companyRccm" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('companyRccm') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">NIF</label>
                                            <input type="text" wire:model="companyNif" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('companyNif') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <div class="mt-4">
                                        <div class="flex items-center">
                                            <input type="checkbox" wire:model="companyIsActive" id="companyIsActive" class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                            <label for="companyIsActive" class="ml-2 block text-sm text-gray-900">
                                                Entreprise active
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations du propriétaire -->
                                <div class="border-t border-gray-200 pt-6">
                                    <h4 class="text-md font-medium text-gray-900 mb-4">Propriétaire de l'entreprise</h4>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nom du propriétaire *</label>
                                            <input type="text" wire:model="ownerName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('ownerName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email du propriétaire *</label>
                                            <input type="email" wire:model="ownerEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('ownerEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700">
                                                Mot de passe {{ $editingCompany ? '(laisser vide pour ne pas changer)' : '*' }}
                                            </label>
                                            <input type="password" wire:model="ownerPassword" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                            @error('ownerPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingCompany ? 'Mettre à jour' : 'Créer' }}
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