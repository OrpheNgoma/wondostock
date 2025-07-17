<div>
    <div class="sm:flex sm:items-center mb-6">
        <div class="sm:flex-auto">
            <h1 class="text-base font-semibold leading-6 text-gray-900">
                {{ $isEditing ? 'Modifier la branche pays' : 'Créer une nouvelle branche pays' }}
            </h1>
            <p class="mt-2 text-sm text-gray-700">
                {{ $isEditing ? 'Modifiez les informations de cette branche pays.' : 'Créez une nouvelle branche pays avec ses propres informations et templates de facture.' }}
            </p>
        </div>
    </div>

    <form wire:submit="save">
        <div class="space-y-12">
            <!-- Informations générales -->
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Informations générales</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Informations de base de la branche pays.</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom de la branche *</label>
                        <div class="mt-2">
                            <input type="text" wire:model="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">Ville *</label>
                        <div class="mt-2">
                            <input type="text" wire:model="city" id="city" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('city') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="col-span-full">
                        <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Adresse *</label>
                        <div class="mt-2">
                            <textarea wire:model="address" id="address" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6"></textarea>
                        </div>
                        @error('address') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="contact_phone" class="block text-sm font-medium leading-6 text-gray-900">Téléphone *</label>
                        <div class="mt-2">
                            <input type="text" wire:model="contact_phone" id="contact_phone" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('contact_phone') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email</label>
                        <div class="mt-2">
                            <input type="email" wire:model="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('email') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Informations du pays -->
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Informations du pays</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Informations spécifiques au pays d'implantation.</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="country_code" class="block text-sm font-medium leading-6 text-gray-900">Code pays *</label>
                        <div class="mt-2">
                            <select wire:model="country_code" id="country_code" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                                <option value="">Sélectionner un pays</option>
                                <option value="GAB">Gabon</option>
                                <option value="CMR">Cameroun</option>
                                <option value="CG">Congo Brazzaville</option>
                                <option value="CD">Congo Kinshasa</option>
                            </select>
                        </div>
                        @error('country_code') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="country_name" class="block text-sm font-medium leading-6 text-gray-900">Nom du pays *</label>
                        <div class="mt-2">
                            <input type="text" wire:model="country_name" id="country_name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('country_name') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="nif" class="block text-sm font-medium leading-6 text-gray-900">NIF</label>
                        <div class="mt-2">
                            <input type="text" wire:model="nif" id="nif" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('nif') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="rccm" class="block text-sm font-medium leading-6 text-gray-900">RCCM</label>
                        <div class="mt-2">
                            <input type="text" wire:model="rccm" id="rccm" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('rccm') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-2">
                        <label for="postal_box" class="block text-sm font-medium leading-6 text-gray-900">Boîte postale (BP)</label>
                        <div class="mt-2">
                            <input type="text" wire:model="postal_box" id="postal_box" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('postal_box') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="business_permit" class="block text-sm font-medium leading-6 text-gray-900">Permis d'exploitation</label>
                        <div class="mt-2">
                            <input type="text" wire:model="business_permit" id="business_permit" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('business_permit') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>

                    <div class="sm:col-span-3">
                        <label for="website" class="block text-sm font-medium leading-6 text-gray-900">Site web</label>
                        <div class="mt-2">
                            <input type="url" wire:model="website" id="website" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
                        </div>
                        @error('website') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            <!-- Templates de facture -->
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Templates de facture</h2>
                <p class="mt-1 text-sm leading-6 text-gray-600">Téléversez les images d'en-tête et de pied de page personnalisées pour les factures.</p>

                <div class="mt-10 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-2">
                    <!-- Image d'en-tête -->
                    <div>
                        <label for="header_image" class="block text-sm font-medium leading-6 text-gray-900">Image d'en-tête</label>
                        <div class="mt-2">
                            @if ($current_header_image)
                                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                                    <p class="text-xs text-blue-600 font-medium mb-2">Image d'en-tête actuelle :</p>
                                    <img src="{{ $current_header_image }}" alt="En-tête actuel" class="h-32 w-auto border border-gray-300 rounded-lg shadow-sm">
                                    <button type="button" wire:click="removeHeaderImage" class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                        🗑️ Supprimer l'image actuelle
                                    </button>
                                </div>
                            @endif
                            <input type="file" wire:model="header_image" id="header_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <div wire:loading wire:target="header_image" class="mt-2 text-sm text-blue-600">
                                ⏳ Chargement de l'image...
                            </div>
                        </div>
                        @error('header_image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        @if ($header_image)
                            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-xs text-gray-600 mb-2">Aperçu de la nouvelle image d'en-tête :</p>
                                <img src="{{ $header_image->temporaryUrl() }}" alt="Aperçu en-tête" class="h-32 w-auto border border-gray-300 rounded-lg shadow-sm">
                            </div>
                        @endif
                    </div>

                    <!-- Image de pied de page -->
                    <div>
                        <label for="footer_image" class="block text-sm font-medium leading-6 text-gray-900">Image de pied de page</label>
                        <div class="mt-2">
                            @if ($current_footer_image)
                                <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                                    <p class="text-xs text-green-600 font-medium mb-2">Image de pied de page actuelle :</p>
                                    <img src="{{ $current_footer_image }}" alt="Pied de page actuel" class="h-32 w-auto border border-gray-300 rounded-lg shadow-sm">
                                    <button type="button" wire:click="removeFooterImage" class="mt-2 text-sm text-red-600 hover:text-red-500 underline">
                                        🗑️ Supprimer l'image actuelle
                                    </button>
                                </div>
                            @endif
                            <input type="file" wire:model="footer_image" id="footer_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <div wire:loading wire:target="footer_image" class="mt-2 text-sm text-blue-600">
                                ⏳ Chargement de l'image...
                            </div>
                        </div>
                        @error('footer_image') <p class="mt-2 text-sm text-red-600">{{ $message }}</p> @enderror
                        @if ($footer_image)
                            <div class="mt-4 p-3 bg-gray-50 border border-gray-200 rounded-lg">
                                <p class="text-xs text-gray-600 mb-2">Aperçu de la nouvelle image de pied de page :</p>
                                <img src="{{ $footer_image->temporaryUrl() }}" alt="Aperçu pied de page" class="h-32 w-auto border border-gray-300 rounded-lg shadow-sm">
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Statut -->
            <div class="border-b border-gray-900/10 pb-12">
                <h2 class="text-base font-semibold leading-7 text-gray-900">Configuration</h2>
                <div class="mt-10 space-y-10">
                    <fieldset>
                        <div class="mt-6 space-y-6">
                            <div class="flex items-center gap-x-3">
                                <input wire:model="is_active" id="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-600">
                                <label for="is_active" class="text-sm font-medium leading-6 text-gray-900">Branche active</label>
                            </div>
                        </div>
                    </fieldset>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-x-6">
            <a href="{{ route('stores.index') }}" class="text-sm font-semibold leading-6 text-gray-900">Annuler</a>
            <button type="submit" class="rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <span wire:loading.remove wire:target="save">{{ $isEditing ? 'Mettre à jour' : 'Créer' }}</span>
                <span wire:loading wire:target="save">{{ $isEditing ? 'Mise à jour...' : 'Création...' }}</span>
            </button>
        </div>
    </form>
</div>