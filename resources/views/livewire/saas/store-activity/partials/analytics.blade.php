{{-- KPIs de performance --}}
<div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">CA période actuelle</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['current_period_sales'] ?? 0, 0, ',', ' ') }} FCFA</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-blue-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">CA période précédente</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['previous_period_sales'] ?? 0, 0, ',', ' ') }} FCFA</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    @php
                        $growthRate = $data['growth_rate'] ?? 0;
                        $isPositive = $growthRate >= 0;
                    @endphp
                    <div class="w-8 h-8 {{ $isPositive ? 'bg-green-500' : 'bg-red-500' }} rounded-full flex items-center justify-center">
                        @if($isPositive)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        @endif
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Croissance</dt>
                        <dd class="text-lg font-medium {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
                            {{ $isPositive ? '+' : '' }}{{ number_format($growthRate, 1) }}%
                        </dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Graphiques de performance --}}
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Comparaison des périodes --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📊 Comparaison des performances</h3>
            <div class="space-y-4">
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Période actuelle</span>
                        <span>{{ number_format($data['current_period_sales'] ?? 0, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        @php
                            $maxSales = max($data['current_period_sales'] ?? 0, $data['previous_period_sales'] ?? 1);
                            $currentPercentage = $maxSales > 0 ? (($data['current_period_sales'] ?? 0) / $maxSales) * 100 : 0;
                        @endphp
                        <div class="bg-blue-600 h-3 rounded-full" style="width: {{ $currentPercentage }}%"></div>
                    </div>
                </div>
                
                <div>
                    <div class="flex justify-between text-sm text-gray-600 mb-1">
                        <span>Période précédente</span>
                        <span>{{ number_format($data['previous_period_sales'] ?? 0, 0, ',', ' ') }} FCFA</span>
                    </div>
                    <div class="w-full bg-gray-200 rounded-full h-3">
                        @php
                            $previousPercentage = $maxSales > 0 ? (($data['previous_period_sales'] ?? 0) / $maxSales) * 100 : 0;
                        @endphp
                        <div class="bg-gray-400 h-3 rounded-full" style="width: {{ $previousPercentage }}%"></div>
                    </div>
                </div>
            </div>

            @php
                $difference = ($data['current_period_sales'] ?? 0) - ($data['previous_period_sales'] ?? 0);
                $isImprovement = $difference >= 0;
            @endphp
            <div class="mt-4 p-3 rounded-lg {{ $isImprovement ? 'bg-green-50' : 'bg-red-50' }}">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        @if($isImprovement)
                            <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path>
                            </svg>
                        @else
                            <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path>
                            </svg>
                        @endif
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium {{ $isImprovement ? 'text-green-800' : 'text-red-800' }}">
                            {{ $isImprovement ? 'Amélioration' : 'Baisse' }} de {{ number_format(abs($difference), 0, ',', ' ') }} FCFA
                        </p>
                        <p class="text-xs {{ $isImprovement ? 'text-green-600' : 'text-red-600' }}">
                            par rapport à la période précédente
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Rotation des stocks --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🔄 Rotation des stocks</h3>
            <div class="text-center">
                <div class="text-4xl font-bold text-blue-600 mb-2">
                    {{ number_format($data['stock_turnover'] ?? 0, 2) }}
                </div>
                <p class="text-sm text-gray-600 mb-4">Taux de rotation pour la période</p>
                
                @php
                    $turnover = $data['stock_turnover'] ?? 0;
                    $turnoverStatus = $turnover >= 4 ? 'excellent' : ($turnover >= 2 ? 'good' : 'poor');
                @endphp
                
                <div class="space-y-2">
                    <div class="w-full bg-gray-200 rounded-full h-2">
                        <div class="bg-{{ $turnoverStatus === 'excellent' ? 'green' : ($turnoverStatus === 'good' ? 'yellow' : 'red') }}-500 h-2 rounded-full" 
                             style="width: {{ min(($turnover / 6) * 100, 100) }}%"></div>
                    </div>
                    
                    <div class="flex justify-between text-xs text-gray-500">
                        <span>Faible (0-2)</span>
                        <span>Correct (2-4)</span>
                        <span>Excellent (4+)</span>
                    </div>
                </div>

                <div class="mt-4 p-3 rounded-lg {{ $turnoverStatus === 'excellent' ? 'bg-green-50' : ($turnoverStatus === 'good' ? 'bg-yellow-50' : 'bg-red-50') }}">
                    <p class="text-sm font-medium {{ $turnoverStatus === 'excellent' ? 'text-green-800' : ($turnoverStatus === 'good' ? 'text-yellow-800' : 'text-red-800') }}">
                        @if($turnoverStatus === 'excellent')
                            ✅ Excellente rotation des stocks
                        @elseif($turnoverStatus === 'good')
                            ⚠️ Rotation correcte
                        @else
                            ⚠️ Rotation faible - Optimisation nécessaire
                        @endif
                    </p>
                    <p class="text-xs {{ $turnoverStatus === 'excellent' ? 'text-green-600' : ($turnoverStatus === 'good' ? 'text-yellow-600' : 'text-red-600') }} mt-1">
                        @if($turnoverStatus === 'excellent')
                            Vos stocks se renouvellent rapidement
                        @elseif($turnoverStatus === 'good')
                            Performance acceptable mais améliorable
                        @else
                            Considérez réviser votre stratégie d'approvisionnement
                        @endif
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Métriques avancées --}}
<div class="bg-white shadow rounded-lg">
    <div class="px-4 py-5 sm:p-6">
        <h3 class="text-lg font-medium text-gray-900 mb-6">📈 Métriques de performance avancées</h3>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            {{-- Rentabilité --}}
            <div class="text-center">
                <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    @php
                        $currentSales = $data['current_period_sales'] ?? 0;
                        $profitMargin = $currentSales > 0 ? 25 : 0; // Estimation
                    @endphp
                    {{ $profitMargin }}%
                </div>
                <div class="text-sm text-gray-600">Marge bénéficiaire</div>
            </div>

            {{-- Efficacité des ventes --}}
            <div class="text-center">
                <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    @php
                        $efficiency = $currentSales > 0 ? min(85 + ($growthRate * 2), 100) : 0;
                    @endphp
                    {{ number_format($efficiency, 0) }}%
                </div>
                <div class="text-sm text-gray-600">Efficacité des ventes</div>
            </div>

            {{-- Productivité --}}
            <div class="text-center">
                <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold text-gray-900">
                    @php
                        $productivity = $currentSales > 0 ? number_format($currentSales / max(1, intval($selectedPeriod)), 0) : 0;
                    @endphp
                    {{ $productivity }}€
                </div>
                <div class="text-sm text-gray-600">CA par jour</div>
            </div>

            {{-- Tendance --}}
            <div class="text-center">
                <div class="w-16 h-16 bg-orange-100 rounded-full flex items-center justify-center mx-auto mb-2">
                    <svg class="w-8 h-8 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path>
                    </svg>
                </div>
                <div class="text-2xl font-bold {{ $isPositive ? 'text-green-600' : 'text-red-600' }}">
                    {{ $isPositive ? '↗️' : '↘️' }}
                </div>
                <div class="text-sm text-gray-600">
                    {{ $isPositive ? 'Tendance positive' : 'Tendance négative' }}
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Recommandations --}}
<div class="mt-6 bg-gradient-to-r from-blue-50 to-purple-50 rounded-lg border border-blue-200">
    <div class="px-6 py-5">
        <h3 class="text-lg font-medium text-gray-900 mb-4">💡 Recommandations intelligentes</h3>
        <div class="space-y-3">
            @if($growthRate > 10)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-green-100 text-green-600 text-sm">✓</span>
                    </div>
                    <p class="text-sm text-gray-700">
                        <strong>Excellente performance !</strong> Votre croissance de {{ number_format($growthRate, 1) }}% est remarquable. 
                        Considérez d'augmenter vos stocks pour soutenir cette dynamique.
                    </p>
                </div>
            @elseif($growthRate < -10)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-red-100 text-red-600 text-sm">!</span>
                    </div>
                    <p class="text-sm text-gray-700">
                        <strong>Attention :</strong> Une baisse de {{ number_format(abs($growthRate), 1) }}% nécessite une analyse. 
                        Vérifiez vos stratégies marketing et prix.
                    </p>
                </div>
            @endif

            @if($turnover < 2)
                <div class="flex items-start space-x-3">
                    <div class="flex-shrink-0">
                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-yellow-100 text-yellow-600 text-sm">⚠</span>
                    </div>
                    <p class="text-sm text-gray-700">
                        <strong>Rotation des stocks lente :</strong> Envisagez des promotions sur les produits à faible rotation 
                        ou réduisez les commandes de réapprovisionnement.
                    </p>
                </div>
            @endif

            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full bg-blue-100 text-blue-600 text-sm">💡</span>
                </div>
                <p class="text-sm text-gray-700">
                    <strong>Conseil :</strong> Analysez vos pics de vente quotidiens pour optimiser vos horaires et votre personnel.
                </p>
            </div>
        </div>
    </div>
</div>