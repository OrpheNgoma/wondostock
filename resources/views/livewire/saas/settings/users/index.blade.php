<div x-data="{ showForm: @entangle('showForm') }" class="space-y-6">
    <!-- En-tête moderne sobre -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Gestion des Utilisateurs
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Gérez les membres de votre équipe et leurs accès aux différentes fonctionnalités
                </p>
            </div>
            <div class="flex items-center gap-3">
                @if($canCreateUser)
                    <button wire:click="create" type="button" 
                            class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Inviter un Utilisateur
                    </button>
                @else
                    <div class="text-center">
                        <button disabled type="button" 
                                class="inline-flex items-center gap-2 rounded-lg bg-gray-300 px-4 py-2.5 text-sm font-semibold text-gray-500 cursor-not-allowed">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                            </svg>
                            Limite atteinte
                        </button>
                        <p class="text-xs text-gray-500 mt-1">
                            {{ $currentUserCount }}/{{ $userLimit === PHP_INT_MAX ? '∞' : $userLimit }} utilisateurs
                        </p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Messages de session -->
    @if (session('success'))
        <div class="mx-6 rounded-lg bg-green-50 p-4 border border-green-200">
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

    <!-- Tableau des utilisateurs -->
    <div class="mx-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-gray-200">
            <!-- En-tête du tableau -->
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Équipe</h3>
                        <p class="text-xs text-gray-600 mt-1">
                            {{ $users->total() }} utilisateur(s) au total 
                            @if(!$canCreateUser)
                                <span class="text-orange-600 font-medium">
                                    ({{ $currentUserCount }}/{{ $userLimit === PHP_INT_MAX ? '∞' : $userLimit }} - Limite atteinte)
                                </span>
                            @else
                                <span class="text-green-600">
                                    ({{ $currentUserCount }}/{{ $userLimit === PHP_INT_MAX ? '∞' : $userLimit }})
                                </span>
                            @endif
                        </p>
                    </div>
                </div>
            </div>

            <!-- Version desktop -->
            <div class="hidden lg:block overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Utilisateur
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Rôle
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Magasin
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Statut
                            </th>
                            <th scope="col" class="relative py-3 pl-3 pr-6 text-right text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($users as $user)
                            <tr wire:key="{{ $user->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                            <span class="text-sm font-semibold text-emerald-700">
                                                {{ strtoupper(substr($user->name, 0, 2)) }}
                                            </span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @php
                                        $role = $user->roles->first();
                                        $roleConfig = match($role?->name ?? 'N/A') {
                                            'Administrateur' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'ring' => 'ring-purple-600/20'],
                                            'Manager' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'ring' => 'ring-blue-600/20'],
                                            'Vendeur' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'ring' => 'ring-green-600/20'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full {{ $roleConfig['bg'] }} px-2 py-1 text-xs font-medium {{ $roleConfig['text'] }} ring-1 {{ $roleConfig['ring'] }}">
                                        {{ $role?->name ?? 'Aucun rôle' }}
                                    </span>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-700">
                                        {{ $user->store?->name ?? 'Tous les magasins' }}
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @if($user->id === auth()->id())
                                        <span class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2 py-1 text-xs font-medium text-emerald-800 ring-1 ring-emerald-600/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-emerald-500"></div>
                                            Connecté
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 ring-1 ring-gray-600/20">
                                            <div class="h-1.5 w-1.5 rounded-full bg-gray-500"></div>
                                            Actif
                                        </span>
                                    @endif
                                </td>
                                <td class="relative py-4 pl-3 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button wire:click="edit({{ $user->id }})" 
                                                class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-2 py-1 text-xs font-medium text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                            </svg>
                                            Modifier
                                        </button>
                                        @if($user->id !== auth()->id())
                                            <button wire:click="delete({{ $user->id }})" 
                                                    wire:confirm="Êtes-vous sûr de vouloir supprimer cet utilisateur ?" 
                                                    class="inline-flex items-center gap-1 rounded-lg bg-red-100 px-2 py-1 text-xs font-medium text-red-700 hover:bg-red-200 transition-colors duration-200">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                                Supprimer
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Aucun utilisateur trouvé</p>
                                            <p class="text-xs text-gray-500 mt-1">Commencez par inviter votre première personne</p>
                                        </div>
                                        <button wire:click="create" 
                                                class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Inviter quelqu'un
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Version mobile -->
            <div class="lg:hidden divide-y divide-gray-200">
                @forelse ($users as $user)
                    <div wire:key="mobile-{{ $user->id }}" class="p-4">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3 flex-1">
                                <div class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center">
                                    <span class="text-sm font-semibold text-emerald-700">
                                        {{ strtoupper(substr($user->name, 0, 2)) }}
                                    </span>
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-semibold text-gray-900">{{ $user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $user->email }}</p>
                                </div>
                            </div>
                            @if($user->id === auth()->id())
                                <span class="text-xs bg-emerald-100 text-emerald-800 px-2 py-1 rounded-full">Vous</span>
                            @endif
                        </div>
                        <div class="mt-3 grid grid-cols-2 gap-3 text-xs">
                            <div>
                                <span class="text-gray-500">Rôle:</span>
                                <span class="text-gray-900 ml-1">{{ $user->roles->first()?->name ?? 'Aucun' }}</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Magasin:</span>
                                <span class="text-gray-900 ml-1">{{ $user->store?->name ?? 'Tous' }}</span>
                            </div>
                        </div>
                        <div class="mt-3 flex gap-2">
                            <button wire:click="edit({{ $user->id }})" 
                                    class="flex-1 text-xs bg-gray-100 text-gray-700 px-3 py-2 rounded-lg hover:bg-gray-200 transition-colors">
                                Modifier
                            </button>
                            @if($user->id !== auth()->id())
                                <button wire:click="delete({{ $user->id }})" 
                                        wire:confirm="Êtes-vous sûr ?" 
                                        class="flex-1 text-xs bg-red-100 text-red-700 px-3 py-2 rounded-lg hover:bg-red-200 transition-colors">
                                    Supprimer
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-16 text-center">
                        <div class="flex flex-col items-center gap-4">
                            <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-medium text-gray-900">Aucun utilisateur</p>
                                <p class="text-xs text-gray-500 mt-1">Invitez votre première personne</p>
                            </div>
                            <button wire:click="create" 
                                    class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Inviter
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            @if($users->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    {{ $users->links() }}
                </div>
            @endif
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
                                    <div>
                                        <label for="name" class="block text-xs font-medium text-gray-700 mb-2">Nom complet *</label>
                                        <input type="text" wire:model="name" id="name" 
                                               placeholder="Prénom et nom de famille"
                                               class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                                        @error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label for="email" class="block text-xs font-medium text-gray-700 mb-2">Adresse email *</label>
                                        <input type="email" wire:model="email" id="email" 
                                               placeholder="prenom.nom@exemple.com"
                                               class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                                        @error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label for="password" class="block text-xs font-medium text-gray-700 mb-2">
                                            Mot de passe @if(!$editingUser->exists)*@endif
                                        </label>
                                        <input type="password" wire:model="password" id="password" 
                                               placeholder="@if($editingUser->exists)Laisser vide pour ne pas changer@else Mot de passe sécurisé@endif"
                                               class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                                        @error('password')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    <div>
                                        <label for="role_id" class="block text-xs font-medium text-gray-700 mb-2">Rôle *</label>
                                        <select wire:model.live="role_id" id="role_id" 
                                                class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                                            <option value="">Sélectionner un rôle...</option>
                                            @foreach($roles as $id => $roleName)
                                                <option value="{{ $id }}">{{ $roleName }}</option>
                                            @endforeach
                                        </select>
                                        @error('role_id')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror
                                    </div>
                                    @if($role_id && \Spatie\Permission\Models\Role::find($role_id)?->name !== 'Administrateur')
                                    <div>
                                        <label for="store_id" class="block text-xs font-medium text-gray-700 mb-2">Magasin d'affectation</label>
                                        <select wire:model="store_id" id="store_id" 
                                                class="block w-full rounded-lg border-gray-300 py-3 px-3 text-sm focus:border-emerald-500 focus:ring-emerald-500 transition-colors duration-200">
                                            <option value="">Accès à tous les magasins</option>
                                            @foreach($stores as $id => $storeName)
                                                <option value="{{ $id }}">{{ $storeName }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end gap-3 px-4 py-4 border-t border-gray-200">
                                <button @click="showForm = false" type="button" 
                                        class="inline-flex items-center gap-2 rounded-lg bg-gray-100 px-4 py-2.5 text-sm font-medium text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                                    Annuler
                                </button>
                                <button type="submit" 
                                        class="inline-flex items-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors duration-200">
                                    <svg wire:loading wire:target="save" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="save">{{ $editingUser->exists ? 'Mettre à jour' : 'Inviter' }}</span>
                                    <span wire:loading wire:target="save">Traitement...</span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
