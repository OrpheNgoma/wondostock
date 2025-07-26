<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouvelle Entreprise</h1>
            <p class="text-gray-600">Créer une nouvelle entreprise cliente</p>
        </div>
        <a href="{{ route('admin.companies.index') }}" 
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
            <!-- Informations de l'entreprise -->
            <div class="border-b border-gray-200 pb-6 mb-6">
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

            <!-- Informations du propriétaire -->
            <div>
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations du propriétaire</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nom du propriétaire -->
                    <div>
                        <label for="ownerName" class="block text-sm font-medium text-gray-700 mb-2">
                            Nom complet *
                        </label>
                        <input type="text" 
                               id="ownerName"
                               wire:model="ownerName" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Nom et prénom du propriétaire">
                        @error('ownerName') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Email du propriétaire -->
                    <div>
                        <label for="ownerEmail" class="block text-sm font-medium text-gray-700 mb-2">
                            Email *
                        </label>
                        <input type="email" 
                               id="ownerEmail"
                               wire:model="ownerEmail" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="proprietaire@entreprise.com">
                        @error('ownerEmail') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Mot de passe -->
                    <div>
                        <label for="ownerPassword" class="block text-sm font-medium text-gray-700 mb-2">
                            Mot de passe *
                        </label>
                        <input type="password" 
                               id="ownerPassword"
                               wire:model="ownerPassword" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Minimum 8 caractères">
                        @error('ownerPassword') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>

                    <!-- Confirmation du mot de passe -->
                    <div>
                        <label for="ownerPasswordConfirmation" class="block text-sm font-medium text-gray-700 mb-2">
                            Confirmer le mot de passe *
                        </label>
                        <input type="password" 
                               id="ownerPasswordConfirmation"
                               wire:model="ownerPasswordConfirmation" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="Confirmer le mot de passe">
                        @error('ownerPasswordConfirmation') 
                            <span class="text-red-500 text-sm">{{ $message }}</span> 
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.companies.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">
                    Créer l'entreprise
                </button>
            </div>
        </form>
    </div>
</div>