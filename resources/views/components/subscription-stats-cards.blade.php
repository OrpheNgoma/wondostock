<div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-blue-600">{{ $totalSubscriptions }}</div>
        <div class="text-sm text-blue-700">Total des abonnements</div>
    </div>
    
    <div class="bg-green-50 border border-green-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-green-600">{{ $activeSubscriptions }}</div>
        <div class="text-sm text-green-700">Abonnements actifs</div>
    </div>
    
    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-red-600">{{ $expiredSubscriptions }}</div>
        <div class="text-sm text-red-700">Abonnements expirés</div>
    </div>
    
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <div class="text-2xl font-bold text-yellow-600">{{ $suspendedSubscriptions }}</div>
        <div class="text-sm text-yellow-700">Abonnements suspendus</div>
    </div>
</div>

<div class="mt-6 grid grid-cols-1 lg:grid-cols-2 gap-4">
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Taux d'Activité</h3>
        <div class="flex items-center justify-between">
            <span class="text-sm text-gray-600">Actifs / Total</span>
            <span class="text-2xl font-bold text-blue-600">
                {{ $totalSubscriptions > 0 ? round(($activeSubscriptions / $totalSubscriptions) * 100, 1) : 0 }}%
            </span>
        </div>
        <div class="mt-2 bg-gray-200 rounded-full h-2">
            <div class="bg-blue-600 h-2 rounded-full" 
                 style="width: {{ $totalSubscriptions > 0 ? ($activeSubscriptions / $totalSubscriptions) * 100 : 0 }}%"></div>
        </div>
    </div>
    
    <div class="bg-white border border-gray-200 rounded-lg p-4">
        <h3 class="text-lg font-semibold text-gray-800 mb-3">Revenus Estimés</h3>
        <div class="text-2xl font-bold text-green-600">
            {{ number_format(\App\Models\Subscription::where('subscriptions.status', 'active')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.price') / 100, 0, ',', ' ') }} FCFA
        </div>
        <div class="text-sm text-gray-600">Revenus mensuels récurrents</div>
    </div>
</div>