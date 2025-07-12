<div>
    <!-- En-tête avec filtres -->
    <div class="border-b border-gray-200 pb-5 sm:flex sm:items-center sm:justify-between">
        <h3 class="text-2xl font-bold leading-6 text-gray-900">
            Tableau de Bord
        </h3>
        <div class="mt-3 sm:mt-0 sm:ml-4">
            <div x-data="{ open: false }" class="relative inline-block text-left">
                <div>
                    <button @click="open = !open" type="button" class="inline-flex w-full justify-center gap-x-1.5 rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50" id="menu-button" aria-expanded="true" aria-haspopup="true">
                        Derniers {{ $period }} jours
                        <svg class="-mr-1 h-5 w-5 text-gray-400" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                            <path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 10.94l3.71-3.71a.75.75 0 111.06 1.06l-4.25 4.25a.75.75 0 01-1.06 0L5.23 8.27a.75.75 0 01.02-1.06z" clip-rule="evenodd" />
                        </svg>
                    </button>
                </div>
                <div x-show="open" @click.away="open = false" x-transition class="absolute right-0 z-10 mt-2 w-56 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" role="menu" aria-orientation="vertical" aria-labelledby="menu-button" tabindex="-1">
                    <div class="py-1" role="none">
                        <a href="#" wire:click.prevent="setPeriod('7')" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-0">7 derniers jours</a>
                        <a href="#" wire:click.prevent="setPeriod('30')" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-1">30 derniers jours</a>
                        <a href="#" wire:click.prevent="setPeriod('90')" class="text-gray-700 block px-4 py-2 text-sm" role="menuitem" tabindex="-1" id="menu-item-2">90 derniers jours</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Grille des KPIs (Stat Cards) -->
    <!-- Grille des KPIs (Stat Cards) -->
    <div class="mt-6">
        <dl class="grid grid-cols-1 gap-5 sm:grid-cols-2 lg:grid-cols-4">
            <!-- KPI Chiffre d'Affaires -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 pt-5 pb-8 shadow sm:px-6 sm:pt-6">
                <dt><div class="absolute rounded-md bg-indigo-500 p-3"><svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0l.879-.659M7.5 14.25l-2.489-1.867a.75.75 0 01.3-1.382l4.12 2.355M16.5 14.25l2.489-1.867a.75.75 0 00-.3-1.382l-4.12 2.355"/></svg></div><p class="ml-16 truncate text-sm font-medium text-gray-500">Chiffre d'Affaires</p></dt>
                <dd class="ml-16 flex items-baseline"><p class="text-2xl font-semibold text-gray-900">{{ number_format($totalRevenue, 0, ',', ' ') }} XAF</p></dd>
            </div>
             <!-- KPI Bénéfice Estimé -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 pt-5 pb-8 shadow sm:px-6 sm:pt-6">
                <dt><div class="absolute rounded-md bg-green-500 p-3"><svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m00v-.75A.75.75 0 013 4.5h.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div><p class="ml-16 truncate text-sm font-medium text-gray-500">Bénéfice Estimé</p></dt>
                <dd class="ml-16 flex items-baseline"><p class="text-2xl font-semibold text-gray-900">{{ number_format($estimatedProfit, 0, ',', ' ') }} XAF</p></dd>
            </div>
            <!-- KPI Total des Ventes -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 pt-5 pb-8 shadow sm:px-6 sm:pt-6">
                <dt><div class="absolute rounded-md bg-blue-500 p-3"><svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.117 1.243H4.252c-.654 0-1.187-.585-1.117-1.243l1.263-12A3.75 3.75 0 017.5 4.5h9a3.75 3.75 0 013.75 3.75V8.517z"/></svg></div><p class="ml-16 truncate text-sm font-medium text-gray-500">Total des Ventes</p></dt>
                <dd class="ml-16 flex items-baseline"><p class="text-2xl font-semibold text-gray-900">{{ $totalSales }}</p></dd>
            </div>
             <!-- KPI Nouveaux Clients -->
            <div class="relative overflow-hidden rounded-lg bg-white px-4 pt-5 pb-8 shadow sm:px-6 sm:pt-6">
                <dt><div class="absolute rounded-md bg-orange-500 p-3"><svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.5 21c-2.331 0-4.512-.645-6.374-1.766z"/></svg></div><p class="ml-16 truncate text-sm font-medium text-gray-500">Nouveaux Clients</p></dt>
                <dd class="ml-16 flex items-baseline"><p class="text-2xl font-semibold text-gray-900">+{{ $newCustomersCount }}</p></dd>
            </div>
        </dl>
    </div>

    <!-- Grille principale : Graphiques et Listes -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-3">
        <!-- Colonne de gauche (plus grande) -->
        <div class="col-span-1 lg:col-span-2">
            <div class="rounded-lg bg-white p-4 shadow h-96"><canvas id="salesChart"></canvas></div>
        </div>

        <!-- Colonne de droite -->
        <div class="col-span-1 space-y-6">
            <!-- Ventes par Catégorie (Donut Chart) -->
            <div class="rounded-lg bg-white p-4 shadow">
                 <h3 class="text-base font-medium text-gray-900">Ventes par Catégorie</h3>
                 <div class="mt-4 h-64"><canvas id="categoryDonutChart"></canvas></div>
            </div>

            <!-- MODIFICATION : Carte à onglets pour les listes -->
            <div class="rounded-lg bg-white p-6 shadow" x-data="{ activeTab: 'top-products' }">
                <div class="border-b border-gray-200">
                    <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                        <a href="#" @click.prevent="activeTab = 'top-products'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'top-products', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'top-products' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Top Produits</a>
                        <a href="#" @click.prevent="activeTab = 'low-stock'" :class="{ 'border-indigo-500 text-indigo-600': activeTab === 'low-stock', 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300': activeTab !== 'low-stock' }" class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm">Stock Bas</a>
                    </nav>
                </div>
                <!-- Contenu des onglets -->
                <div class="mt-4">
                    <div x-show="activeTab === 'top-products'" x-transition>
                        <ul role="list" class="divide-y divide-gray-200">
                            @forelse ($topProducts as $product)
                                <li class="py-3 flex justify-between items-center">
                                    <div class="truncate">
                                        <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <p class="text-sm font-semibold text-gray-900">{{ (int)$product->total_quantity }} vendus</p>
                                </li>
                            @empty
                                 <li class="py-3 text-center text-sm text-gray-500">Aucune vente enregistrée.</li>
                            @endforelse
                        </ul>
                    </div>
                    <div x-show="activeTab === 'low-stock'" x-transition x-cloak>
                         <ul role="list" class="divide-y divide-gray-200">
                            @forelse ($lowStockProducts as $product)
                                <li class="py-3">
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    @foreach($product->stores as $store)
                                        <div class="flex justify-between items-center text-sm text-gray-500 pl-2">
                                            <span>{{ $store->name }}:</span>
                                            <span class="font-semibold text-red-600">{{ $store->pivot->quantity }} en stock</span>
                                        </div>
                                    @endforeach
                                </li>
                            @empty
                                 <li class="py-3 text-center text-sm text-gray-500">Aucun produit en stock bas.</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
    @script
    <script>
        function dashboardCharts(lineLabels, lineValues, donutLabels, donutValues) {
            return {
                lineChart: null, donutChart: null,
                initCharts() {
                    this.lineChart = new Chart(document.getElementById('salesChart'), { type: 'line', data: { labels: lineLabels, datasets: [{ label: 'Chiffre d\'Affaires', data: lineValues, borderColor: 'rgb(79, 70, 229)', backgroundColor: 'rgba(79, 70, 229, 0.1)', fill: true, tension: 0.4 }] }, options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } } });
                    this.donutChart = new Chart(document.getElementById('categoryDonutChart'), { type: 'doughnut', data: { labels: donutLabels, datasets: [{ label: 'Ventes', data: donutValues, backgroundColor: ['#4f46e5', '#6d28d9', '#a78bfa', '#c4b5fd', '#e0e7ff'], hoverOffset: 4 }] }, options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } } });
                    Livewire.on('update-charts', (event) => { this.updateCharts(event[0]); });
                },
                updateCharts(data) {
                    this.lineChart.data.labels = data.lineLabels; this.lineChart.data.datasets[0].data = data.lineValues; this.lineChart.update();
                    this.donutChart.data.labels = data.donutLabels; this.donutChart.data.datasets[0].data = data.donutValues; this.donutChart.update();
                }
            }
        }
        const salesChartCtx = document.getElementById('salesChart').getContext('2d');
        const salesChart = new Chart(salesChartCtx, {
            type: 'line',
            data: {
                labels: @json($lineChartLabels),
                datasets: [{ label: 'Chiffre d\'Affaires', data: @json($lineChartValues), borderColor: 'rgb(79, 70, 229)', backgroundColor: 'rgba(79, 70, 229, 0.1)', fill: true, tension: 0.4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, scales: { y: { beginAtZero: true } }, plugins: { legend: { display: false } } }
        });
        
        const donutChartCtx = document.getElementById('categoryDonutChart').getContext('2d');
        const donutChart = new Chart(donutChartCtx, {
            type: 'doughnut',
            data: {
                labels: @json($donutChartLabels),
                datasets: [{ label: 'Ventes', data: @json($donutChartValues), backgroundColor: ['#4f46e5', '#6d28d9', '#a78bfa', '#c4b5fd', '#e0e7ff'], hoverOffset: 4 }]
            },
            options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
        });

        // On écoute l'événement envoyé par Livewire
        Livewire.on('update-charts', (event) => {
            // Mise à jour du graphique linéaire
            salesChart.data.labels = event[0].lineLabels;
            salesChart.data.datasets[0].data = event[0].lineValues;
            salesChart.update();

            // Mise à jour du graphique donut
            donutChart.data.labels = event[0].donutLabels;
            donutChart.data.datasets[0].data = event[0].donutValues;
            donutChart.update();
        });
    </script>
    @endscript
</div>  
