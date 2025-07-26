<div class="space-y-8">
    <!-- En-tête du dashboard -->
    <div class="bg-white border-b border-gray-200 px-6 py-8 rounded-lg shadow-sm">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">
                    Dashboard Administration Globale
                </h1>
                <p class="mt-2 text-lg text-gray-600">
                    Vue d'ensemble de votre plateforme SaaS WondoStock
                </p>
            </div>
            <div class="flex items-center gap-3">
                <div class="px-4 py-2 bg-gradient-to-r from-red-50 to-orange-50 rounded-lg border border-red-200">
                    <div class="flex items-center gap-2">
                        <div class="h-3 w-3 rounded-full bg-red-500 animate-pulse"></div>
                        <span class="text-sm font-medium text-red-700">Système Actif</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistiques principales -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Total Entreprises -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Total Entreprises</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($this->system_stats['total_companies']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ $this->system_stats['active_companies'] }} actives
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-blue-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Utilisateurs Actifs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Utilisateurs Actifs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($this->system_stats['total_users']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ number_format($this->system_health['average_users_per_company'], 1) }} par entreprise
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-green-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Revenus Mensuels -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Revenus Mensuels</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($this->revenue_stats['monthly_revenue'], 2) }}€</p>
                    <p class="text-sm text-gray-500 mt-1">
                        ARR: {{ number_format($this->revenue_stats['annual_revenue'], 0) }}€
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-yellow-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Abonnements Actifs -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-gray-600">Abonnements Actifs</p>
                    <p class="text-3xl font-bold text-gray-900">{{ number_format($this->system_stats['total_subscriptions']) }}</p>
                    <p class="text-sm text-gray-500 mt-1">
                        {{ number_format($this->system_health['subscription_rate'], 1) }}% conversion
                    </p>
                </div>
                <div class="h-12 w-12 rounded-full bg-purple-100 flex items-center justify-center">
                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Graphiques et tableaux -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Répartition des plans -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Répartition des Plans</h3>
            <div class="space-y-4">
                @foreach($this->plan_distribution as $plan)
                    @php
                        $percentage = $this->system_stats['total_subscriptions'] > 0 
                            ? ($plan->subscription_count / $this->system_stats['total_subscriptions']) * 100 
                            : 0;
                        $planConfig = match($plan->slug) {
                            'essentiel' => ['bg' => 'bg-blue-500', 'text' => 'text-blue-600'],
                            'pro' => ['bg' => 'bg-purple-500', 'text' => 'text-purple-600'],
                            'entreprise' => ['bg' => 'bg-amber-500', 'text' => 'text-amber-600'],
                            default => ['bg' => 'bg-gray-500', 'text' => 'text-gray-600'],
                        };
                    @endphp
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="h-3 w-3 rounded-full {{ $planConfig['bg'] }}"></div>
                            <span class="text-sm font-medium text-gray-900">{{ $plan->name }}</span>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-semibold {{ $planConfig['text'] }}">{{ $plan->subscription_count }}</span>
                            <span class="text-xs text-gray-500 ml-1">({{ number_format($percentage, 1) }}%)</span>
                        </div>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="h-2 rounded-full {{ $planConfig['bg'] }}" style="width: {{ $percentage }}%"></div>
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Croissance des entreprises -->
        <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Croissance des Entreprises</h3>
            <div class="space-y-3">
                @foreach($this->company_growth as $month)
                    <div class="flex items-center justify-between">
                        <span class="text-sm text-gray-600">{{ $month['month'] }}</span>
                        <div class="flex items-center gap-2">
                            <div class="w-20 bg-gray-200 rounded-full h-2">
                                <div class="h-2 rounded-full bg-red-500" style="width: {{ $month['count'] > 0 ? min(($month['count'] / max($this->company_growth->pluck('count')->toArray())) * 100, 100) : 0 }}%"></div>
                            </div>
                            <span class="text-sm font-semibold text-gray-900 w-6">{{ $month['count'] }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Entreprises récentes -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Entreprises Récentes</h3>
            <p class="text-sm text-gray-600 mt-1">Les 5 dernières entreprises inscrites</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Entreprise</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Propriétaire</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Plan</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Statut</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Inscription</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->recent_companies as $company)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 rounded-full bg-red-100 flex items-center justify-center">
                                        <span class="text-sm font-semibold text-red-700">
                                            {{ strtoupper(substr($company->name, 0, 2)) }}
                                        </span>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900">{{ $company->name }}</div>
                                        <div class="text-sm text-gray-500">{{ $company->email }}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->owner)
                                    <div class="text-sm text-gray-900">{{ $company->owner->name }}</div>
                                    <div class="text-sm text-gray-500">{{ $company->owner->email }}</div>
                                @else
                                    <span class="text-sm text-gray-400">Non assigné</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->subscription && $company->subscription->plan)
                                    @php
                                        $planConfig = match($company->subscription->plan->slug) {
                                            'essentiel' => ['bg' => 'bg-blue-100', 'text' => 'text-blue-800', 'ring' => 'ring-blue-600/20'],
                                            'pro' => ['bg' => 'bg-purple-100', 'text' => 'text-purple-800', 'ring' => 'ring-purple-600/20'],
                                            'entreprise' => ['bg' => 'bg-amber-100', 'text' => 'text-amber-800', 'ring' => 'ring-amber-600/20'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-full {{ $planConfig['bg'] }} px-2 py-1 text-xs font-medium {{ $planConfig['text'] }} ring-1 {{ $planConfig['ring'] }}">
                                        {{ $company->subscription->plan->name }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center rounded-full bg-gray-100 px-2 py-1 text-xs font-medium text-gray-800 ring-1 ring-gray-600/20">
                                        Aucun plan
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @if($company->is_active)
                                    <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-800 ring-1 ring-green-600/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                        Active
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-2 py-1 text-xs font-medium text-red-800 ring-1 ring-red-600/20">
                                        <div class="h-1.5 w-1.5 rounded-full bg-red-500"></div>
                                        Inactive
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                {{ $company->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-sm text-gray-500">
                                Aucune entreprise trouvée
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($this->recent_companies->count() > 0)
            <div class="px-6 py-3 bg-gray-50 border-t border-gray-200">
                <span class="text-sm font-medium text-gray-500">
                    Gestion des entreprises (à venir) →
                </span>
            </div>
        @endif
    </div>

    <!-- Santé du système -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Santé du Système</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="text-center">
                <div class="h-16 w-16 rounded-full bg-green-100 mx-auto flex items-center justify-center mb-3">
                    <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($this->system_health['activation_rate'], 1) }}%</p>
                <p class="text-sm text-gray-600">Taux d'Activation</p>
            </div>
            <div class="text-center">
                <div class="h-16 w-16 rounded-full bg-blue-100 mx-auto flex items-center justify-center mb-3">
                    <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($this->system_health['subscription_rate'], 1) }}%</p>
                <p class="text-sm text-gray-600">Taux de Conversion</p>
            </div>
            <div class="text-center">
                <div class="h-16 w-16 rounded-full bg-purple-100 mx-auto flex items-center justify-center mb-3">
                    <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                    </svg>
                </div>
                <p class="text-2xl font-bold text-gray-900">{{ number_format($this->system_health['average_users_per_company'], 1) }}</p>
                <p class="text-sm text-gray-600">Utilisateurs/Entreprise</p>
            </div>
        </div>
    </div>
</div>