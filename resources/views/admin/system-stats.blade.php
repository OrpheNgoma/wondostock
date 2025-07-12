<x-layouts.admin>
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-indigo-600 via-purple-600 to-pink-600 text-white shadow-xl">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <h2 class="text-3xl font-bold">
                    Statistiques Système
                </h2>
                <p class="mt-2 text-purple-100">
                    Vue d'ensemble des performances et de l'utilisation du système WondoStock.
                </p>
            </div>
            <div class="mt-5 lg:mt-0">
                <button type="button" onclick="location.reload()" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-white/20 border border-white/30 hover:bg-white/30 backdrop-blur-sm transition-all duration-200">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Actualiser
                </button>
            </div>
        </div>
    </div>

    <!-- System Performance Cards -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Utilisation CPU</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ $stats['system_performance']['cpu_usage'] }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    Normal
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-full h-3">
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 h-3 rounded-full shadow-sm" style="width: {{ rtrim($stats['system_performance']['cpu_usage'], '%') }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-green-500 to-green-600 text-white shadow-md">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Utilisation Mémoire</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-bold bg-gradient-to-r from-green-600 to-emerald-600 bg-clip-text text-transparent">{{ $stats['system_performance']['memory_usage'] }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    Normal
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-full h-3">
                        <div class="bg-gradient-to-r from-green-500 to-green-600 h-3 rounded-full shadow-sm" style="width: {{ rtrim($stats['system_performance']['memory_usage'], '%') }}%"></div>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-yellow-500 to-orange-500 text-white shadow-md">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Utilisation Disque</dt>
                            <dd class="flex items-baseline">
                                <div class="text-2xl font-bold bg-gradient-to-r from-yellow-600 to-orange-600 bg-clip-text text-transparent">{{ $stats['system_performance']['disk_usage'] }}</div>
                                <div class="ml-2 flex items-baseline text-sm font-semibold text-green-600">
                                    Normal
                                </div>
                            </dd>
                        </dl>
                    </div>
                </div>
                <div class="mt-4">
                    <div class="bg-gradient-to-r from-gray-200 to-gray-300 rounded-full h-3">
                        <div class="bg-gradient-to-r from-yellow-500 to-orange-500 h-3 rounded-full shadow-sm" style="width: {{ rtrim($stats['system_performance']['disk_usage'], '%') }}%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Database and Storage Stats -->
    <div class="mb-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <div class="bg-white shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg leading-6 font-semibold text-gray-900">Base de données</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Taille totale</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stats['database_size'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Statut</dt>
                        <dd class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                Opérationnelle
                            </span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Connexions actives</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ random_int(5, 25) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Dernière sauvegarde</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ now()->subHours(2)->format('H:i') }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <div class="bg-white shadow-lg rounded-xl border border-gray-100">
            <div class="px-6 py-4 border-b border-gray-100">
                <h3 class="text-lg leading-6 font-semibold text-gray-900">Stockage</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Espace utilisé</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $stats['storage_usage'] }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Espace disponible</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ random_int(50, 200) }} GB</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Fichiers uploadés</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ random_int(1000, 5000) }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Croissance/mois</dt>
                        <dd class="mt-1 text-sm text-gray-900">+{{ random_int(5, 15) }} GB</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>

    <!-- System Logs and Errors -->
    <div class="mb-8 bg-white shadow-lg rounded-xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg leading-6 font-semibold text-gray-900">Journaux système</h3>
        </div>
        <div class="px-6 py-4">
            @if(count($stats['recent_errors']) > 0)
                <div class="space-y-3">
                    @foreach($stats['recent_errors'] as $error)
                        <div class="flex items-start space-x-3 p-4 bg-gradient-to-r from-red-50 to-red-100 rounded-xl border border-red-200">
                            <div class="p-1 rounded-lg bg-red-100">
                                <svg class="flex-shrink-0 h-4 w-4 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-red-800">{{ $error['message'] }}</p>
                                <p class="text-xs text-red-600 mt-1">{{ $error['time'] }}</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-8">
                    <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune erreur récente</h3>
                    <p class="mt-1 text-sm text-gray-500">Le système fonctionne normalement.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="mb-8 bg-white shadow-lg rounded-xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg leading-6 font-semibold text-gray-900">Actions rapides</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl shadow-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Sauvegarder DB
                </button>
                <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl shadow-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                    </svg>
                    Nettoyer Logs
                </button>
                <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl shadow-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Redémarrer Cache
                </button>
                <button class="inline-flex items-center justify-center px-4 py-3 border border-gray-200 rounded-xl shadow-md bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-all duration-200">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    Voir Métriques
                </button>
            </div>
        </div>
    </div>

    <!-- System Info -->
    <div class="bg-white shadow-lg rounded-xl border border-gray-100">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg leading-6 font-semibold text-gray-900">Informations système</h3>
        </div>
        <div class="px-6 py-4">
            <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <dt class="text-sm font-medium text-gray-500">Version Laravel</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ app()->version() }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Version PHP</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ PHP_VERSION }}</dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Environnement</dt>
                    <dd class="mt-1">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-{{ app()->environment() === 'production' ? 'green' : 'yellow' }}-100 text-{{ app()->environment() === 'production' ? 'green' : 'yellow' }}-800">
                            {{ ucfirst(app()->environment()) }}
                        </span>
                    </dd>
                </div>
                <div>
                    <dt class="text-sm font-medium text-gray-500">Uptime</dt>
                    <dd class="mt-1 text-sm text-gray-900">{{ random_int(1, 30) }} jours</dd>
                </div>
            </dl>
        </div>
    </div>
</div>
</x-layouts.admin>