<div class="space-y-8">
    <!-- En-tête moderne avec gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-500 via-blue-600 to-indigo-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-blue-500/20 to-indigo-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Gestion des Magasins
                    </h1>
                    <p class="text-blue-100 text-lg">
                        Gérez tous vos points de vente depuis une seule interface
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button wire:click="create" 
                            type="button" 
                            class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouveau Magasin
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
    </div>

    <!-- Messages modernes -->
    @if (session('success'))
        <div class="rounded-2xl bg-gradient-to-r from-green-50 to-emerald-50 p-4 border border-green-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-green-100 flex items-center justify-center">
                    <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-green-800">{{ session('success') }}</p>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-2xl bg-gradient-to-r from-red-50 to-pink-50 p-4 border border-red-200 shadow-sm">
            <div class="flex items-center gap-3">
                <div class="h-8 w-8 rounded-lg bg-red-100 flex items-center justify-center">
                    <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="text-sm font-semibold text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Tableau moderne des magasins -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <!-- En-tête du tableau -->
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Liste des Magasins</h3>
                    <p class="text-sm text-gray-600">{{ $stores->count() }} magasin(s) au total</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <div class="h-3 w-3 rounded-full bg-blue-500"></div>
                    <span>Points de vente</span>
                </div>
            </div>
        </div>

        <!-- Contenu du tableau -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50/50">
                    <tr>
                        <th scope="col" class="py-4 pl-6 pr-3 text-left text-sm font-semibold text-gray-700">
                            <div class="flex items-center gap-2">
                                <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                </svg>
                                Magasin
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Localisation</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Contact</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($stores as $store)
                        <tr wire:key="{{ $store->id }}" class="group hover:bg-gradient-to-r hover:from-blue-50/50 hover:to-indigo-50/50 transition-all duration-200">
                            <td class="py-4 pl-6 pr-3">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center shadow-sm">
                                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">
                                            {{ $store->name }}
                                        </p>
                                        <p class="text-xs text-gray-500 mt-1">
                                            ID: {{ $store->id }}
                                        </p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="text-sm">
                                    @if($store->address)
                                        <p class="text-gray-900 font-medium">{{ Str::limit($store->address, 30) }}</p>
                                    @endif
                                    @if($store->city)
                                        <p class="text-gray-500 text-xs mt-1">{{ $store->city }}</p>
                                    @endif
                                    @if(!$store->address && !$store->city)
                                        <span class="text-gray-400 text-xs">Non renseigné</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                @if($store->contact_phone)
                                    <div class="flex items-center gap-2">
                                        <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                        </svg>
                                        <span class="text-sm text-gray-700">{{ $store->contact_phone }}</span>
                                    </div>
                                @else
                                    <span class="text-gray-400 text-xs">Non renseigné</span>
                                @endif
                            </td>
                            <td class="px-3 py-4">
                                @if ($store->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-600/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-50 px-3 py-1 text-xs font-semibold text-red-700 ring-1 ring-red-600/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-red-500"></div>
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $store->id }})" 
                                           class="inline-flex items-center gap-1 rounded-lg bg-blue-50 px-3 py-2 text-xs font-medium text-blue-700 hover:bg-blue-100 transition-colors duration-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Modifier
                                    </button>
                                    <button wire:click="delete({{ $store->id }})" 
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce magasin ?" 
                                            class="inline-flex items-center gap-1 rounded-lg bg-red-50 px-3 py-2 text-xs font-medium text-red-700 hover:bg-red-100 transition-colors duration-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                        </svg>
                                        Supprimer
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="h-16 w-16 rounded-full bg-gray-100 flex items-center justify-center">
                                        <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Aucun magasin trouvé</p>
                                        <p class="text-sm text-gray-500 mt-1">Commencez par créer votre premier point de vente</p>
                                    </div>
                                    <button wire:click="create" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Nouveau Magasin
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    
    <!-- Panneau latéral (Slide-over) pour Créer/Modifier -->
        
    <div 
        x-data="{ showForm: false }"
        @open-form.window="showForm = true"
        @close-form.window="showForm = false"
        x-show="showForm"
        x-cloak
        class="relative z-10" 
        aria-labelledby="slide-over-title" 
        role="dialog" 
        aria-modal="true"
    >
        <div x-show="showForm" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showForm = false" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showForm" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                        <form wire:submit.prevent="save" class="flex h-full flex-col bg-white shadow-xl">
                            <!-- En-tête moderne du formulaire -->
                            <div class="border-b border-gray-100 bg-gradient-to-r from-blue-50 to-indigo-50 px-6 py-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-blue-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900" id="slide-over-title">
                                                {{ $editingStore->exists ? 'Modifier le Magasin' : 'Créer un Nouveau Magasin' }}
                                            </h2>
                                            <p class="text-sm text-gray-600">
                                                {{ $editingStore->exists ? 'Mettre à jour les informations du point de vente' : 'Ajouter un nouveau point de vente' }}
                                            </p>
                                        </div>
                                    </div>
                                    <button @click="showForm = false" 
                                            type="button" 
                                            class="rounded-xl p-2 text-gray-400 hover:text-gray-600 hover:bg-white/50 transition-all duration-200">
                                        <span class="sr-only">Fermer</span>
                                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <!-- Contenu du formulaire moderne -->
                            <div class="flex-1 overflow-y-auto p-6">
                                <div class="space-y-6">
                                    <!-- Nom du magasin -->
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                                </svg>
                                                Nom du magasin *
                                            </span>
                                        </label>
                                        <input type="text" 
                                               wire:model="name" 
                                               id="name" 
                                               placeholder="Entrez le nom du magasin"
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                        @error('name') 
                                            <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                </svg>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Adresse -->
                                    <div>
                                        <label for="address" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                                </svg>
                                                Adresse
                                            </span>
                                        </label>
                                        <input type="text" 
                                               wire:model="address" 
                                               id="address" 
                                               placeholder="Adresse du magasin"
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>

                                    <!-- Ville -->
                                    <div>
                                        <label for="city" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                                </svg>
                                                Ville
                                            </span>
                                        </label>
                                        <input type="text" 
                                               wire:model="city" 
                                               id="city" 
                                               placeholder="Ville du magasin"
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>

                                    <!-- Téléphone de contact -->
                                    <div>
                                        <label for="contact_phone" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                                </svg>
                                                Téléphone de contact
                                            </span>
                                        </label>
                                        <input type="text" 
                                               wire:model="contact_phone" 
                                               id="contact_phone" 
                                               placeholder="Numéro de téléphone"
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                    </div>

                                    <!-- Statut actif -->
                                    <div class="rounded-xl bg-gray-50 p-4">
                                        <div class="flex items-center gap-3">
                                            <input id="is_active" 
                                                   wire:model="is_active" 
                                                   type="checkbox" 
                                                   class="h-5 w-5 rounded-lg border-gray-300 text-blue-600 focus:ring-blue-500 focus:ring-offset-0">
                                            <div class="flex-1">
                                                <label for="is_active" class="block text-sm font-semibold text-gray-900">
                                                    Magasin Actif
                                                </label>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Les magasins inactifs n'apparaîtront pas dans les listes de sélection
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Actions du formulaire -->
                            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                                <div class="flex items-center justify-end gap-3">
                                    <button @click="showForm = false" 
                                            type="button" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2.5 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 hover:ring-gray-400 transition-all duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                                        </svg>
                                        Annuler
                                    </button>
                                    <button type="submit" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-blue-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-all duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $editingStore->exists ? 'Mettre à jour' : 'Créer le magasin' }}
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>