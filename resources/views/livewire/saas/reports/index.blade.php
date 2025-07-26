<div class="space-y-6">
    <!-- En-tête moderne sobre -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Rapports & Exports
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Téléchargez les données de votre entreprise pour votre comptabilité et analyses
                </p>
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

    @if (session('error'))
        <div class="mx-6 rounded-lg bg-red-50 p-4 border border-red-200">
            <div class="flex items-center gap-3">
                <div class="h-5 w-5 text-red-600">
                    <svg fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <p class="text-sm font-medium text-red-800">{{ session('error') }}</p>
            </div>
        </div>
    @endif

    <!-- Grille des rapports disponibles -->
    <div class="mx-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <!-- Carte Journal des Ventes -->
            <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 group hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 rounded-lg bg-emerald-100 p-3 group-hover:bg-emerald-200 transition-colors duration-200">
                        <svg class="h-6 w-6 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-emerald-700 transition-colors duration-200">
                            Journal des Ventes
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Exportez le détail de toutes vos factures et avoirs sur une période donnée pour votre comptabilité.
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <button wire:click="exportSales" 
                            wire:loading.attr="disabled" 
                            wire:target="exportSales" 
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        <svg wire:loading.remove wire:target="exportSales" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <svg wire:loading wire:target="exportSales" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportSales">Télécharger CSV</span>
                        <span wire:loading wire:target="exportSales">Génération en cours...</span>
                    </button>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                    <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
                    <span>Factures, devis, avoirs</span>
                </div>
            </div>

            <!-- Carte État des Stocks -->
            <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 group hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 rounded-lg bg-blue-100 p-3 group-hover:bg-blue-200 transition-colors duration-200">
                        <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-blue-700 transition-colors duration-200">
                            État des Stocks
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Obtenez une photo instantanée de toutes vos quantités en stock et de leur valeur pour inventaire.
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <button wire:click="exportStockState" 
                            wire:loading.attr="disabled" 
                            wire:target="exportStockState" 
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-blue-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        <svg wire:loading.remove wire:target="exportStockState" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <svg wire:loading wire:target="exportStockState" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportStockState">Télécharger CSV</span>
                        <span wire:loading wire:target="exportStockState">Génération en cours...</span>
                    </button>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                    <div class="h-2 w-2 rounded-full bg-blue-400"></div>
                    <span>Quantités, prix, valorisation</span>
                </div>
            </div>

            <!-- Carte Historique des Paiements -->
            <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200 group hover:shadow-md transition-shadow duration-200">
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 rounded-lg bg-purple-100 p-3 group-hover:bg-purple-200 transition-colors duration-200">
                        <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m00v-.75A.75.75 0 013 4.5h.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="text-lg font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200">
                            Historique des Paiements
                        </h3>
                        <p class="mt-2 text-sm text-gray-600">
                            Suivez tous les paiements enregistrés dans l'application sur une période donnée pour votre trésorerie.
                        </p>
                    </div>
                </div>
                <div class="mt-6">
                    <button wire:click="exportPayments" 
                            wire:loading.attr="disabled" 
                            wire:target="exportPayments" 
                            class="w-full inline-flex items-center justify-center gap-2 rounded-lg bg-purple-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors duration-200">
                        <svg wire:loading.remove wire:target="exportPayments" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                        </svg>
                        <svg wire:loading wire:target="exportPayments" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span wire:loading.remove wire:target="exportPayments">Télécharger CSV</span>
                        <span wire:loading wire:target="exportPayments">Génération en cours...</span>
                    </button>
                </div>
                <div class="mt-3 flex items-center gap-2 text-xs text-gray-500">
                    <div class="h-2 w-2 rounded-full bg-purple-400"></div>
                    <span>Encaissements, virements</span>
                </div>
            </div>
        </div>
    </div>
</div>