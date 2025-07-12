<div x-data="{ showForm: @entangle('showForm') }">
    <!-- CORRECTION : Le scope d'AlpineJS englobe maintenant toute la page -->

    <!-- Panneau latéral (Slide-over) pour le formulaire -->
    <div x-show="showForm" x-cloak class="relative z-10" aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
        <div x-show="showForm" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showForm = false" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showForm" x-transition:enter="transform transition ease-in-out duration-500" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="pointer-events-auto w-screen max-w-md">
                        <form wire:submit.prevent="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll py-6">
                                <div class="px-4 sm:px-6">
                                    <div class="flex items-start justify-between">
                                        <h2 class="text-base font-semibold leading-6 text-gray-900">{{ $editingCustomer->exists ? 'Modifier le Client' : 'Nouveau Client' }}</h2>
                                        <div class="ml-3 flex h-7 items-center">
                                            <button type="button" @click="showForm = false" class="rounded-md bg-white text-gray-400 hover:text-gray-500 focus:outline-none focus:ring-2 focus:ring-indigo-500"><span class="sr-only">Fermer</span><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button>
                                        </div>
                                    </div>
                                </div>
                                <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-6">
                                    <div><label for="name" class="block text-sm font-medium text-gray-900">Nom complet *</label><input type="text" wire:model="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="email" class="block text-sm font-medium text-gray-900">Email</label><input type="email" wire:model="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="phone_number" class="block text-sm font-medium text-gray-900">Téléphone</label><input type="text" wire:model="phone_number" id="phone_number" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></div>
                                    <div><label for="address" class="block text-sm font-medium text-gray-900">Adresse</label><textarea wire:model="address" id="address" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea></div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-900">Type de client</label>
                                        <fieldset class="mt-2"><div class="space-y-4 sm:flex sm:items-center sm:space-y-0 sm:space-x-10">
                                            <div class="flex items-center"><input wire:model="type" id="individual" value="individual" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"><label for="individual" class="ml-3 block text-sm font-medium text-gray-900">Particulier</label></div>
                                            <div class="flex items-center"><input wire:model="type" id="professional" value="professional" type="radio" class="h-4 w-4 border-gray-300 text-indigo-600 focus:ring-indigo-600"><label for="professional" class="ml-3 block text-sm font-medium text-gray-900">Professionnel</label></div>
                                        </div></fieldset>
                                    </div>
                                </div>
                            </div>
                            <div class="flex flex-shrink-0 justify-end px-4 py-4">
                                <button type="button" @click="showForm = false" class="rounded-md bg-white py-2 px-3 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Annuler</button>
                                <button type="submit" class="ml-4 inline-flex justify-center rounded-md bg-indigo-600 py-2 px-3 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Enregistrer</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Vue de la liste des clients -->
    <div>
        <div class="sm:flex sm:items-center sm:justify-between">
            <div class="min-w-0 flex-1">
                <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Clients</h2>
                <p class="mt-1 text-sm text-gray-500">Gérez votre base de données clients.</p>
            </div>
            <div class="mt-5 flex sm:mt-0 sm:ml-4">
                <button wire:click="create" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Nouveau Client</button>
            </div>
        </div>
        <div class="mt-6"><input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher un client..." class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"></div>
        <div class="mt-8 flow-root">
            <div class="inline-block min-w-full py-2 align-middle sm:px-1">
                <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                    <table class="min-w-full divide-y divide-gray-300">
                        <thead class="bg-gray-50"><tr>
                            <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nom</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Contact</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Type</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-4 font-semibold text-gray-900 sm:pr-6">Actions</th></tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 bg-white">
                            @forelse ($customers as $customer)
                                <tr wire:key="{{ $customer->id }}">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $customer->name }}</td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><div>{{ $customer->email }}</div><div>{{ $customer->phone_number }}</div></td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $customer->type == App\Enums\CustomerType::Professional ? 'bg-blue-100 text-blue-700' : 'bg-green-100 text-green-700' }}">{{ $customer->type->value == 'professional' ? 'Professionnel' : 'individual' }}</span></td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <button wire:click="edit({{ $customer->id }})" class="text-indigo-600 hover:text-indigo-900">Modifier</button>
                                        <button wire:click="delete({{ $customer->id }})" wire:confirm="Êtes-vous sûr ?" class="ml-4 text-red-600 hover:text-red-900">Supprimer</button>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="text-center py-10 text-gray-500">Aucun client trouvé.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                <div class="mt-4">{{ $customers->links() }}</div>
            </div>
        </div>
    </div>
</div>