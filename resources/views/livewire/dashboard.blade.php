<div class="space-y-8">
    <!-- En-tête moderne avec gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-teal-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        Tableau de Bord
                    </h1>
                    <p class="text-emerald-100 text-lg">
                        Vue d'ensemble de votre activité
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" 
                                type="button" 
                                class="inline-flex items-center gap-x-2 rounded-xl bg-white/10 backdrop-blur-sm px-4 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200" 
                                id="menu-button" 
                                aria-expanded="true" 
                                aria-haspopup="true">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5a2.25 2.25 0 002.25-2.25m-18 0v-7.5A2.25 2.25 0 005.25 9h13.5a2.25 2.25 0 002.25 2.25v7.5" />
                            </svg>
                            Derniers {{ $period }} jours
                            <svg class="h-4 w-4 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                            </svg>
                        </button>
                        <div x-show="open" 
                             @click.away="open = false" 
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 z-10 mt-2 w-64 origin-top-right rounded-xl bg-white shadow-xl ring-1 ring-black/5 backdrop-blur-lg" 
                             role="menu" 
                             aria-orientation="vertical" 
                             aria-labelledby="menu-button" 
                             tabindex="-1"
                             style="display: none;">
                            <div class="p-2" role="none">
                                <a href="#" wire:click.prevent="setPeriod('7')" 
                                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200" 
                                   role="menuitem">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                    7 derniers jours
                                </a>
                                <a href="#" wire:click.prevent="setPeriod('30')" 
                                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200" 
                                   role="menuitem">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                    30 derniers jours
                                </a>
                                <a href="#" wire:click.prevent="setPeriod('90')" 
                                   class="flex items-center gap-3 rounded-lg px-4 py-3 text-sm font-medium text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-all duration-200" 
                                   role="menuitem">
                                    <div class="h-2 w-2 rounded-full bg-emerald-500"></div>
                                    90 derniers jours
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
    </div>

    <!-- Grille moderne des KPIs -->
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <!-- KPI Chiffre d'Affaires -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-blue-50 to-blue-100 p-6 shadow-sm border border-blue-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-blue-600 mb-1">Chiffre d'Affaires</p>
                    <p class="text-2xl font-bold text-blue-900">{{ number_format($totalRevenue, 0, ',', ' ') }}</p>
                    <p class="text-xs text-blue-600 mt-1">XAF</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-blue-500 shadow-lg group-hover:bg-blue-600 transition-colors duration-200">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0l.879-.659M7.5 14.25l-2.489-1.867a.75.75 0 01.3-1.382l4.12 2.355M16.5 14.25l2.489-1.867a.75.75 0 00-.3-1.382l-4.12 2.355" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-blue-400 to-blue-600"></div>
        </div>

        <!-- KPI Bénéfice Estimé -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-50 to-emerald-100 p-6 shadow-sm border border-emerald-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-emerald-600 mb-1">Bénéfice Estimé</p>
                    <p class="text-2xl font-bold text-emerald-900">{{ number_format($estimatedProfit, 0, ',', ' ') }}</p>
                    <p class="text-xs text-emerald-600 mt-1">XAF</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-500 shadow-lg group-hover:bg-emerald-600 transition-colors duration-200">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.75A.75.75 0 013 4.5h.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-emerald-400 to-emerald-600"></div>
        </div>

        <!-- KPI Total des Ventes -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-purple-50 to-purple-100 p-6 shadow-sm border border-purple-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-purple-600 mb-1">Total des Ventes</p>
                    <p class="text-2xl font-bold text-purple-900">{{ $totalSales }}</p>
                    <p class="text-xs text-purple-600 mt-1">commandes</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-500 shadow-lg group-hover:bg-purple-600 transition-colors duration-200">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.658-.463 1.243-1.117 1.243H4.252c-.654 0-1.187-.585-1.117-1.243l1.263-12A3.75 3.75 0 017.5 4.5h9a3.75 3.75 0 013.75 3.75V8.517z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-purple-400 to-purple-600"></div>
        </div>

        <!-- KPI Nouveaux Clients -->
        <div class="group relative overflow-hidden rounded-2xl bg-gradient-to-br from-orange-50 to-orange-100 p-6 shadow-sm border border-orange-200/50 hover:shadow-lg hover:scale-105 transition-all duration-300">
            <div class="flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-sm font-medium text-orange-600 mb-1">Nouveaux Clients</p>
                    <p class="text-2xl font-bold text-orange-900">+{{ $newCustomersCount }}</p>
                    <p class="text-xs text-orange-600 mt-1">ce mois</p>
                </div>
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-orange-500 shadow-lg group-hover:bg-orange-600 transition-colors duration-200">
                    <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM4 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 0110.5 21c-2.331 0-4.512-.645-6.374-1.766z" />
                    </svg>
                </div>
            </div>
            <div class="absolute bottom-0 left-0 h-1 w-full bg-gradient-to-r from-orange-400 to-orange-600"></div>
        </div>
    </div>

    <!-- Section graphiques et analyses modernes -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Graphique principal des ventes -->
        <div class="lg:col-span-8">
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Évolution du Chiffre d'Affaires</h3>
                            <p class="text-sm text-gray-600 mt-1">Tendance sur la période sélectionnée</p>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-gray-500">
                            <div class="h-3 w-3 rounded-full bg-emerald-500"></div>
                            <span>Revenus</span>
                        </div>
                    </div>
                </div>
                <div class="p-6">
                    <div class="h-80">
                        <canvas id="salesChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Colonne de droite avec graphique donut et listes -->
        <div class="lg:col-span-4 space-y-6">
            <!-- Ventes par Catégorie moderne -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-lg transition-shadow duration-300">
                <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white p-6">
                    <h3 class="text-lg font-semibold text-gray-900">Ventes par Catégorie</h3>
                    <p class="text-sm text-gray-600 mt-1">Répartition des ventes</p>
                </div>
                <div class="p-6">
                    <div class="h-64">
                        <canvas id="categoryDonutChart" class="w-full h-full"></canvas>
                    </div>
                </div>
            </div>

            <!-- Onglets modernes pour listes -->
            <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-lg transition-shadow duration-300" x-data="{ activeTab: 'top-products' }">
                <div class="border-b border-gray-100">
                    <nav class="flex bg-gray-50/50" aria-label="Tabs">
                        <button @click="activeTab = 'top-products'" 
                               :class="{ 
                                   'bg-white shadow-sm border-b-2 border-emerald-500 text-emerald-600': activeTab === 'top-products', 
                                   'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'top-products' 
                               }" 
                               class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 border-b-2 border-transparent">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 18.75h-9m9 0a3 3 0 013 3h-15a3 3 0 013-3m9 0v-3.375c0-.621-.503-1.125-1.125-1.125h-.871M7.5 18.75v-3.375c0-.621.504-1.125 1.125-1.125h.872m5.007 0H9.497m5.007 0a7.454 7.454 0 01-.982-3.172M9.497 14.25a7.454 7.454 0 00.981-3.172M15.75 4.5c0-1.11-.893-2.007-2.007-2.007H10.257c-1.114 0-2.007.898-2.007 2.007v1.68c0 .55.448.997.997.997h5.506c.549 0 .997-.447.997-.997V4.5z" />
                                </svg>
                                Top Produits
                            </div>
                        </button>
                        <button @click="activeTab = 'low-stock'" 
                               :class="{ 
                                   'bg-white shadow-sm border-b-2 border-emerald-500 text-emerald-600': activeTab === 'low-stock', 
                                   'text-gray-500 hover:text-gray-700 hover:bg-gray-50': activeTab !== 'low-stock' 
                               }" 
                               class="flex-1 px-6 py-4 text-sm font-medium transition-all duration-200 border-b-2 border-transparent">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                </svg>
                                Stock Bas
                            </div>
                        </button>
                    </nav>
                </div>
                
                <!-- Contenu des onglets moderne -->
                <div class="p-6">
                    <div x-show="activeTab === 'top-products'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0">
                        <div class="space-y-4">
                            @forelse ($topProducts as $product)
                                <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-gray-50 to-white border border-gray-100 hover:shadow-md transition-all duration-200">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-emerald-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                            <p class="text-xs text-gray-500">{{ $product->sku ?? 'N/A' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-bold text-emerald-600">{{ (int)$product->total_quantity }}</p>
                                        <p class="text-xs text-gray-500">vendus</p>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">Aucune vente enregistrée</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                    
                    <div x-show="activeTab === 'low-stock'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 transform translate-y-2" x-transition:enter-end="opacity-100 transform translate-y-0" x-cloak>
                        <div class="space-y-4">
                            @forelse ($lowStockProducts as $product)
                                <div class="p-4 rounded-xl bg-gradient-to-r from-red-50 to-orange-50 border border-red-100">
                                    <div class="flex items-center gap-3 mb-3">
                                        <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $product->name }}</p>
                                        </div>
                                    </div>
                                    <div class="space-y-2">
                                        @foreach($product->stores as $store)
                                            <div class="flex justify-between items-center text-sm bg-white/50 rounded-lg px-3 py-2">
                                                <span class="text-gray-600">{{ $store->name }}</span>
                                                <span class="font-semibold text-red-600">{{ $store->pivot->quantity }} en stock</span>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8">
                                    <svg class="h-12 w-12 text-gray-300 mx-auto mb-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    <p class="text-sm text-gray-500">Aucun produit en stock bas</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@script
