<div class="space-y-6">
    <!-- En-tête avec statistiques -->
    <div class="bg-white border-b border-gray-200 px-6 py-8 rounded-lg shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Gestion des Plans d'Abonnement
                </h1>
                <p class="mt-2 text-lg text-gray-600">
                    Configuration des plans tarifaires pour les entreprises clientes
                </p>
            </div>
            <div>
                <a href="{{ route('admin.plans.create') }}" 
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-3 text-sm font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nouveau Plan
                </a>
            </div>
        </div>

        <!-- Statistiques rapides -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-lg p-4 border border-blue-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-blue-600">Total Plans</p>
                        <p class="text-2xl font-bold text-blue-900">{{ number_format($this->stats['total_plans']) }}</p>
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
                        <p class="text-sm font-medium text-green-600">Abonnements Actifs</p>
                        <p class="text-2xl font-bold text-green-900">{{ number_format($this->stats['total_subscriptions']) }}</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-green-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-lg p-4 border border-yellow-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-yellow-600">Revenus Mensuels</p>
                        <p class="text-2xl font-bold text-yellow-900">{{ number_format($this->stats['monthly_revenue'], 2) }}€</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-yellow-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-lg p-4 border border-purple-200">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-purple-600">Prix Moyen</p>
                        <p class="text-2xl font-bold text-purple-900">{{ number_format($this->stats['average_price'], 2) }}€</p>
                    </div>
                    <div class="h-10 w-10 rounded-full bg-purple-200 flex items-center justify-center">
                        <svg class="h-5 w-5 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 7.996 21 8.625 21h6.75c.621 0 1.125-.504 1.125-1.125v-6.75c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v6.75C21 20.496 20.496 21 19.875 21H4.125C3.504 21 3 20.496 3 19.875v-6.75z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 10.5V9a6 6 0 1112 0v1.5" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Barre de recherche -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <div class="flex flex-col sm:flex-row gap-4">
            <div class="flex-1 relative">
                <input 
                    type="text" 
                    wire:model.live.debounce.300ms="search"
                    placeholder="Rechercher un plan..."
                    class="block w-full rounded-lg border-gray-300 py-3 pl-10 pr-3 text-sm focus:border-red-500 focus:ring-red-500"
                >
                <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Liste des plans -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
        <!-- En-tête du tableau -->
        <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Plans d'Abonnement</h3>
                    <p class="text-sm text-gray-600 mt-1">{{ $plans->total() }} plan(s) configuré(s)</p>
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
                                Plan
                                @if($sortBy === 'name')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left">
                            <button wire:click="sortBy('price')" class="group inline-flex items-center gap-1 text-xs font-semibold text-gray-700 uppercase tracking-wide hover:text-gray-900">
                                Prix
                                @if($sortBy === 'price')
                                    <svg class="h-3 w-3 {{ $sortDirection === 'asc' ? 'rotate-180' : '' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                    </svg>
                                @endif
                            </button>
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Utilisateurs
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Fonctionnalités
                        </th>
                        <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Abonnements
                        </th>
                        <th scope="col" class="relative py-3 pl-3 pr-6 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200 bg-white">
                    @forelse ($plans as $plan)
                        <tr wire:key="{{ $plan->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                            <td class="py-6 pl-6 pr-3">
                                <div>
                                    <div class="flex items-center gap-3">
                                        @php
                                            $planConfig = match($plan->slug) {
                                                'essentiel' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600'],
                                                'pro' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600'],
                                                'entreprise' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-600'],
                                                default => ['bg' => 'bg-gray-500', 'text' => 'text-gray-600'],
                                            };
                                        @endphp
                                        <div class="h-3 w-3 rounded-full {{ $planConfig['bg'] }}"></div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $plan->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $plan->slug }}</p>
                                        </div>
                                    </div>
                                    <p class="text-xs text-gray-600 mt-2 max-w-xs">{{ $plan->description }}</p>
                                </div>
                            </td>
                            <td class="px-3 py-6">
                                <div class="text-lg font-bold text-gray-900">{{ number_format($plan->price, 2) }}€</div>
                                <div class="text-xs text-gray-500">par mois</div>
                            </td>
                            <td class="px-3 py-6">
                                @if($plan->unlimited_users)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-green-600/20">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Illimité
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-blue-100 px-2 py-1 text-xs font-medium text-blue-800 ring-1 ring-blue-600/20">
                                        {{ $plan->user_limit }} utilisateur(s)
                                    </span>
                                @endif
                            </td>
                            <td class="px-3 py-6">
                                @php $planFeatures = $plan->getFeaturesArray(); @endphp
                                <div class="flex flex-wrap gap-1">
                                    @foreach(array_slice($planFeatures, 0, 3) as $feature)
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800">
                                            {{ $availableFeatures[$feature] ?? $feature }}
                                        </span>
                                    @endforeach
                                    @if(count($planFeatures) > 3)
                                        <span class="text-xs text-gray-500">+{{ count($planFeatures) - 3 }} autres</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-6">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $plan->subscriptions_count }} abonnement(s)
                                </div>
                                <div class="text-xs text-gray-500">
                                    {{ number_format($plan->subscriptions_count * $plan->price, 2) }}€/mois
                                </div>
                            </td>
                            <td class="relative py-6 pl-3 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <!-- Bouton Modifier -->
                                    <a href="{{ route('admin.plans.edit', $plan) }}" 
                                            class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Modifier
                                    </a>

                                    <!-- Bouton Supprimer -->
                                    <button wire:click="deletePlan({{ $plan->id }})"
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce plan ? Cette action est irréversible."
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
                                        <p class="text-lg font-medium text-gray-900">Aucun plan trouvé</p>
                                        <p class="text-sm text-gray-500 mt-1">Créez votre premier plan d'abonnement</p>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($plans->hasPages())
            <div class="border-t border-gray-200 bg-white px-6 py-4">
                {{ $plans->links() }}
            </div>
        @endif
    </div>

    <!-- Modal de création/édition -->
    @if($showModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <form wire:submit="savePlan">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        {{ $editingPlan ? 'Modifier le Plan' : 'Nouveau Plan' }}
                                    </h3>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <!-- Nom -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nom du plan</label>
                                            <input type="text" wire:model="name" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                            @error('name') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Slug -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Slug</label>
                                            <input type="text" wire:model="slug" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                            @error('slug') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Prix -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Prix (€/mois)</label>
                                            <input type="number" step="0.01" wire:model="price" 
                                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                            @error('price') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>

                                        <!-- Limite utilisateurs -->
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Limite utilisateurs</label>
                                            <div class="mt-1 space-y-2">
                                                <div class="flex items-center">
                                                    <input type="checkbox" wire:model.live="unlimited_users" id="unlimited_users"
                                                           class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                                                    <label for="unlimited_users" class="ml-2 block text-sm text-gray-900">
                                                        Utilisateurs illimités
                                                    </label>
                                                </div>
                                                @unless($unlimited_users)
                                                    <input type="number" wire:model="user_limit" min="1"
                                                           class="block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                                @endunless
                                            </div>
                                            @error('user_limit') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                        </div>
                                    </div>

                                    <!-- Description -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700">Description</label>
                                        <textarea wire:model="description" rows="3"
                                                  class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm"></textarea>
                                        @error('description') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                    </div>

                                    <!-- Fonctionnalités -->
                                    <div class="mt-4">
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Fonctionnalités</label>
                                        
                                        <!-- Ajouter une fonctionnalité -->
                                        <div class="flex gap-2 mb-3">
                                            <select wire:model="newFeature" class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-red-500 focus:ring-red-500 sm:text-sm">
                                                <option value="">Sélectionner une fonctionnalité</option>
                                                @foreach($availableFeatures as $key => $label)
                                                    @unless(in_array($key, $features))
                                                        <option value="{{ $key }}">{{ $label }}</option>
                                                    @endunless
                                                @endforeach
                                            </select>
                                            <button type="button" wire:click="addFeature" 
                                                    class="px-3 py-2 bg-red-600 text-white text-sm rounded-md hover:bg-red-700">
                                                Ajouter
                                            </button>
                                        </div>

                                        <!-- Liste des fonctionnalités -->
                                        <div class="space-y-2 max-h-32 overflow-y-auto">
                                            @foreach($features as $index => $feature)
                                                <div class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-md">
                                                    <span class="text-sm text-gray-700">{{ $availableFeatures[$feature] ?? $feature }}</span>
                                                    <button type="button" wire:click="removeFeature({{ $index }})" 
                                                            class="text-red-600 hover:text-red-700">
                                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                                        </svg>
                                                    </button>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" 
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                                {{ $editingPlan ? 'Mettre à jour' : 'Créer' }}
                            </button>
                            <button type="button" wire:click="closeModal" 
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>