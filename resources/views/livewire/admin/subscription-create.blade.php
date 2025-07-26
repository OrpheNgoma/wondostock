<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvel Abonnement</h1>
            <p class="text-gray-600">Créer un nouvel abonnement pour une entreprise</p>
        </div>
        <a href="{{ route('admin.subscriptions.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm">
        <form wire:submit.prevent="save" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Entreprise -->
                <div>
                    <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Entreprise *
                    </label>
                    <select id="company_id" 
                            wire:model="company_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner une entreprise</option>
                        @foreach ($companies as $company)
                            <option value="{{ $company->id }}">
                                {{ $company->name }}
                                @if ($company->email)
                                    ({{ $company->email }})
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('company_id') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Plan -->
                <div>
                    <label for="plan_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Plan d'abonnement *
                    </label>
                    <select id="plan_id" 
                            wire:model.live="plan_id"
                            class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="">Sélectionner un plan</option>
                        @foreach ($plans as $plan)
                            <option value="{{ $plan->id }}">
                                {{ $plan->name }} - {{ number_format($plan->price, 2) }}€
                                @if ($plan->unlimited_users)
                                    (Utilisateurs illimités)
                                @else
                                    (Max {{ $plan->user_limit }} utilisateurs)
                                @endif
                            </option>
                        @endforeach
                    </select>
                    @error('plan_id') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Date de début -->
                <div>
                    <label for="starts_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de début *
                    </label>
                    <input type="date" 
                           id="starts_at"
                           wire:model.live="starts_at" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('starts_at') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Date de fin -->
                <div>
                    <label for="ends_at" class="block text-sm font-medium text-gray-700 mb-2">
                        Date de fin *
                    </label>
                    <input type="date" 
                           id="ends_at"
                           wire:model="ends_at" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                    @error('ends_at') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Statut -->
                <div class="md:col-span-2">
                    <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                        Statut *
                    </label>
                    <select id="status" 
                            wire:model="status"
                            class="w-full md:w-1/2 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                        <option value="active">Actif</option>
                        <option value="inactive">Inactif</option>
                        <option value="cancelled">Annulé</option>
                    </select>
                    @error('status') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>
            </div>

            <!-- Aperçu du plan sélectionné -->
            @if ($plan_id)
                @php
                    $selectedPlan = $plans->find($plan_id);
                @endphp
                @if ($selectedPlan)
                    <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-md">
                        <h4 class="text-sm font-medium text-blue-900 mb-2">Aperçu du plan sélectionné</h4>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="font-medium text-blue-800">Prix :</span>
                                <span class="text-blue-900">{{ number_format($selectedPlan->price, 2) }}€/mois</span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-800">Utilisateurs :</span>
                                <span class="text-blue-900">
                                    @if ($selectedPlan->unlimited_users)
                                        Illimités
                                    @else
                                        Maximum {{ $selectedPlan->user_limit }}
                                    @endif
                                </span>
                            </div>
                            <div>
                                <span class="font-medium text-blue-800">Fonctionnalités :</span>
                                <span class="text-blue-900">{{ count($selectedPlan->features) }} disponibles</span>
                            </div>
                        </div>
                        
                        @if ($selectedPlan->description)
                            <p class="text-sm text-blue-800 mt-2">{{ $selectedPlan->description }}</p>
                        @endif

                        @if (!empty($selectedPlan->features))
                            <div class="mt-3">
                                <span class="text-xs font-medium text-blue-800">Fonctionnalités incluses :</span>
                                <div class="flex flex-wrap gap-1 mt-1">
                                    @foreach ($selectedPlan->features as $feature)
                                        <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium bg-blue-100 text-blue-800 ring-1 ring-blue-600/20">
                                            {{ $feature }}
                                        </span>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @endif
            @endif

            <!-- Avertissement pour abonnement existant -->
            @if ($company_id)
                @php
                    $existingSubscription = \App\Models\Subscription::where('company_id', $company_id)
                        ->where('status', 'active')
                        ->with('plan')
                        ->first();
                @endphp
                @if ($existingSubscription)
                    <div class="mt-6 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                        <div class="flex">
                            <svg class="w-5 h-5 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z"/>
                            </svg>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">Abonnement existant détecté</h3>
                                <p class="text-sm text-yellow-700 mt-1">
                                    Cette entreprise a déjà un abonnement actif au plan "{{ $existingSubscription->plan->name }}" 
                                    (du {{ $existingSubscription->starts_at->format('d/m/Y') }} au {{ $existingSubscription->ends_at->format('d/m/Y') }}).
                                    La création d'un nouvel abonnement annulera automatiquement l'abonnement actuel.
                                </p>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.subscriptions.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">
                    Créer l'abonnement
                </button>
            </div>
        </form>
    </div>
</div>