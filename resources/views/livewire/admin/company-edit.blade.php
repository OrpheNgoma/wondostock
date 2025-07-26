<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier l'Entreprise</h1>
            <p class="text-gray-600">Modifier les informations de "{{ $company->name }}"</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.companies.show', $company) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Voir détails
            </a>
            <a href="{{ route('admin.companies.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
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
            <!-- Informations de l'entreprise -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de l'entreprise</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom de l'entreprise -->
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom de l'entreprise *
                        </label>
                        <input type="text" 
                               id="name"
                               wire:model="name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nom commercial">
                        @error('name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Raison sociale -->
                    <div>
                        <label for="legal_name" class="block text-sm font-medium text-gray-700 mb-2">
                            Raison sociale
                        </label>
                        <input type="text" 
                               id="legal_name"
                               wire:model="legal_name" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Raison sociale officielle">
                        @error('legal_name') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Email de l'entreprise -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email de l'entreprise *
                        </label>
                        <input type="email" 
                               id="email"
                               wire:model="email" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="contact@entreprise.com">
                        @error('email') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Téléphone -->
                    <div>
                        <label for="phone_number" class="block text-sm font-medium text-gray-700 mb-2">
                            Téléphone
                        </label>
                        <input type="text" 
                               id="phone_number"
                               wire:model="phone_number" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="+33 1 23 45 67 89">
                        @error('phone_number') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- RCCM -->
                    <div>
                        <label for="rccm" class="block text-sm font-medium text-gray-700 mb-2">
                            RCCM
                        </label>
                        <input type="text" 
                               id="rccm"
                               wire:model="rccm" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Numéro RCCM">
                        @error('rccm') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- NIF -->
                    <div>
                        <label for="nif" class="block text-sm font-medium text-gray-700 mb-2">
                            NIF
                        </label>
                        <input type="text" 
                               id="nif"
                               wire:model="nif" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Numéro d'identification fiscale">
                        @error('nif') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>

                <!-- Adresse -->
                <div class="mt-6">
                    <label for="address" class="block text-sm font-medium text-gray-700 mb-2">
                        Adresse
                    </label>
                    <textarea id="address"
                              wire:model="address" 
                              rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                              placeholder="Adresse complète de l'entreprise..."></textarea>
                    @error('address') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Statut -->
                <div class="mt-6">
                    <label class="flex items-center">
                        <input type="checkbox" 
                               wire:model="is_active"
                               class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                        <span class="ml-2 text-sm font-medium text-gray-700">Entreprise active</span>
                    </label>
                </div>
            </div>

            <!-- Informations sur le propriétaire actuel -->
            @if($company->owner)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Propriétaire actuel</h3>
                    <div class="bg-gray-50 p-4 rounded-md">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-700">Nom :</span>
                                <span class="text-sm text-gray-900 ml-2">{{ $company->owner->name }}</span>
                            </div>
                            <div>
                                <span class="text-sm font-medium text-gray-700">Email :</span>
                                <span class="text-sm text-gray-900 ml-2">{{ $company->owner->email }}</span>
                            </div>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">
                            <svg class="w-4 h-4 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            Pour modifier les informations du propriétaire, utilisez la page de détails de l'entreprise.
                        </p>
                    </div>
                </div>
            @endif

            <!-- Statistiques de l'entreprise -->
            @if($company->exists)
                <div class="mt-8 pt-6 border-t border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Statistiques</h3>
                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                        <div class="bg-blue-50 p-3 rounded-md text-center">
                            <div class="text-2xl font-bold text-blue-600">{{ $company->users()->count() }}</div>
                            <div class="text-xs text-blue-600">Utilisateurs</div>
                        </div>
                        <div class="bg-green-50 p-3 rounded-md text-center">
                            <div class="text-2xl font-bold text-green-600">{{ $company->products()->count() }}</div>
                            <div class="text-xs text-green-600">Produits</div>
                        </div>
                        <div class="bg-purple-50 p-3 rounded-md text-center">
                            <div class="text-2xl font-bold text-purple-600">{{ $company->documents()->count() }}</div>
                            <div class="text-xs text-purple-600">Documents</div>
                        </div>
                        <div class="bg-orange-50 p-3 rounded-md text-center">
                            <div class="text-2xl font-bold text-orange-600">{{ $company->stores()->count() }}</div>
                            <div class="text-xs text-orange-600">Magasins</div>
                        </div>
                        <div class="bg-gray-50 p-3 rounded-md text-center">
                            <div class="text-2xl font-bold text-gray-600">{{ $company->customers()->count() }}</div>
                            <div class="text-xs text-gray-600">Clients</div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.companies.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>