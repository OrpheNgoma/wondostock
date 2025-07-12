<form wire:submit.prevent="save">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                Paramètres de l'Entreprise
            </h2>
            <p class="mt-1 text-sm text-gray-500">Mettez à jour les informations légales et de contact de votre entreprise.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                <span wire:loading.remove wire:target="save">Sauvegarder les changements</span>
                <span wire:loading wire:target="save">Sauvegarde...</span>
            </button>
        </div>
    </div>

    <!-- Contenu du formulaire -->
    <div class="mt-10 space-y-8">
        <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Informations Générales</h3>
            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom commercial *</label>
                    <input type="text" wire:model="name" id="name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="legal_name" class="block text-sm font-medium leading-6 text-gray-900">Raison sociale</label>
                    <input type="text" wire:model="legal_name" id="legal_name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="sm:col-span-3">
                    <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Email de contact</label>
                    <input type="email" wire:model="email" id="email" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="sm:col-span-3">
                    <label for="phone_number" class="block text-sm font-medium leading-6 text-gray-900">Téléphone</label>
                    <input type="text" wire:model="phone_number" id="phone_number" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="sm:col-span-full">
                    <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Adresse</label>
                    <textarea wire:model="address" id="address" rows="3" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                </div>
            </div>
        </div>

        <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Informations Légales & Logo</h3>
            <div class="mt-6 grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                <div class="sm:col-span-3">
                    <label for="rccm" class="block text-sm font-medium leading-6 text-gray-900">N° RCCM</label>
                    <input type="text" wire:model="rccm" id="rccm" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                <div class="sm:col-span-3">
                    <label for="nif" class="block text-sm font-medium leading-6 text-gray-900">NIF</label>
                    <input type="text" wire:model="nif" id="nif" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
                 <div class="sm:col-span-full">
                    <label for="logo" class="block text-sm font-medium leading-6 text-gray-900">Logo</label>
                    <div class="mt-2 flex items-center gap-x-3">
                        {{-- On vérifie que le fichier est bien une image avant de l'afficher --}}
                        @if ($logo && Str::startsWith($logo->getMimeType(), 'image/'))
                            <img src="{{ $logo->temporaryUrl() }}" class="h-12 w-12 rounded-full object-cover">
                        @elseif ($company->hasMedia('logo'))
                            <img src="{{ $company->getFirstMediaUrl('logo') }}" class="h-12 w-12 rounded-full object-cover">
                        @else
                            <div class="h-12 w-12 rounded-full bg-gray-200 flex items-center justify-center text-gray-500">
                                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5zm10.5-11.25h.008v.008h-.008V8.25zm.375 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z" /></svg>
                            </div>
                        @endif
                        <input type="file" wire:model="logo" id="logo" class="hidden">
                        <label for="logo" class="cursor-pointer rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Changer</label>
                    </div>
                    @error('logo')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                 </div>
            </div>
        </div>
    </div>
</form>