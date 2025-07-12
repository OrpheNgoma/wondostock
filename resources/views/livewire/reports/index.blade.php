<div>
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Rapports & Exports</h2>
            <p class="mt-1 text-sm leading-6 text-gray-500">Téléchargez les données de votre entreprise pour votre comptabilité et vos analyses.</p>
        </div>
    </div>

    <!-- Grille des rapports disponibles -->
    <div class="mt-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <!-- Carte Journal des Ventes -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-x-4">
                <div class="flex-shrink-0 rounded-lg bg-indigo-50 p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Journal des Ventes</h3>
            </div>
            <p class="mt-4 text-sm text-gray-500">Exportez le détail de toutes vos factures et avoirs sur une période donnée.</p>
            <div class="mt-6">
                <button wire:click="exportSales" wire:loading.attr="disabled" wire:target="exportSales" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="exportSales">Télécharger (.csv)</span>
                    <span wire:loading wire:target="exportSales">Génération...</span>
                </button>
            </div>
        </div>

        <!-- Carte État des Stocks -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-x-4">
                <div class="flex-shrink-0 rounded-lg bg-indigo-50 p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" /></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">État des Stocks</h3>
            </div>
            <p class="mt-4 text-sm text-gray-500">Obtenez une photo instantanée de toutes vos quantités en stock et de leur valeur.</p>
            <div class="mt-6">
                 <button wire:click="exportStockState" wire:loading.attr="disabled" wire:target="exportStockState" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="exportStockState">Télécharger (.csv)</span>
                    <span wire:loading wire:target="exportStockState">Génération...</span>
                </button>
            </div>
        </div>

        <!-- Carte Historique des Paiements -->
        <div class="rounded-xl bg-white p-6 shadow-sm ring-1 ring-gray-900/5">
            <div class="flex items-center gap-x-4">
                <div class="flex-shrink-0 rounded-lg bg-indigo-50 p-3">
                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m00v-.75A.75.75 0 013 4.5h.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">Historique des Paiements</h3>
            </div>
            <p class="mt-4 text-sm text-gray-500">Suivez tous les paiements enregistrés dans l'application sur une période donnée.</p>
            <div class="mt-6">
                 <button wire:click="exportPayments" wire:loading.attr="disabled" wire:target="exportPayments" class="w-full rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 disabled:opacity-50">
                    <span wire:loading.remove wire:target="exportPayments">Télécharger (.csv)</span>
                    <span wire:loading wire:target="exportPayments">Génération...</span>
                </button>
            </div>
        </div>
    </div>
</div>