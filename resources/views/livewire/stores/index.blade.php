<div>
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Gestion des Magasins</h2>
            <p class="mt-1 text-sm text-gray-500">Gérez tous vos points de vente depuis une seule interface.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <button wire:click="create" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                <svg class="-ml-0.5 mr-1.5 h-5 w-5" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.75-11.25a.75.75 0 00-1.5 0v2.5h-2.5a.75.75 0 000 1.5h2.5v2.5a.75.75 0 001.5 0v-2.5h2.5a.75.75 0 000-1.5h-2.5v-2.5z" clip-rule="evenodd" /></svg>
                Nouveau Magasin
            </button>
        </div>
    </div>

    <!-- Messages de succès/erreur -->
    @if (session('success'))
        <div class="rounded-md bg-green-50 p-4 mt-4">
            <div class="flex">
                <div class="ml-3"><p class="text-sm font-medium text-green-800">{{ session('success') }}</p></div>
            </div>
        </div>
    @endif
    @if (session('error'))
        <div class="rounded-md bg-red-50 p-4 mt-4">
            <div class="flex">
                <div class="ml-3"><p class="text-sm font-medium text-red-800">{{ session('error') }}</p></div>
            </div>
        </div>
    @endif

    <!-- Tableau des magasins -->
    <div class="mt-8 flow-root">
        <div class="-my-2 -mx-4 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nom</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Adresse</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Ville</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                                <th scope="col" class="px-3 py-3.5 pl-3 pr-4 text-left text-sm font-semibold text-gray-900 sm:pr-6">Actions<span class="sr-only"></span></th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($stores as $store)
                                <tr wire:key="{{ $store->id }}">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                        <div class="font-medium text-gray-900">{{ $store->name }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600">
                                        <div class="text-gray-700">{{ $store->address }}</div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $store->city ?? '-' }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        @if ($store->is_active)
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Actif</span>
                                        @else
                                            <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-0.5 text-xs font-medium text-red-800">Inactif</span>
                                        @endif
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-left text-sm font-medium sm:pr-6">
                                        <a href="#" wire:click.prevent="edit({{ $store->id }})" class="text-indigo-600 hover:text-indigo-900">Modifier<span class="sr-only">, {{ $store->name }}</span></a>
                                        <a href="#" wire:click.prevent="delete({{ $store->id }})" wire:confirm="Êtes-vous sûr de vouloir supprimer ce magasin ?" class="ml-4 text-red-600 hover:text-red-900">Supprimer<span class="sr-only">, {{ $store->name }}</span></a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-10 text-gray-500">
                                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true"><path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" /></svg>
                                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucun magasin</h3>
                                    <p class="mt-1 text-sm text-gray-500">Commencez par créer votre premier point de vente.</p>
                                    <div class="mt-6"><button wire:click="create" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Nouveau Magasin</button></div>
                                </td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
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
                        <form wire:submit.prevent="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll py-6">
                                <div class="px-4 sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <h2 class="text-base font-semibold leading-6 text-gray-900" id="slide-over-title">{{ $editingStore->exists ? 'Modifier le Magasin' : 'Créer un Nouveau Magasin' }}</h2>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button @click="showForm = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"><span class="sr-only">Fermer</span><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative mt-6 flex-1 px-4 sm:px-6">
                                <!-- Contenu du formulaire -->
                                <div class="space-y-6">
                                    <div>
                                        <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom du magasin *</label>
                                        <div class="mt-2"><input type="text" wire:model="name" id="name" class="block w-full rounded-md border-0 px-4 py-3.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></div>
                                        @error('name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                                    </div>
                                    <div>
                                        <label for="address" class="block text-sm font-medium leading-6 text-gray-900">Adresse</label>
                                        <div class="mt-2"><input type="text" wire:model="address" id="address" class="block w-full rounded-md border-0 px-4 py-3.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></div>
                                    </div>
                                    <div>
                                        <label for="city" class="block text-sm font-medium leading-6 text-gray-900">Ville</label>
                                        <div class="mt-2"><input type="text" wire:model="city" id="city" class="block w-full rounded-md border-0 px-4 py-3.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></div>
                                    </div>
                                    <div>
                                        <label for="contact_phone" class="block text-sm font-medium leading-6 text-gray-900">Téléphone de contact</label>
                                        <div class="mt-2"><input type="text" wire:model="contact_phone" id="contact_phone" class="block w-full rounded-md border-0 px-4 py-3.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-indigo-600"></div>
                                    </div>
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center"><input id="is_active" wire:model="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></div>
                                        <div class="ml-3 text-sm leading-6"><label for="is_active" class="font-medium text-gray-900">Magasin Actif</label></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="flex flex-shrink-0 justify-end px-4 py-4">
                                <button @click="showForm = false" type="button" class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</button>
                                <button type="submit" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>