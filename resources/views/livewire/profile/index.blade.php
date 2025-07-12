<div>
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Mon Profil</h2>
            <p class="mt-1 text-sm leading-6 text-gray-500">Gérez vos informations personnelles et votre mot de passe.</p>
        </div>
    </div>

    <div class="mt-10 space-y-8">
        <!-- Carte Informations du Profil -->
        <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Informations du Profil</h3>
            <p class="mt-1 text-sm text-gray-500">Mettez à jour votre nom et votre adresse e-mail.</p>
            <form wire:submit.prevent="updateProfile" class="mt-6">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-3">
                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom complet</label>
                        <input type="text" wire:model="name" id="name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="sm:col-span-3">
                        <label for="email" class="block text-sm font-medium leading-6 text-gray-900">Adresse e-mail</label>
                        <input type="email" wire:model="email" id="email" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        @error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                </div>
                <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Sauvegarder</button>
                </div>
            </form>
        </div>

        <!-- Carte Mise à jour du mot de passe -->
        <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <h3 class="text-base font-semibold leading-6 text-gray-900">Mettre à jour le mot de passe</h3>
            <p class="mt-1 text-sm text-gray-500">Assurez-vous d'utiliser un mot de passe long et aléatoire pour rester en sécurité.</p>
            <form wire:submit.prevent="updatePassword" class="mt-6">
                <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
                    <div class="sm:col-span-4">
                        <label for="current_password" class="block text-sm font-medium leading-6 text-gray-900">Mot de passe actuel</label>
                        <input type="password" wire:model="current_password" id="current_password" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        @error('current_password')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="sm:col-span-4">
                        <label for="password" class="block text-sm font-medium leading-6 text-gray-900">Nouveau mot de passe</label>
                        <input type="password" wire:model="password" id="password" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        @error('password')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                    </div>
                    <div class="sm:col-span-4">
                        <label for="password_confirmation" class="block text-sm font-medium leading-6 text-gray-900">Confirmer le mot de passe</label>
                        <input type="password" wire:model="password_confirmation" id="password_confirmation" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    </div>
                </div>
                 <div class="mt-6 flex justify-end">
                    <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Changer le mot de passe</button>
                </div>
            </form>
        </div>
    </div>
</div>