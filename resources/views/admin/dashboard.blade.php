<x-layouts.admin>
    <!-- Header moderne pour admin -->
    <div class="mb-8">
<div class="relative overflow-hidden rounded-xl bg-white p-8 shadow-sm border border-gray-200">
<div class="absolute inset-0 bg-purple-50"></div>
            <div class="relative">
                <div class="flex items-center gap-x-4 mb-4">
<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-600 shadow-sm">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5 0l1 3m-1-3l1-3"/>
                        </svg>
                    </div>
                    <div>
<h1 class="text-3xl font-bold text-gray-900">Dashboard Global Admin</h1>
                        <p class="text-purple-600">Vue d'ensemble de la plateforme SaaS WondoStock</p>
                    </div>
                </div>
                <div class="flex items-center gap-x-6 text-sm">
                    <div class="flex items-center gap-x-2">
                        <div class="h-2 w-2 rounded-full bg-emerald-400"></div>
<span class="text-gray-600">Système opérationnel</span>
                    </div>
                    <div class="flex items-center gap-x-2">
                        <div class="h-2 w-2 rounded-full bg-purple-400"></div>
<span class="text-gray-600">{{ now()->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-gray-200 hover:border-emerald-200 transition-all duration-300 group">
            <div class="absolute inset-0 bg-emerald-50/50 group-hover:bg-emerald-50 transition-colors duration-300"></div>
            <div class="relative flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Entreprises</p>
                    <p class="text-3xl font-bold text-gray-900">{{ $stats['total_companies'] }}</p>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-gray-200 hover:border-emerald-200 transition-all duration-300 group">
            <div class="absolute inset-0 bg-emerald-50/50 group-hover:bg-emerald-50 transition-colors duration-300"></div>
            <div class="relative flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-600 shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Entreprises Actives</p>
                    <p class="text-3xl font-bold text-emerald-600">{{ $stats['active_companies'] }}</p>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-gray-200 hover:border-purple-200 transition-all duration-300 group">
            <div class="absolute inset-0 bg-purple-50/50 group-hover:bg-purple-50 transition-colors duration-300"></div>
            <div class="relative flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-600 shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Total Utilisateurs</p>
                    <p class="text-3xl font-bold text-purple-600">{{ $stats['total_users'] }}</p>
                </div>
            </div>
        </div>

        <div class="relative overflow-hidden rounded-xl bg-white p-6 shadow-sm border border-gray-200 hover:border-amber-200 transition-all duration-300 group">
            <div class="absolute inset-0 bg-amber-50/50 group-hover:bg-amber-50 transition-colors duration-300"></div>
            <div class="relative flex items-center">
                <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-600 shadow-sm group-hover:scale-105 transition-transform duration-300">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-600">Revenus Mensuels</p>
                    <p class="text-3xl font-bold text-amber-600">{{ $stats['monthly_revenue'] }}€</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenu principal -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
        <div class="lg:col-span-2">
            <div class="rounded-xl bg-white shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Entreprises Récemment Inscrites</h3>
                </div>
                <div class="p-6">
                    @if($stats['recent_signups']->count() > 0)
                        <div class="space-y-4">
                            @foreach($stats['recent_signups'] as $company)
                                <div class="flex items-center justify-between p-4 bg-gray-50 rounded-xl border border-gray-100 hover:border-purple-200 transition-all duration-300 group">
                                    <div class="flex items-center space-x-4">
                                        <div class="h-12 w-12 rounded-xl bg-purple-600 flex items-center justify-center text-white font-bold text-lg shadow-sm group-hover:scale-105 transition-transform duration-300">
                                            {{ substr($company->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-semibold text-gray-900">{{ $company->name }}</p>
                                            <p class="text-sm text-gray-600">{{ $company->email }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-sm font-medium text-gray-700">{{ $company->created_at->format('d/m/Y') }}</p>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $company->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                            </svg>
                            <p class="mt-4 text-gray-500">Aucune nouvelle inscription récente.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            <div class="rounded-xl bg-white shadow-sm border border-gray-200">
                <div class="px-6 py-4 border-b border-gray-100">
                    <h3 class="text-lg font-semibold text-gray-900">Actions Rapides</h3>
                </div>
                <div class="p-6 space-y-4">
                    <a href="{{ route('admin.companies.index') }}" class="group w-full inline-flex items-center justify-center px-6 py-4 text-sm font-semibold rounded-xl text-white bg-purple-600 hover:bg-purple-700 shadow-sm hover:shadow-md transition-all duration-300 hover:scale-105">
                        <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                        </svg>
                        Gérer les Entreprises
                    </a>
                    <a href="{{ route('admin.system-stats') }}" class="group w-full inline-flex items-center justify-center px-6 py-4 border border-gray-200 text-sm font-semibold rounded-xl text-gray-700 bg-gray-50 hover:bg-gray-100 hover:text-gray-900 hover:border-gray-300 transition-all duration-300 hover:scale-105">
                        <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        Statistiques Système
                    </a>
                    <button class="group w-full inline-flex items-center justify-center px-6 py-4 border border-emerald-200 text-sm font-semibold rounded-xl text-emerald-700 bg-emerald-50 hover:bg-emerald-100 hover:border-emerald-300 transition-all duration-300 hover:scale-105">
                        <svg class="w-5 h-5 mr-3 group-hover:scale-110 transition-transform duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Nouvelle Entreprise
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Top entreprises par utilisation -->
    <div class="rounded-xl bg-white shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-100">
            <h3 class="text-lg font-semibold text-gray-900">Top Entreprises par Utilisation</h3>
        </div>
        <div class="p-6">
            @if($stats['top_companies']->count() > 0)
                <div class="overflow-hidden rounded-lg border border-gray-200">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entreprise</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Utilisateurs</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Produits</th>
                                <th class="px-6 py-4 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($stats['top_companies'] as $company)
                                <tr class="hover:bg-gray-50 transition-colors duration-200">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-4">
                                            <div class="h-10 w-10 rounded-lg bg-purple-600 flex items-center justify-center text-white font-bold">
                                                {{ substr($company->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-semibold text-gray-900">{{ $company->name }}</div>
                                                <div class="text-sm text-gray-500">{{ $company->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $company->users_count }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="text-sm font-medium text-gray-900">{{ $company->products_count ?? 0 }}</span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold {{ $company->is_active ? 'bg-emerald-100 text-emerald-700 border border-emerald-200' : 'bg-red-100 text-red-700 border border-red-200' }}">
                                            {{ $company->is_active ? 'Active' : 'Inactive' }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center py-12">
                    <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                    </svg>
                    <p class="mt-4 text-gray-500">Aucune donnée disponible.</p>
                </div>
            @endif
        </div>
    </div>
</x-layouts.admin>