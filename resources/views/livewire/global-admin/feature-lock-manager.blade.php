<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Gestionnaire de Verrouillages
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Contrôlez l'accès aux fonctionnalités par entreprise
                </p>
            </div>
            
            <!-- Actions principales -->
            <div class="flex items-center gap-3">
                <button wire:click="clearExpiredLocks" 
                        class="inline-flex items-center gap-2 rounded-lg bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-amber-700 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                    </svg>
                    Nettoyer Expirés
                </button>
                
                <button wire:click="openLockModal(true)" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-red-700 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25z" />
                    </svg>
                    Verrouiller en Lot
                </button>
            </div>
        </div>

        <!-- Statistiques globales -->
        <div class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4 mt-6">
            <div class="bg-blue-50 overflow-hidden rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 9h.75v.75h-.75V9zm6.75 0h.75v.75h-.75V9zm-3 3h.75v.75h-.75V12zm0 3h.75v.75h-.75V15zm3-6h.75v.75h-.75V9zm0 3h.75v.75h-.75V12zm0 3h.75v.75h-.75V15zm3-9h.75v.75h-.75V6zm0 3h.75v.75h-.75V9zm0 3h.75v.75h-.75V12zm0 3h.75v.75h-.75V15z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-blue-900 truncate">Total Entreprises</dt>
                                <dd class="text-lg font-medium text-blue-900">{{ $globalStats['total_companies'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-red-50 overflow-hidden rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-red-900 truncate">Verrouillages Actifs</dt>
                                <dd class="text-lg font-medium text-red-900">{{ $globalStats['total_active_locks'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-amber-50 overflow-hidden rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-amber-900 truncate">Entreprises Affectées</dt>
                                <dd class="text-lg font-medium text-amber-900">{{ $globalStats['companies_with_locks'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-green-50 overflow-hidden rounded-lg">
                <div class="p-5">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="ml-5 w-0 flex-1">
                            <dl>
                                <dt class="text-sm font-medium text-green-900 truncate">Moyenne par Entreprise</dt>
                                <dd class="text-lg font-medium text-green-900">{{ $globalStats['average_locks_per_company'] }}</dd>
                            </dl>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200 p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-4">
            <!-- Recherche -->
            <div>
                <label for="search" class="block text-sm font-medium text-gray-700">Rechercher</label>
                <input wire:model.live="search" 
                       type="text" 
                       id="search"
                       class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                       placeholder="Nom d'entreprise...">
            </div>

            <!-- Filtre par entreprise -->
            <div>
                <label for="company-filter" class="block text-sm font-medium text-gray-700">Entreprise</label>
                <select wire:model.live="selectedCompany" 
                        id="company-filter"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="">Toutes les entreprises</option>
                    @foreach($this->getAllCompanies() as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filtre par statut -->
            <div>
                <label for="status-filter" class="block text-sm font-medium text-gray-700">Statut</label>
                <select wire:model.live="statusFilter" 
                        id="status-filter"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                    <option value="all">Tous</option>
                    <option value="locked">Avec verrouillages</option>
                    <option value="unlocked">Sans verrouillage</option>
                </select>
            </div>

            <!-- Actions -->
            <div class="flex items-end">
                <button wire:click="$dispatch('refresh-locks')" 
                        class="inline-flex items-center gap-2 rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des entreprises -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Entreprises et Verrouillages</h3>
            
            @if($companies->count() > 0)
                <div class="space-y-4">
                    @foreach($companies as $company)
                        <div class="border border-gray-200 rounded-lg">
                            <!-- En-tête de l'entreprise -->
                            <div class="bg-gray-50 px-4 py-3 border-b border-gray-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div>
                                            <h4 class="text-sm font-medium text-gray-900">{{ $company->name }}</h4>
                                            <p class="text-xs text-gray-500">
                                                {{ $company->active_locks_count }} verrouillage(s) actif(s)
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <div class="flex items-center gap-2">
                                        @if($company->active_locks_count > 0)
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">
                                                {{ $company->active_locks_count }} verrouillé(s)
                                            </span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                Aucun verrouillage
                                            </span>
                                        @endif
                                        
                                        <button wire:click="openLockModalForCompany({{ $company->id }})" 
                                                class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                            Gérer
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Détails des verrouillages -->
                            @php
                                $companyLocks = $this->getCompanyFeatureLocks($company->id);
                            @endphp
                            @if($companyLocks->count() > 0)
                                <div class="px-4 py-3">
                                    <div class="grid grid-cols-1 gap-2 sm:grid-cols-2 lg:grid-cols-3">
                                        @foreach($companyLocks as $lock)
                                            <div class="flex items-center justify-between p-2 bg-red-50 border border-red-200 rounded-md">
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-medium text-red-900 truncate">
                                                        {{ $lock->getFeatureDescription() }}
                                                    </p>
                                                    @if($lock->expires_at)
                                                        <p class="text-xs text-red-700">
                                                            Expire: {{ $lock->expires_at->format('d/m/Y H:i') }}
                                                        </p>
                                                    @endif
                                                </div>
                                                <button wire:click="unlockFeature({{ $company->id }}, '{{ $lock->feature_key }}')"
                                                        class="ml-2 text-red-600 hover:text-red-900">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5V6.75a4.5 4.5 0 1 1 9 0v3.75M3.75 21.75h10.5a2.25 2.25 0 0 0 2.25-2.25v-6.75a2.25 2.25 0 0 0-2.25-2.25H3.75a2.25 2.25 0 0 0-2.25 2.25v6.75a2.25 2.25 0 0 0 2.25 2.25Z" />
                                                    </svg>
                                                </button>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6">
                    {{ $companies->links() }}
                </div>
            @else
                <div class="text-center py-6">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune entreprise trouvée</h3>
                    <p class="mt-1 text-sm text-gray-500">Modifiez vos critères de recherche.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Activité récente -->
    <div class="bg-white shadow-sm rounded-lg border border-gray-200">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">Activité Récente</h3>
            
            @if($recentLocks->count() > 0)
                <div class="flow-root">
                    <ul role="list" class="-mb-8">
                        @foreach($recentLocks as $lock)
                            <li>
                                <div class="relative pb-8">
                                    @if(!$loop->last)
                                        <span class="absolute top-4 left-4 -ml-px h-full w-0.5 bg-gray-200" aria-hidden="true"></span>
                                    @endif
                                    <div class="relative flex space-x-3">
                                        <div>
                                            <span class="h-8 w-8 rounded-full bg-red-500 flex items-center justify-center ring-8 ring-white">
                                                <svg class="h-4 w-4 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25z" />
                                                </svg>
                                            </span>
                                        </div>
                                        <div class="min-w-0 flex-1">
                                            <div>
                                                <div class="text-sm">
                                                    <span class="font-medium text-gray-900">{{ $lock->getFeatureDescription() }}</span>
                                                    verrouillée pour
                                                    <span class="font-medium text-gray-900">{{ $lock->company->name }}</span>
                                                </div>
                                                <p class="mt-0.5 text-sm text-gray-500">
                                                    Par {{ $lock->lockedBy?->name ?? 'Système' }} • {{ $lock->created_at->diffForHumans() }}
                                                </p>
                                            </div>
                                            @if($lock->reason)
                                                <div class="mt-2 text-sm text-gray-700">
                                                    <p>{{ $lock->reason }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @else
                <p class="text-sm text-gray-500 text-center py-4">Aucune activité récente.</p>
            @endif
        </div>
    </div>

    <!-- Modal de verrouillage -->
    @if($showLockModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModals"></div>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full sm:p-6">
                    <div class="absolute top-0 right-0 pt-4 pr-4">
                        <button wire:click="closeModals" type="button" class="bg-white rounded-md text-gray-400 hover:text-gray-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                @if(!$bulkMode && count($selectedCompanies) === 1)
                                    @php
                                        $selectedCompany = $this->getAllCompanies()->find($selectedCompanies[0]);
                                    @endphp
                                    Gérer les fonctionnalités - {{ $selectedCompany?->name }}
                                @else
                                    Verrouiller des Fonctionnalités
                                @endif
                            </h3>
                            <div class="mt-4 space-y-4">
                                <!-- Sélection des entreprises -->
                                @if($bulkMode || count($selectedCompanies) !== 1)
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Entreprises</label>
                                        <div class="mt-2 max-h-32 overflow-y-auto border border-gray-300 rounded-md p-2">
                                            @foreach($this->getAllCompanies() as $company)
                                                <label class="flex items-center py-1">
                                                    <input wire:model="selectedCompanies" type="checkbox" value="{{ $company->id }}" 
                                                           class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                    <span class="ml-2 text-sm text-gray-700">{{ $company->name }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('selectedCompanies') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                    </div>
                                @else
                                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3">
                                        <div class="flex items-center">
                                            <svg class="h-5 w-5 text-blue-600 mr-2" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75" />
                                            </svg>
                                            <span class="text-sm font-medium text-blue-800">
                                                Entreprise sélectionnée : {{ $selectedCompany?->name }}
                                            </span>
                                        </div>
                                    </div>
                                @endif

                                <!-- Sélection des fonctionnalités -->
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Fonctionnalités</label>
                                    <div class="mt-2 max-h-40 overflow-y-auto border border-gray-300 rounded-md p-2 space-y-2">
                                        @foreach($featureCategories as $category => $features)
                                            <div>
                                                <h4 class="text-xs font-medium text-gray-500 uppercase tracking-wide">{{ $category }}</h4>
                                                <div class="mt-1 space-y-1">
                                                    @foreach($features as $feature)
                                                        <label class="flex items-center">
                                                            <input wire:model="selectedFeatures" type="checkbox" value="{{ $feature['key'] }}" 
                                                                   class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                                            <span class="ml-2 text-sm text-gray-700">{{ $feature['description'] }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                    @error('selectedFeatures') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Raison du verrouillage -->
                                <div>
                                    <label for="lock-reason" class="block text-sm font-medium text-gray-700">Raison du verrouillage</label>
                                    <textarea wire:model="lockReason" id="lock-reason" rows="3"
                                              class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                              placeholder="Expliquez pourquoi ces fonctionnalités sont verrouillées..."></textarea>
                                    @error('lockReason') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>

                                <!-- Date d'expiration -->
                                <div>
                                    <label for="expires-at" class="block text-sm font-medium text-gray-700">Expiration (optionnel)</label>
                                    <input wire:model="expiresAt" type="datetime-local" id="expires-at"
                                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                    @error('expiresAt') <p class="mt-1 text-sm text-red-600">{{ $message }}</p> @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:flex sm:flex-row-reverse">
                        <button wire:click="lockFeatures" 
                                wire:loading.attr="disabled"
                                type="button" 
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                            <span wire:loading.remove>Verrouiller</span>
                            <span wire:loading>Verrouillage...</span>
                        </button>
                        <button wire:click="closeModals" 
                                type="button" 
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Notifications Livewire -->
    <livewire:notifications />
</div>

@script
<script>
    // Auto-refresh des données toutes les 30 secondes
    setInterval(() => {
        $wire.dispatch('refresh-locks');
    }, 30000);
</script>
@endscript