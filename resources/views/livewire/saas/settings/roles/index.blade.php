<div>
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between mb-6">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900">Rôles & Permissions</h2>
            <p class="mt-1 text-sm text-gray-500">
                Gérez les rôles et permissions de votre entreprise. Définissez qui peut faire quoi dans l'application.
            </p>
        </div>
        <div class="mt-4 flex sm:ml-4 sm:mt-0">
            <button wire:click="createRole" 
                    class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                    <path d="M10.75 4.75a.75.75 0 00-1.5 0v4.5h-4.5a.75.75 0 000 1.5h4.5v4.5a.75.75 0 001.5 0v-4.5h4.5a.75.75 0 000-1.5h-4.5v-4.5z" />
                </svg>
                Nouveau Rôle
            </button>
        </div>
    </div>

    <!-- Barre de recherche et onglets -->
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M9 3.5a5.5 5.5 0 100 11 5.5 5.5 0 000-11zM2 9a7 7 0 1112.452 4.391l3.328 3.329a.75.75 0 11-1.06 1.06l-3.329-3.328A7 7 0 012 9z" clip-rule="evenodd" />
                    </svg>
                </div>
                <input wire:model.live="search" 
                       type="search" 
                       placeholder="Rechercher..." 
                       class="block w-full rounded-md border-0 py-1.5 pl-10 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6">
            </div>
            
            <div class="flex rounded-lg shadow-sm">
                <button wire:click="$set('selectedTab', 'roles')" 
                        class="relative inline-flex items-center rounded-l-md px-3 py-2 text-sm font-semibold {{ $selectedTab === 'roles' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50' }}">
                    Rôles
                </button>
                <button wire:click="$set('selectedTab', 'users')" 
                        class="relative -ml-px inline-flex items-center rounded-r-md px-3 py-2 text-sm font-semibold {{ $selectedTab === 'users' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-900 ring-1 ring-inset ring-gray-300 hover:bg-gray-50' }}">
                    Utilisateurs
                </button>
            </div>
        </div>
    </div>

    <!-- Contenu des onglets -->
    @if ($selectedTab === 'roles')
        <!-- Liste des rôles -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <div class="px-4 py-6 sm:p-8">
                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                            Nom du rôle
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Description
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Utilisateurs
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Permissions
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse ($roles as $role)
                                        <tr class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-0">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0">
                                                        <div class="h-10 w-10 rounded-full bg-indigo-100 flex items-center justify-center">
                                                            <svg class="h-5 w-5 text-indigo-600" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-5.5-2.5a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0zM10 12a5.99 5.99 0 00-4.793 2.39A6.483 6.483 0 0010 16.5a6.483 6.483 0 004.793-2.11A5.99 5.99 0 0010 12z" clip-rule="evenodd" />
                                                            </svg>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $role->name }}</div>
                                                        <div class="text-sm text-gray-500">Créé {{ $role->created_at->diffForHumans() }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $role->description ?: 'Aucune description' }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center rounded-full bg-blue-100 px-2.5 py-0.5 text-xs font-medium text-blue-800">
                                                    {{ $role->users_count }} utilisateur(s)
                                                </span>
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">
                                                    {{ $role->permissions()->count() }} permission(s)
                                                </span>
                                            </td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <div class="flex items-center justify-end space-x-2">
                                                    <button wire:click="managePermissions({{ $role->id }})" 
                                                            class="text-indigo-600 hover:text-indigo-900">
                                                        Permissions
                                                    </button>
                                                    <button wire:click="assignRole({{ $role->id }})" 
                                                            class="text-green-600 hover:text-green-900">
                                                        Assigner
                                                    </button>
                                                    <button wire:click="editRole({{ $role->id }})" 
                                                            class="text-blue-600 hover:text-blue-900">
                                                        Modifier
                                                    </button>
                                                    <button wire:click="confirmDelete({{ $role->id }})" 
                                                            class="text-red-600 hover:text-red-900">
                                                        Supprimer
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="px-3 py-8 text-center text-sm text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                                    </svg>
                                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun rôle</h3>
                                                    <p class="mt-1 text-sm text-gray-500">Commencez par créer un nouveau rôle.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                @if ($roles->hasPages())
                    <div class="mt-6">
                        {{ $roles->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    @if ($selectedTab === 'users')
        <!-- Liste des utilisateurs avec leurs rôles -->
        <div class="bg-white shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
            <div class="px-4 py-6 sm:p-8">
                <div class="flow-root">
                    <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
                        <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                            <table class="min-w-full divide-y divide-gray-300">
                                <thead>
                                    <tr>
                                        <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-0">
                                            Utilisateur
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Email
                                        </th>
                                        <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">
                                            Rôles assignés
                                        </th>
                                        <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-0">
                                            <span class="sr-only">Actions</span>
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @forelse ($users as $user)
                                        <tr class="hover:bg-gray-50">
                                            <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-0">
                                                <div class="flex items-center">
                                                    <div class="h-10 w-10 flex-shrink-0">
                                                        <div class="h-10 w-10 rounded-full bg-gray-100 flex items-center justify-center">
                                                            <span class="text-sm font-medium text-gray-700">
                                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                                            </span>
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                        <div class="text-sm text-gray-500">
                                                            Inscrit {{ $user->created_at->diffForHumans() }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-3 py-4 text-sm text-gray-500">
                                                <div class="flex flex-wrap gap-1">
                                                    @forelse ($user->roles as $role)
                                                        <span class="inline-flex items-center gap-x-1.5 rounded-full bg-indigo-100 px-2 py-1 text-xs font-medium text-indigo-700">
                                                            {{ $role->name }}
                                                            <button wire:click="removeRoleFromUser({{ $user->id }}, {{ $role->id }})" 
                                                                    class="group relative -mr-1 h-3.5 w-3.5 rounded-sm hover:bg-indigo-600/20">
                                                                <svg viewBox="0 0 14 14" class="h-3.5 w-3.5 stroke-indigo-700/50 group-hover:stroke-indigo-700/75">
                                                                    <path d="m4 4 6 6m0-6-6 6" />
                                                                </svg>
                                                            </button>
                                                        </span>
                                                    @empty
                                                        <span class="text-gray-400 italic">Aucun rôle assigné</span>
                                                    @endforelse
                                                </div>
                                            </td>
                                            <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-0">
                                                <!-- Actions pour l'utilisateur peuvent être ajoutées ici -->
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-3 py-8 text-center text-sm text-gray-500">
                                                <div class="flex flex-col items-center">
                                                    <svg class="h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                                                    </svg>
                                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun utilisateur</h3>
                                                    <p class="mt-1 text-sm text-gray-500">Il n'y a aucun utilisateur dans votre entreprise.</p>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
                
                @if ($users->hasPages())
                    <div class="mt-6">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    @endif

    <!-- Modal Créer un rôle -->
    @if ($showCreateRoleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-indigo-100">
                            <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Créer un nouveau rôle
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Définissez un nouveau rôle pour votre entreprise.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="saveRole" class="mt-6">
                        <div class="space-y-4">
                            <div>
                                <label for="roleName" class="block text-sm font-medium text-gray-700">
                                    Nom du rôle *
                                </label>
                                <input type="text" 
                                       wire:model="roleName" 
                                       id="roleName"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       placeholder="Ex: Responsable des ventes"
                                       required>
                                @error('roleName') 
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                                @enderror
                            </div>
                            
                            <div>
                                <label for="roleDescription" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea wire:model="roleDescription" 
                                          id="roleDescription"
                                          rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                          placeholder="Description du rôle et de ses responsabilités..."></textarea>
                                @error('roleDescription') 
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                Créer le rôle
                            </button>
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Modifier un rôle -->
    @if ($showEditRoleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-blue-100">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Modifier le rôle
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Modifiez les informations du rôle "{{ $selectedRole?->name }}".
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="updateRole" class="mt-6">
                        <div class="space-y-4">
                            <div>
                                <label for="editRoleName" class="block text-sm font-medium text-gray-700">
                                    Nom du rôle *
                                </label>
                                <input type="text" 
                                       wire:model="roleName" 
                                       id="editRoleName"
                                       class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                       required>
                                @error('roleName') 
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                                @enderror
                            </div>
                            
                            <div>
                                <label for="editRoleDescription" class="block text-sm font-medium text-gray-700">
                                    Description
                                </label>
                                <textarea wire:model="roleDescription" 
                                          id="editRoleDescription"
                                          rows="3"
                                          class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                          placeholder="Description du rôle et de ses responsabilités..."></textarea>
                                @error('roleDescription') 
                                    <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                Enregistrer
                            </button>
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Gérer les permissions -->
    @if ($showPermissionsModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Gérer les permissions
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Définissez les permissions pour le rôle "{{ $selectedRole?->name }}".
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="savePermissions" class="mt-6">
                        <div class="max-h-96 overflow-y-auto">
                            @foreach ($permissions as $group => $permissionList)
                                <div class="mb-6">
                                    <h4 class="text-base font-medium text-gray-800 capitalize border-b pb-2 mb-4">
                                        {{ ucfirst($group) }}
                                    </h4>
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach ($permissionList as $permission)
                                            <div class="relative flex items-start">
                                                <div class="flex items-center h-5">
                                                    <input wire:model="rolePermissions" 
                                                           id="permission-{{ $permission->id }}" 
                                                           value="{{ $permission->id }}" 
                                                           type="checkbox" 
                                                           class="focus:ring-indigo-500 h-4 w-4 text-indigo-600 border-gray-300 rounded">
                                                </div>
                                                <div class="ml-3 text-sm">
                                                    <label for="permission-{{ $permission->id }}" class="font-medium text-gray-700">
                                                        {{ $permission->name }}
                                                    </label>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm">
                                Enregistrer les permissions
                            </button>
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Assigner un rôle -->
    @if ($showAssignRoleModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div>
                        <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100">
                            <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-5">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Assigner le rôle
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Assignez le rôle "{{ $selectedRole?->name }}" à un utilisateur.
                                </p>
                            </div>
                        </div>
                    </div>
                    
                    <form wire:submit.prevent="assignRoleToUser" class="mt-6">
                        <div>
                            <label for="selectedUser" class="block text-sm font-medium text-gray-700">
                                Sélectionner un utilisateur *
                            </label>
                            <select wire:model="selectedUser" 
                                    id="selectedUser"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                                    required>
                                <option value="">-- Choisir un utilisateur --</option>
                                @foreach ($availableUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            @error('selectedUser') 
                                <span class="mt-1 text-sm text-red-600">{{ $message }}</span> 
                            @enderror
                        </div>
                        
                        @if (empty($availableUsers) || $availableUsers->count() === 0)
                            <div class="mt-4 p-4 bg-yellow-50 border border-yellow-200 rounded-md">
                                <p class="text-sm text-yellow-700">
                                    Tous les utilisateurs ont déjà ce rôle ou il n'y a aucun utilisateur disponible.
                                </p>
                            </div>
                        @endif
                        
                        <div class="mt-5 sm:mt-6 sm:grid sm:grid-cols-2 sm:gap-3 sm:grid-flow-row-dense">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-indigo-600 text-base font-medium text-white hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:col-start-2 sm:text-sm"
                                    @if(empty($availableUsers) || $availableUsers->count() === 0) disabled @endif>
                                Assigner le rôle
                            </button>
                            <button type="button" 
                                    wire:click="closeModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:col-start-1 sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif

    <!-- Modal Confirmer suppression -->
    @if ($showDeleteModal)
        <div class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" wire:click="closeModal"></div>
                
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                
                <div class="relative inline-block align-bottom bg-white rounded-lg px-4 pt-5 pb-4 text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full sm:p-6">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 15.5c-.77.833.192 2.5 1.732 2.5z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                Supprimer le rôle
                            </h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Êtes-vous sûr de vouloir supprimer le rôle "{{ $selectedRole?->name }}" ? 
                                    Cette action ne peut pas être annulée.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div class="mt-5 sm:mt-4 sm:ml-10 sm:pl-4 sm:flex">
                        <button wire:click="deleteRole"
                                class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:w-auto sm:text-sm">
                            Supprimer
                        </button>
                        <button wire:click="closeModal"
                                class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                            Annuler
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>