<div x-data="{ showForm: @entangle('showForm') }">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Fournisseurs</h2>
            <p class="mt-1 text-sm text-gray-500">Gérez la liste de vos fournisseurs.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <button wire:click="create" type="button" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nouveau Fournisseur
            </button>
        </div>
    </div>
    
    <!-- Tableau -->
    <div class="mt-8 flow-root">
        <div class="inline-block min-w-full py-2 align-middle sm:px-1">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50"><tr><th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Nom</th><th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Contact</th><th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th><th class="relative py-3.5 pl-3 pr-4 sm:pr-6"></th></tr></thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($suppliers as $supplier)
                            <tr wire:key="{{ $supplier->id }}">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ $supplier->name }}</div>
                                    <div class="text-gray-500">{{ $supplier->contact_person }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><div>{{ $supplier->email }}</div><div>{{ $supplier->phone_number }}</div></td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @if ($supplier->is_active)
                                        <span class="inline-flex items-center rounded-full bg-green-100 px-2.5 py-0.5 text-xs font-medium text-green-800">Actif</span>
                                    @else
                                        <span class="inline-flex items-center rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-800">Inactif</span>
                                    @endif
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <button wire:click="edit({{ $supplier->id }})" class="text-indigo-600 hover:text-indigo-900">Modifier</button>
                                    <button wire:click="delete({{ $supplier->id }})" wire:confirm="Êtes-vous sûr ?" class="ml-4 text-red-600 hover:text-red-900">Supprimer</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="text-center py-10 text-gray-500">Aucun fournisseur trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $suppliers->links() }}</div>
        </div>
    </div>
    
    <!-- Panneau latéral pour le formulaire -->
    <div x-show="showForm" x-cloak class="relative z-10">
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>
        <div class="fixed inset-0 overflow-hidden">
            <div class="absolute inset-0 overflow-hidden">
                <div @click.away="showForm = false" class="pointer-events-none fixed inset-y-0 right-0 flex max-w-full pl-10">
                    <div x-show="showForm" x-transition class="pointer-events-auto w-screen max-w-md">
                        <form wire:submit.prevent="save" class="flex h-full flex-col divide-y divide-gray-200 bg-white shadow-xl">
                            <div class="flex min-h-0 flex-1 flex-col overflow-y-scroll py-6">
                                <div class="px-4 sm:px-6"><div class="flex items-start justify-between"><h2 class="text-base font-semibold leading-6 text-gray-900">{{ $editingSupplier->exists ? 'Modifier le Fournisseur' : 'Nouveau Fournisseur' }}</h2><div class="ml-3 flex h-7 items-center"><button @click="showForm = false" type="button" class="rounded-md bg-white text-gray-400 hover:text-gray-500"><svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path d="M6 18L18 6M6 6l12 12" /></svg></button></div></div></div>
                                <div class="relative mt-6 flex-1 px-4 sm:px-6 space-y-6">
                                    <div><label for="name" class="block text-sm font-medium text-gray-900">Nom de l'entreprise *</label><input type="text" wire:model="name" id="name" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('name')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="contact_person" class="block text-sm font-medium text-gray-900">Personne à contacter</label><input type="text" wire:model="contact_person" id="contact_person" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></div>
                                    <div><label for="email" class="block text-sm font-medium text-gray-900">Email</label><input type="email" wire:model="email" id="email" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">@error('email')<span class="text-red-500 text-xs mt-1">{{ $message }}</span>@enderror</div>
                                    <div><label for="phone_number" class="block text-sm font-medium text-gray-900">Téléphone</label><input type="text" wire:model="phone_number" id="phone_number" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></div>
                                    <div><label for="address" class="block text-sm font-medium text-gray-900">Adresse</label><textarea wire:model="address" id="address" rows="2" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea></div>
                                    <div><label for="nif" class="block text-sm font-medium text-gray-900">NIF (Numéro d'Identification Fiscale)</label><input type="text" wire:model="nif" id="nif" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></div>
                                    <div><label for="rccm" class="block text-sm font-medium text-gray-900">RCCM</label><input type="text" wire:model="rccm" id="rccm" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></div>
                                    <div><label for="notes" class="block text-sm font-medium text-gray-900">Notes</label><textarea wire:model="notes" id="notes" rows="3" class="block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea></div>
                                    <div class="relative flex items-start">
                                        <div class="flex h-6 items-center"><input id="is_active" wire:model="is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></div>
                                        <div class="ml-3 text-sm leading-6"><label for="is_active" class="font-medium text-gray-900">Fournisseur Actif</label></div>
                                    </div>
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