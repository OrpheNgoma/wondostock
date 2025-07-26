<div>
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900">Mon Abonnement</h2>
            <p class="mt-1 text-sm text-gray-500">Consultez votre forfait actuel et découvrez nos autres offres.</p>
        </div>
    </div>

    @if($subscription)
        <!-- Informations abonnement actuel -->
        <div class="mt-6 overflow-hidden rounded-lg bg-white shadow">
            <div class="px-4 py-5 sm:p-6">
                <div class="sm:flex sm:items-center sm:justify-between">
                    <div>
                        <h3 class="text-lg font-medium leading-6 text-gray-900">Abonnement actuel</h3>
                        <div class="mt-2 max-w-xl text-sm text-gray-500">
                            <p>Vous êtes actuellement sur le plan <strong>{{ $currentPlan->name }}</strong></p>
                            @if($subscription->starts_at)
                                <p class="mt-1">Actif depuis le {{ $subscription->starts_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="mt-3 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                        <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium
                            @if($this->subscriptionStatus === 'Actif') bg-green-100 text-green-800
                            @elseif($this->subscriptionStatus === 'Expire bientôt') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800 @endif">
                            {{ $this->subscriptionStatus }}
                        </span>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Grille des plans -->
    <div class="mt-10 space-y-12 lg:grid lg:grid-cols-3 lg:gap-x-8 lg:space-y-0">
        @if($plans)
            @foreach($plans as $plan)
                <div class="relative flex flex-col rounded-2xl border {{ $currentPlan && $currentPlan->id === $plan->id ? 'border-indigo-600 ring-2 ring-indigo-200' : 'border-gray-200' }} bg-white p-8 shadow-sm hover:shadow-md transition-shadow">
                    <div class="flex-1">
                        <h3 class="text-xl font-semibold text-gray-900">{{ $plan->name }}</h3>
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <p class="absolute top-0 -translate-y-1/2 transform rounded-full bg-indigo-600 py-1.5 px-4 text-sm font-semibold text-white">
                                Plan Actuel
                            </p>
                        @endif
                        
                        @if(isset($this->planDetails[$plan->name]))
                            <p class="mt-2 text-sm text-gray-500">{{ $this->planDetails[$plan->name]['description'] }}</p>
                            <p class="mt-1 text-xs text-gray-400">{{ $this->planDetails[$plan->name]['target'] }}</p>
                            
                            <div class="mt-4 space-y-2">
                                @if($this->planDetails[$plan->name]['monthly_price'] !== 'Sur Devis')
                                    <!-- Prix mensuel -->
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-gray-900">{{ number_format($this->planDetails[$plan->name]['monthly_price']) }}</span>
                                        <span class="ml-1 text-sm text-gray-500">{{ $this->planDetails[$plan->name]['currency'] }}/mois</span>
                                    </div>
                                    
                                    <!-- Options trimestrielle et annuelle -->
                                    <div class="space-y-1 text-xs text-gray-600">
                                        @if($this->planDetails[$plan->name]['quarterly_price'])
                                            <div>Trimestriel: <span class="font-medium">{{ number_format($this->planDetails[$plan->name]['quarterly_price']) }} {{ $this->planDetails[$plan->name]['currency'] }}</span></div>
                                        @endif
                                        @if($this->planDetails[$plan->name]['yearly_price'])
                                            <div class="flex items-center">
                                                <span>Annuel: <span class="font-medium">{{ number_format($this->planDetails[$plan->name]['yearly_price']) }} {{ $this->planDetails[$plan->name]['currency'] }}</span></span>
                                                @if($this->planDetails[$plan->name]['yearly_note'])
                                                    <span class="ml-2 inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">{{ $this->planDetails[$plan->name]['yearly_note'] }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <!-- Plan sur devis -->
                                    <div class="flex items-baseline">
                                        <span class="text-2xl font-bold text-gray-900">{{ $this->planDetails[$plan->name]['monthly_price'] }}</span>
                                    </div>
                                    <p class="text-xs text-gray-500">Contactez-nous pour un devis personnalisé</p>
                                @endif
                            </div>
                        @endif
                        
                        @if($plan->features && is_array($plan->features))
                            <ul role="list" class="mt-6 space-y-4">
                                @foreach($plan->features as $feature)
                                <li class="flex space-x-3">
                                    <svg class="h-5 w-5 flex-shrink-0 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <span class="text-sm text-gray-600">
                                        {{ $this->planFeatures[$feature] ?? ucfirst(str_replace('_', ' ', $feature)) }}
                                    </span>
                                </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="mt-6 text-sm text-gray-500">
                                <p>Aucune fonctionnalité définie pour ce plan.</p>
                            </div>
                        @endif
                    </div>

                    <div class="mt-8">
                        @if($currentPlan && $currentPlan->id === $plan->id)
                            <div class="rounded-lg border border-gray-200 bg-gray-50 py-3 px-4 text-center text-sm font-medium text-gray-700">
                                <svg class="mx-auto h-5 w-5 text-green-500 mb-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Votre plan actuel
                            </div>
                        @else
                            <button 
                                wire:click="requestPlanChange({{ $plan->id }})" 
                                class="w-full rounded-lg bg-indigo-600 px-6 py-3 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors"
                                wire:loading.attr="disabled"
                                wire:target="requestPlanChange({{ $plan->id }})"
                            >
                                <span wire:loading.remove wire:target="requestPlanChange({{ $plan->id }})">
                                    Demander ce plan
                                </span>
                                <span wire:loading wire:target="requestPlanChange({{ $plan->id }})" class="flex items-center justify-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Traitement...
                                </span>
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <div class="col-span-3 text-center py-12">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun plan disponible</h3>
                <p class="mt-1 text-sm text-gray-500">Les plans d'abonnement ne sont pas encore configurés.</p>
            </div>
        @endif
    </div>

    <!-- Service d'onboarding -->
    <div class="mt-10 overflow-hidden rounded-lg bg-gradient-to-r from-purple-50 to-indigo-50 border border-purple-200">
        <div class="px-6 py-8">
            <div class="sm:flex sm:items-start sm:justify-between">
                <div class="flex-1">
                    <div class="flex items-center">
                        <div class="flex-shrink-0">
                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.436 60.436 0 00-.491 6.347A48.627 48.627 0 0112 20.904a48.627 48.627 0 018.232-4.41 60.46 60.46 0 00-.491-6.347m-15.482 0a50.57 50.57 0 00-2.658-.813A59.905 59.905 0 0112 3.493a59.902 59.902 0 0110.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.697 50.697 0 0112 13.489a50.702 50.702 0 017.74-3.342M6.75 15a.75.75 0 100-1.5.75.75 0 000 1.5zm0 0v-3.675A55.378 55.378 0 0112 8.443m-7.007 11.55A5.981 5.981 0 006.75 15.75v-1.5" />
                            </svg>
                        </div>
                        <div class="ml-4">
                            <h3 class="text-lg font-semibold text-gray-900">Service d'Onboarding Optionnel</h3>
                            <p class="mt-1 text-sm text-gray-600">{{ $this->onboardingInfo['description'] }}</p>
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <ul class="space-y-2">
                            @foreach($this->onboardingInfo['includes'] as $include)
                                <li class="flex items-center text-sm text-gray-700">
                                    <svg class="h-4 w-4 text-green-500 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $include }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
                
                <div class="mt-6 sm:mt-0 sm:ml-6 sm:flex-shrink-0">
                    <div class="text-center">
                        <div class="text-2xl font-bold text-gray-900">{{ number_format($this->onboardingInfo['price']) }}</div>
                        <div class="text-sm text-gray-500">{{ $this->onboardingInfo['currency'] }} (forfait unique)</div>
                        <div class="mt-4">
                            <button 
                                wire:click="requestOnboardingQuote" 
                                class="inline-flex items-center rounded-md bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 transition-colors"
                                wire:loading.attr="disabled"
                                wire:target="requestOnboardingQuote"
                            >
                                <span wire:loading.remove wire:target="requestOnboardingQuote" class="flex items-center">
                                    <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                    </svg>
                                    Demander un devis
                                </span>
                                <span wire:loading wire:target="requestOnboardingQuote" class="flex items-center">
                                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Envoi en cours...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Section d'aide -->
    <div class="mt-10 rounded-lg bg-blue-50 p-4">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="h-5 w-5 text-blue-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                </svg>
            </div>
            <div class="ml-3">
                <h3 class="text-sm font-medium text-blue-800">Besoin d'aide ?</h3>
                <div class="mt-2 text-sm text-blue-700">
                    <p>Si vous avez des questions sur les plans ou si vous souhaitez une démonstration personnalisée, n'hésitez pas à nous contacter.</p>
                    <p class="mt-1 text-xs">📞 Libreville, Gabon • Support disponible du lundi au vendredi</p>
                </div>
            </div>
        </div>
    </div>
</div>