<div class="space-y-8">
    <!-- En-tête moderne avec gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-500 via-red-600 to-pink-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-orange-500/20 to-pink-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Gestion des Fournisseurs
                    </h1>
                    <p class="text-orange-100 text-lg">
                        Gérez votre réseau de fournisseurs et partenaires commerciaux
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <button wire:click="create" 
                            type="button" 
                            class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                        </svg>
                        Nouveau Fournisseur
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

    <!-- Tableau moderne des fournisseurs -->
    <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
        <!-- En-tête du tableau -->
        <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">Liste des Fournisseurs</h3>
                    <p class="text-sm text-gray-600">{{ $suppliers->total() }} fournisseur(s) au total</p>
                </div>
                <div class="flex items-center gap-2 text-sm text-gray-500">
                    <div class="h-3 w-3 rounded-full bg-orange-500"></div>
                    <span>Réseau fournisseurs</span>
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
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                </svg>
                                Fournisseur
                            </div>
                        </th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Contact</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Informations</th>
                        <th scope="col" class="px-3 py-4 text-left text-sm font-semibold text-gray-700">Statut</th>
                        <th scope="col" class="relative py-4 pl-3 pr-6">
                            <span class="sr-only">Actions</span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50 bg-white">
                    @forelse ($suppliers as $supplier)
                        <tr wire:key="{{ $supplier->id }}" class="group hover:bg-gradient-to-r hover:from-orange-50/50 hover:to-red-50/50 transition-all duration-200">
                            <td class="py-4 pl-6 pr-3">
                                <div class="flex items-center gap-4">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-orange-100 to-red-200 flex items-center justify-center shadow-sm">
                                        <svg class="h-6 w-6 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                        </svg>
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm font-semibold text-gray-900 group-hover:text-orange-700 transition-colors duration-200">
                                            {{ $supplier->name }}
                                        </p>
                                        @if($supplier->contact_person)
                                            <p class="text-xs text-gray-500 mt-1">
                                                Contact: {{ $supplier->contact_person }}
                                            </p>
                                        @else
                                            <p class="text-xs text-gray-500 mt-1">
                                                ID: {{ $supplier->id }}
                                            </p>
                                        @endif
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="space-y-1">
                                    @if($supplier->email)
                                        <div class="flex items-center gap-2">
                                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0021.75 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75z" />
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ $supplier->email }}</span>
                                        </div>
                                    @endif
                                    @if($supplier->phone_number)
                                        <div class="flex items-center gap-2">
                                            <svg class="h-3 w-3 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                            </svg>
                                            <span class="text-sm text-gray-700">{{ $supplier->phone_number }}</span>
                                        </div>
                                    @endif
                                    @if(!$supplier->email && !$supplier->phone_number)
                                        <span class="text-gray-400 text-xs">Contact non renseigné</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                <div class="space-y-1">
                                    @if($supplier->nif)
                                        <div class="text-xs">
                                            <span class="text-gray-500">NIF:</span>
                                            <code class="bg-gray-100 text-gray-800 px-1 rounded text-xs">{{ $supplier->nif }}</code>
                                        </div>
                                    @endif
                                    @if($supplier->rccm)
                                        <div class="text-xs">
                                            <span class="text-gray-500">RCCM:</span>
                                            <code class="bg-gray-100 text-gray-800 px-1 rounded text-xs">{{ $supplier->rccm }}</code>
                                        </div>
                                    @endif
                                    @if($supplier->address)
                                        <div class="text-xs text-gray-600">
                                            {{ Str::limit($supplier->address, 30) }}
                                        </div>
                                    @endif
                                    @if(!$supplier->nif && !$supplier->rccm && !$supplier->address)
                                        <span class="text-gray-400 text-xs">Informations incomplètes</span>
                                    @endif
                                </div>
                            </td>
                            <td class="px-3 py-4">
                                @if ($supplier->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-50 px-3 py-1 text-xs font-semibold text-green-700 ring-1 ring-green-600/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                        Actif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-gray-50 px-3 py-1 text-xs font-semibold text-gray-700 ring-1 ring-gray-600/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-gray-500"></div>
                                        Inactif
                                    </span>
                                @endif
                            </td>
                            <td class="relative py-4 pl-3 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="edit({{ $supplier->id }})" 
                                           class="inline-flex items-center gap-1 rounded-lg bg-orange-50 px-3 py-2 text-xs font-medium text-orange-700 hover:bg-orange-100 transition-colors duration-200">
                                        <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                        </svg>
                                        Modifier
                                    </button>
                                    <button wire:click="delete({{ $supplier->id }})" 
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer ce fournisseur ?" 
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
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-lg font-medium text-gray-900">Aucun fournisseur trouvé</p>
                                        <p class="text-sm text-gray-500 mt-1">Commencez par ajouter votre premier fournisseur</p>
                                    </div>
                                    <button wire:click="create" 
                                            class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 transition-colors duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                        </svg>
                                        Nouveau Fournisseur
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination moderne -->
        @if($suppliers->hasPages())
            <div class="border-t border-gray-100 bg-gray-50/50 px-6 py-4">
                {{ $suppliers->links() }}
            </div>
        @endif
    </div>
    
    <!-- Panneau latéral (Slide-over) pour Créer/Modifier -->
    <div 
        x-data="{ showForm: @entangle('showForm') }"
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
                    <div x-show="showForm" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-lg">
                        <form wire:submit.prevent="save" class="flex h-full flex-col bg-white shadow-xl">
                            <!-- En-tête moderne du formulaire -->
                            <div class="border-b border-gray-100 bg-gradient-to-r from-orange-50 to-red-50 px-6 py-6">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-orange-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-orange-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h2 class="text-lg font-semibold text-gray-900" id="slide-over-title">
                                                {{ $editingSupplier->exists ? 'Modifier le Fournisseur' : 'Créer un Nouveau Fournisseur' }}
                                            </h2>
                                            <p class="text-sm text-gray-600">
                                                {{ $editingSupplier->exists ? 'Mettre à jour les informations du fournisseur' : 'Ajouter un nouveau partenaire commercial' }}
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
                                    <!-- Nom de l'entreprise -->
                                    <div>
                                        <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                                </svg>
                                                Nom de l'entreprise *
                                            </span>
                                        </label>
                                        <input type="text" 
                                               wire:model="name" 
                                               id="name" 
                                               placeholder="Nom de l'entreprise fournisseur"
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                        @error('name') 
                                            <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                </svg>
                                                {{ $message }}
                                            </div>
                                        @enderror
                                    </div>

                                    <!-- Personne à contacter -->
                                    <div>
                                        <label for="contact_person" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                                                </svg>
                                                Personne à contacter
                                            </span>
                                        </label>
                                        <input type="text" 
                                               wire:model="contact_person" 
                                               id="contact_person" 
                                               placeholder="Nom du contact principal"
                                               class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                    </div>

                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <!-- Email -->
                                        <div>
                                            <label for="email" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <span class="flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0021.75 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75z" />
                                                    </svg>
                                                    Email
                                                </span>
                                            </label>
                                            <input type="email" 
                                                   wire:model="email" 
                                                   id="email" 
                                                   placeholder="email@entreprise.com"
                                                   class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                            @error('email') 
                                                <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                                    </svg>
                                                    {{ $message }}
                                                </div>
                                            @enderror
                                        </div>

                                        <!-- Téléphone -->
                                        <div>
                                            <label for="phone_number" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <span class="flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                                    </svg>
                                                    Téléphone
                                                </span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="phone_number" 
                                                   id="phone_number" 
                                                   placeholder="+237 6XX XX XX XX"
                                                   class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                        </div>
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
                                        <textarea wire:model="address" 
                                                  id="address" 
                                                  rows="2" 
                                                  placeholder="Adresse complète du fournisseur"
                                                  class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                                    </div>

                                    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                                        <!-- NIF -->
                                        <div>
                                            <label for="nif" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <span class="flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                                    </svg>
                                                    NIF
                                                </span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="nif" 
                                                   id="nif" 
                                                   placeholder="Numéro d'Identification Fiscale"
                                                   class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                        </div>

                                        <!-- RCCM -->
                                        <div>
                                            <label for="rccm" class="block text-sm font-semibold text-gray-700 mb-2">
                                                <span class="flex items-center gap-2">
                                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                                    </svg>
                                                    RCCM
                                                </span>
                                            </label>
                                            <input type="text" 
                                                   wire:model="rccm" 
                                                   id="rccm" 
                                                   placeholder="Registre du Commerce"
                                                   class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200">
                                        </div>
                                    </div>

                                    <!-- Notes -->
                                    <div>
                                        <label for="notes" class="block text-sm font-semibold text-gray-700 mb-2">
                                            <span class="flex items-center gap-2">
                                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                                </svg>
                                                Notes
                                            </span>
                                        </label>
                                        <textarea wire:model="notes" 
                                                  id="notes" 
                                                  rows="3" 
                                                  placeholder="Notes sur le fournisseur, conditions de livraison, etc."
                                                  class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-orange-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                                    </div>

                                    <!-- Statut actif -->
                                    <div class="rounded-xl bg-gray-50 p-4">
                                        <div class="flex items-center gap-3">
                                            <input id="is_active" 
                                                   wire:model="is_active" 
                                                   type="checkbox" 
                                                   class="h-5 w-5 rounded-lg border-gray-300 text-orange-600 focus:ring-orange-500 focus:ring-offset-0">
                                            <div class="flex-1">
                                                <label for="is_active" class="block text-sm font-semibold text-gray-900">
                                                    Fournisseur Actif
                                                </label>
                                                <p class="text-xs text-gray-600 mt-1">
                                                    Les fournisseurs inactifs n'apparaîtront pas dans les listes de sélection
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
                                            class="inline-flex items-center gap-2 rounded-xl bg-orange-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-orange-500 focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 transition-all duration-200">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $editingSupplier->exists ? 'Mettre à jour' : 'Créer le fournisseur' }}
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