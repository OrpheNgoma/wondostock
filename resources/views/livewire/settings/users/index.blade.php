<div x-data="{ showForm: @entangle('showForm') }">
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Utilisateurs</h2>
            <p class="mt-1 text-sm text-gray-500">Gérez les membres de votre équipe et leurs accès.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <button wire:click="create" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Inviter un Utilisateur
            </button>
        </div>
    </div>
    
    <!-- Tableau -->
    <div class="mt-8 flow-root">
        <div class="inline-block min-w-full py-2 align-middle sm:px-1">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50"><tr><th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nom</th><th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Rôle</th><th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Magasin</th><th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6"></th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($users as $user)
                            <tr wire:key="{{ $user->id }}">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                    <div class="text-gray-500">{{ $user->email }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->roles->first()?->name ?? 'N/A' }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $user->store?->name ?? 'Tous' }}</td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <button wire:click="edit({{ $user->id }})" class="text-indigo-600 hover:text-indigo-900">Modifier</button>
                                    @if($user->id !== auth()->id())
                                    <button wire:click="delete({{ $user->id }})" wire:confirm="Êtes-vous sûr ?" class="ml-4 text-red-600 hover:text-red-900">Supprimer</button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-10 text-gray-500">Aucun utilisateur trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $users->links() }}</div>
        </div>
    </div>
    
    <!-- Panneau latéral pour le formulaire -->
    <div x-show="showForm" x-cloak class="relative z-10">
        <div x-show="showForm" x-transition:enter="ease-in-out duration-500" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showForm = false" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showForm" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" class="pointer-events-auto w-screen max-w-md">
                        <form wire:submit.prevent="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll py-6">
                                <div class="px-4 sm:px-6"><div class="flex items-start justify-between"><h2 class="text-base font-semibold leading-6 text-gray-900">{{ $editingUser->exists ? 'Modifier l\'Utilisateur' : 'Inviter un Utilisateur' }}</h2><div class="ml-3 flex h-7 items-center"><button @click="showForm = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button></div></div></div>
                                <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-6">
                                    <div><label for="name" class="block text-sm font-medium text-gray-900">Nom complet *</label><input type="text" wire:model="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="email" class="block text-sm font-medium text-gray-900">Email *</label><input type="email" wire:model="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="password" class="block text-sm font-medium text-gray-900">Mot de passe @if(!$editingUser->exists)*@endif</label><input type="password" wire:model="password" id="password" placeholder="Laisser vide pour ne pas changer" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('password')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="role_id" class="block text-sm font-medium text-gray-900">Rôle *</label><select wire:model.live="role_id" id="role_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"><option value="">Choisir un rôle</option>@foreach($roles as $id => $roleName)<option value="{{ $id }}">{{ $roleName }}</option>@endforeach</select>@error('role_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    @if($role_id && \Spatie\Permission\Models\Role::find($role_id)?->name !== 'Administrateur')
                                    <div><label for="store_id" class="block text-sm font-medium text-gray-900">Magasin d'affectation</label><select wire:model="store_id" id="store_id" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"><option value="">Aucun</option>@foreach($stores as $id => $storeName)<option value="{{ $id }}">{{ $storeName }}</option>@endforeach</select></div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end px-4 py-4"><button @click="showForm = false" type="button" class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</button><button type="submit" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Enregistrer</button></div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
