<div class="space-y-6">
    <!-- En-tête avec informations de l'entreprise -->
    <div class="bg-white border-b border-gray-200 px-6 py-8 rounded-lg shadow-sm">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">
            <div class="flex-1">
                <div class="flex items-center gap-4 mb-4">
                    <a href="{{ route('admin.companies.index') }}" 
                       class="inline-flex items-center gap-2 text-sm text-gray-600 hover:text-gray-900 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5" />
                        </svg>
                        Retour aux entreprises
                    </a>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="h-16 w-16 rounded-full bg-gradient-to-r from-red-500 to-orange-500 flex items-center justify-center text-white text-2xl font-bold">
                        {{ strtoupper(substr($company->name, 0, 2)) }}
                    </div>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900 flex items-center gap-3">
                            {{ $company->name }}
                            @if($company->is_active)
                                <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-sm font-medium text-green-800 ring-1 ring-green-600/20">
                                    <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                    Active
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-sm font-medium text-red-800 ring-1 ring-red-600/20">
                                    <div class="h-1.5 w-1.5 rounded-full bg-red-500"></div>
                                    Inactive
                                </span>
                            @endif
                        </h1>
                        <p class="text-lg text-gray-600 mt-1">{{ $company->legal_name ?: $company->email }}</p>
                    </div>
                </div>

                <!-- Statistiques rapides -->
                <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                    <div class="bg-blue-50 rounded-lg p-3 border border-blue-200">
                        <p class="text-sm font-medium text-blue-600">Utilisateurs</p>
                        <p class="text-xl font-bold text-blue-900">{{ $company->users->count() }}</p>
                    </div>
                    <div class="bg-green-50 rounded-lg p-3 border border-green-200">
                        <p class="text-sm font-medium text-green-600">Magasins</p>
                        <p class="text-xl font-bold text-green-900">{{ $company->stores->count() }}</p>
                    </div>
                    <div class="bg-purple-50 rounded-lg p-3 border border-purple-200">
                        <p class="text-sm font-medium text-purple-600">Plan</p>
                        <div class="text-lg font-bold text-purple-900">
                            @if($company->subscription)
                                {{ $company->subscription->plan->name }}
                            @else
                                <span class="text-gray-500">Aucun</span>
                            @endif
                        </div>
                    </div>
                    <div class="bg-yellow-50 rounded-lg p-3 border border-yellow-200">
                        <p class="text-sm font-medium text-yellow-600">Créée le</p>
                        <p class="text-sm font-bold text-yellow-900">{{ $company->created_at->format('d/m/Y') }}</p>
                    </div>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <button wire:click="openEditForm" 
                        class="inline-flex items-center gap-2 rounded-lg bg-blue-600 px-4 py-3 text-sm font-medium text-white hover:bg-blue-700 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                    </svg>
                    Modifier
                </button>
                
                <button wire:click="toggleCompanyStatus" 
                        class="inline-flex items-center gap-2 rounded-lg px-4 py-3 text-sm font-medium transition-colors {{ $company->is_active ? 'bg-red-600 hover:bg-red-700 text-white' : 'bg-green-600 hover:bg-green-700 text-white' }}">
                    @if($company->is_active)
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 18.364l12.728-12.728" />
                        </svg>
                        Désactiver
                    @else
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Activer
                    @endif
                </button>
            </div>
        </div>
    </div>

    <!-- Onglets de navigation -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="border-b border-gray-200">
            <nav class="flex space-x-8 px-6" aria-label="Tabs">
                <button onclick="showTab('subscription')" 
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-red-500 text-red-600"
                        id="tab-subscription">
                    Abonnement
                </button>
                <button onclick="showTab('users')" 
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        id="tab-users">
                    Utilisateurs
                </button>
                <button onclick="showTab('details')" 
                        class="tab-button whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300"
                        id="tab-details">
                    Détails
                </button>
            </nav>
        </div>

        <!-- Contenu Abonnement -->
        <div id="content-subscription" class="tab-content p-6">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Gestion de l'Abonnement</h3>
                <button wire:click="openSubscriptionForm" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    {{ $company->subscription ? 'Modifier l\'Abonnement' : 'Créer un Abonnement' }}
                </button>
            </div>

            @if($company->subscription)
                <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-lg p-6 border border-green-200">
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div>
                            <h4 class="text-sm font-medium text-green-800 mb-2">Plan Actuel</h4>
                            <p class="text-lg font-bold text-green-900">{{ $company->subscription->plan->name }}</p>
                            <p class="text-sm text-green-700">{{ number_format($company->subscription->plan->price, 2) }}€/mois</p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-green-800 mb-2">Période</h4>
                            <p class="text-sm text-green-900">
                                Du {{ $company->subscription->starts_at?->format('d/m/Y') }}
                                @if($company->subscription->ends_at)
                                    au {{ $company->subscription->ends_at->format('d/m/Y') }}
                                @else
                                    <span class="text-green-600">(Permanent)</span>
                                @endif
                            </p>
                        </div>
                        <div>
                            <h4 class="text-sm font-medium text-green-800 mb-2">Statut</h4>
                            <span class="inline-flex items-center rounded-full px-2 py-1 text-xs font-medium 
                                {{ $company->subscription->status === 'active' ? 'bg-green-200 text-green-800' : 
                                   ($company->subscription->status === 'expired' ? 'bg-yellow-200 text-yellow-800' : 'bg-red-200 text-red-800') }}">
                                {{ ucfirst($company->subscription->status) }}
                            </span>
                        </div>
                    </div>
                    
                    <!-- Fonctionnalités -->
                    <div class="mt-4">
                        <h5 class="text-sm font-medium text-green-800 mb-2">Fonctionnalités incluses:</h5>
                        <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                            @foreach($company->subscription->plan->getFeaturesArray() as $feature)
                                <div class="flex items-center gap-2 text-sm text-green-700">
                                    <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                                    </svg>
                                    <span>{{ $feature }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-12">
                    <div class="h-16 w-16 mx-auto rounded-full bg-gray-100 flex items-center justify-center mb-4">
                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Aucun Abonnement</h3>
                    <p class="text-gray-600">Cette entreprise n'a pas encore d'abonnement actif.</p>
                </div>
            @endif
        </div>

        <!-- Contenu Utilisateurs -->
        <div id="content-users" class="tab-content p-6 hidden">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-lg font-semibold text-gray-900">Gestion des Utilisateurs</h3>
                <button wire:click="openUserForm" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2 text-sm font-medium text-white hover:bg-red-700">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.329 0-4.52-.793-6.374-1.764z" />
                    </svg>
                    Nouvel Utilisateur
                </button>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Utilisateur</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Email</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Rôle</th>
                            <th class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">Créé le</th>
                            <th class="relative py-3 pl-3 pr-6 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($users as $user)
                            <tr wire:key="user-{{ $user->id }}" class="hover:bg-gray-50">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-full bg-blue-500 flex items-center justify-center text-white text-sm font-medium">
                                            {{ strtoupper(substr($user->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                            @if($user->id === $company->owner_id)
                                                <span class="text-xs text-blue-600 font-medium">Propriétaire</span>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $user->email }}</td>
                                <td class="px-3 py-4">
                                    @if($user->roles->count() > 0)
                                        <div class="flex flex-wrap gap-1">
                                            @foreach($user->roles as $role)
                                                <span class="inline-flex items-center rounded-full bg-purple-100 px-2 py-1 text-xs font-medium text-purple-800">
                                                    {{ $role->name }}
                                                </span>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-gray-500">Aucun rôle</span>
                                    @endif
                                </td>
                                <td class="px-3 py-4 text-sm text-gray-900">{{ $user->created_at->format('d/m/Y') }}</td>
                                <td class="relative py-4 pl-3 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="editUser({{ $user->id }})" 
                                                class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-1 text-xs font-medium text-blue-700 hover:bg-blue-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Modifier
                                        </button>
                                        
                                        @if($user->id !== $company->owner_id)
                                            <button wire:click="deleteUser({{ $user->id }})"
                                                    wire:confirm="Êtes-vous sûr de vouloir supprimer cet utilisateur ?"
                                                    class="inline-flex items-center gap-1 rounded-lg bg-red-100 text-red-700 hover:bg-red-200 px-3 py-1 text-xs font-medium">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Supprimer
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-12 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-lg font-medium text-gray-900">Aucun utilisateur</p>
                                            <p class="text-sm text-gray-500 mt-1">Créez le premier utilisateur pour cette entreprise</p>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($users->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
        </div>

        <!-- Contenu Détails -->
        <div id="content-details" class="tab-content p-6 hidden">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informations de l'Entreprise</h3>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nom de l'entreprise</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->name }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Raison sociale</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->legal_name ?: 'Non spécifiée' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Email</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->email }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Téléphone</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->phone_number ?: 'Non spécifié' }}</p>
                    </div>
                </div>
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Adresse</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->address ?: 'Non spécifiée' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">RCCM</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->rccm ?: 'Non spécifié' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">NIF</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->nif ?: 'Non spécifié' }}</p>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Date de création</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $company->created_at->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Abonnement -->
    @if($showSubscriptionForm)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeSubscriptionForm"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveSubscription">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $company->subscription ? 'Modifier l\'Abonnement' : 'Créer un Abonnement' }}
                            </h3>
                            
                            <div class="space-y-4">
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
                                {{ $company->subscription ? 'Mettre à jour' : 'Créer' }}
                            </button>
                            <button type="button" wire:click="closeSubscriptionForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Utilisateur -->
    @if($showUserForm)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeUserForm"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="saveUser">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                {{ $editingUserId ? 'Modifier l\'Utilisateur' : 'Nouvel Utilisateur' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nom</label>
                                    <input type="text" wire:model="userName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('userName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
                                    <input type="email" wire:model="userEmail" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('userEmail') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">
                                        Mot de passe {{ $editingUserId ? '(laisser vide pour ne pas changer)' : '' }}
                                    </label>
                                    <input type="password" wire:model="userPassword" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('userPassword') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingUserId ? 'Mettre à jour' : 'Créer' }}
                            </button>
                            <button type="button" wire:click="closeUserForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Édition Entreprise -->
    @if($showEditForm)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeEditForm"></div>
                
                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit="saveCompany">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Modifier l'Entreprise</h3>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nom de l'entreprise</label>
                                    <input type="text" wire:model="companyName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('companyName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Raison sociale</label>
                                    <input type="text" wire:model="companyLegalName" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500">
                                    @error('companyLegalName') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Email</label>
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
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Mettre à jour
                            </button>
                            <button type="button" wire:click="closeEditForm" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>

<script>
function showTab(tabName) {
    // Masquer tous les contenus
    document.querySelectorAll('.tab-content').forEach(el => el.classList.add('hidden'));
    
    // Réinitialiser tous les boutons
    document.querySelectorAll('.tab-button').forEach(el => {
        el.classList.remove('border-red-500', 'text-red-600');
        el.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Afficher le contenu sélectionné
    document.getElementById('content-' + tabName).classList.remove('hidden');
    
    // Activer le bouton sélectionné
    const activeButton = document.getElementById('tab-' + tabName);
    activeButton.classList.remove('border-transparent', 'text-gray-500');
    activeButton.classList.add('border-red-500', 'text-red-600');
}
</script>