<form wire:submit.prevent="save">
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900">Numérotation des Documents</h2>
            <p class="mt-1 text-sm text-gray-500">Personnalisez les préfixes de vos documents.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <button type="submit" class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Sauvegarder</button>
        </div>
    </div>
    <div class="mt-10 bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
        <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6">
            <div class="sm:col-span-3">
                <label for="invoice_prefix" class="block text-sm font-medium leading-6 text-gray-900">Préfixe Facture</label>
                <input type="text" wire:model="prefixes.invoice" id="invoice_prefix" class="mt-2 block w-full rounded-md ...">
            </div>
            <div class="sm:col-span-3">
                <label for="quote_prefix" class="block text-sm font-medium leading-6 text-gray-900">Préfixe Devis</label>
                <input type="text" wire:model="prefixes.quote" id="quote_prefix" class="mt-2 block w-full rounded-md ...">
            </div>
            <div class="sm:col-span-3">
                <label for="credit_note_prefix" class="block text-sm font-medium leading-6 text-gray-900">Préfixe Avoir</label>
                <input type="text" wire:model="prefixes.credit_note" id="credit_note_prefix" class="mt-2 block w-full rounded-md ...">
            </div>
            <div class="sm:col-span-3">
                <label for="purchase_order_prefix" class="block text-sm font-medium leading-6 text-gray-900">Préfixe Bon de Commande</label>
                <input type="text" wire:model="prefixes.purchase_order" id="purchase_order_prefix" class="mt-2 block w-full rounded-md ...">
            </div>
        </div>
    </div>
</form>