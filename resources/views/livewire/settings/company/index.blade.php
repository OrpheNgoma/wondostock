<div class="space-y-6">
    <!-- En-tête moderne sobre -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Paramètres de l'Entreprise
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Mettez à jour les informations légales et de contact de votre entreprise
                </p>
            </div>
        </div>
    </div>

    <form wire:submit.prevent="save" class="mx-6">

        <!-- Messages de session -->
        @if (session('success'))
            <div class="rounded-lg bg-green-50 p-4 border border-green-200">
                <div class="flex items-center gap-3">
                    <div class="h-5 w-5 text-green-600">
                        <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <p class="text-sm font-medium text-green-800">{{ session('success') }}</p>
                </div>
            </div>
        @endif

        <!-- Informations Générales -->
        <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informations Générales</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="name" class="block text-xs font-medium text-gray-700 mb-2">Nom commercial *</label>
                    <input type="text" wire:model="name" id="name" 
                           placeholder="Nom affiché publiquement (ex: Mon Entreprise)"
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                    @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>
                <div>
                    <label for="legal_name" class="block text-xs font-medium text-gray-700 mb-2">Raison sociale</label>
                    <input type="text" wire:model="legal_name" id="legal_name" 
                           placeholder="Raison sociale officielle"
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
                <div>
                    <label for="email" class="block text-xs font-medium text-gray-700 mb-2">Email de contact</label>
                    <input type="email" wire:model="email" id="email" 
                           placeholder="contact@monentreprise.com"
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
                <div>
                    <label for="phone_number" class="block text-xs font-medium text-gray-700 mb-2">Téléphone</label>
                    <input type="text" wire:model="phone_number" id="phone_number" 
                           placeholder="+33 1 23 45 67 89"
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
                <div class="sm:col-span-2">
                    <label for="address" class="block text-xs font-medium text-gray-700 mb-2">Adresse complète</label>
                    <textarea wire:model="address" id="address" rows="3" 
                              placeholder="123 Rue de la République, 75001 Paris, France"
                              class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200"></textarea>
                </div>
            </div>
        </div>

        <!-- Informations Légales & Logo -->
        <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 mb-6">Informations Légales & Logo</h3>
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                <div>
                    <label for="rccm" class="block text-xs font-medium text-gray-700 mb-2">N° RCCM</label>
                    <input type="text" wire:model="rccm" id="rccm" 
                           placeholder="Numéro de registre du commerce (ex: CD/KGA/RCCM/23-A-12345)"
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
                <div>
                    <label for="nif" class="block text-xs font-medium text-gray-700 mb-2">NIF</label>
                    <input type="text" wire:model="nif" id="nif" 
                           placeholder="Numéro d'identification fiscale (ex: A23456789)"
                           class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                </div>
                <div class="sm:col-span-2">
                    <label for="logo" class="block text-xs font-medium text-gray-700 mb-2">Logo de l'entreprise</label>
                    <div class="flex items-center gap-4">
                        {{-- On vérifie que le fichier est bien une image avant de l'afficher --}}
                        @if ($logo && Str::startsWith($logo->getMimeType(), 'image/'))
                            <img src="{{ $logo->temporaryUrl() }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                        @elseif ($company->hasMedia('logo'))
                            <img src="{{ $company->getFirstMediaUrl('logo') }}" class="h-16 w-16 rounded-lg object-cover border border-gray-200 shadow-sm">
                        @else
                            <div class="h-16 w-16 rounded-lg bg-gray-100 flex items-center justify-center border border-gray-200">
                                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" />
                                </svg>
                            </div>
                        @endif
                        <div class="flex-1">
                            <input type="file" wire:model="logo" id="logo" class="hidden" accept="image/*">
                            <label for="logo" class="cursor-pointer inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                </svg>
                                Télécharger un logo
                            </label>
                            <p class="text-xs text-gray-500 mt-1">PNG, JPG jusqu'à 2MB - Taille recommandée: 200x200px</p>
                        </div>
                    </div>
                    @error('logo')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        <!-- Boutons d'action -->
        <div class="flex items-center justify-end gap-3 pt-6 border-t border-gray-200">
            <button type="submit" 
                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                <span wire:loading.remove wire:target="save">Sauvegarder les changements</span>
                <span wire:loading wire:target="save">Sauvegarde en cours...</span>
            </button>
        </div>
    </form>
</div>