<script>
    // Configuration moderne des graphiques
    const chartConfig = {
        line: {
            type: 'line',
            data: {
                labels: @json($lineChartLabels),
                datasets: [{
                    label: 'Chiffre d\'Affaires (XAF)',
                    data: @json($lineChartValues),
                    borderColor: 'rgb(16, 185, 129)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.4,
                    borderWidth: 3,
                    pointBackgroundColor: 'rgb(16, 185, 129)',
                    pointBorderColor: 'white',
                    pointBorderWidth: 2,
                    pointRadius: 6,
                    pointHoverRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    intersect: false,
                    mode: 'index'
                },
                scales: {
                    x: {
                        grid: {
                            display: false
                        },
                        border: {
                            display: false
                        }
                    },
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0, 0, 0, 0.05)'
                        },
                        border: {
                            display: false
                        },
                        ticks: {
                            callback: function(value) {
                                return new Intl.NumberFormat('fr-FR').format(value) + ' XAF';
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(16, 185, 129, 0.8)',
                        borderWidth: 1,
                        cornerRadius: 8,
                        displayColors: false
                    }
                }
            }
        },
        donut: {
            type: 'doughnut',
            data: {
                labels: @json($donutChartLabels),
                datasets: [{
                    label: 'Ventes par catégorie',
                    data: @json($donutChartValues),
                    backgroundColor: [
                        'rgb(16, 185, 129)',
                        'rgb(59, 130, 246)', 
                        'rgb(139, 92, 246)',
                        'rgb(245, 158, 11)',
                        'rgb(239, 68, 68)'
                    ],
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(0, 0, 0, 0.8)',
                        titleColor: 'white',
                        bodyColor: 'white',
                        borderColor: 'rgba(16, 185, 129, 0.8)',
                        borderWidth: 1,
                        cornerRadius: 8
                    }
                }
            }
        }
    };

    // Initialisation des graphiques
    let salesChart, donutChart;
    
    document.addEventListener('DOMContentLoaded', function() {
        const salesChartCtx = document.getElementById('salesChart')?.getContext('2d');
        const donutChartCtx = document.getElementById('categoryDonutChart')?.getContext('2d');
        
        if (salesChartCtx) {
            salesChart = new Chart(salesChartCtx, chartConfig.line);
        }
        
        if (donutChartCtx) {
            donutChart = new Chart(donutChartCtx, chartConfig.donut);
        }
    });

    // Écoute des événements Livewire pour mise à jour
    Livewire.on('update-charts', (event) => {
        const data = event[0];
        
        if (salesChart) {
            salesChart.data.labels = data.lineLabels;
            salesChart.data.datasets[0].data = data.lineValues;
            salesChart.update('active');
        }
        
        if (donutChart) {
            donutChart.data.labels = data.donutLabels;
            donutChart.data.datasets[0].data = data.donutValues;
            donutChart.update('active');
        }
    });
</script>
@endscript
</div>  
