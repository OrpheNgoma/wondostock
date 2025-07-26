{{-- Résumé des ventes --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-green-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Chiffre d'affaires</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['sales_report']['summary']->total_revenue ?? 0, 0, ',', ' ') }} FCFA</dd>
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
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Factures</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['sales_report']['summary']->total_invoices ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-purple-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v2a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Panier moyen</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ number_format($data['sales_report']['summary']->average_invoice_value ?? 0, 0, ',', ' ') }} FCFA</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0">
                    <div class="w-8 h-8 bg-orange-500 rounded-full flex items-center justify-center">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                </div>
                <div class="ml-5 w-0 flex-1">
                    <dl>
                        <dt class="text-sm font-medium text-gray-500 truncate">Clients uniques</dt>
                        <dd class="text-lg font-medium text-gray-900">{{ $data['sales_report']['summary']->unique_customers ?? 0 }}</dd>
                    </dl>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
    {{-- Graphique des ventes par jour --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📈 Évolution des ventes</h3>
            @if(count($data['sales_report']['daily_sales'] ?? []) > 0)
                <div class="space-y-2">
                    @foreach($data['sales_report']['daily_sales'] as $day)
                        <div class="flex items-center justify-between py-2">
                            <span class="text-sm text-gray-600">{{ \Carbon\Carbon::parse($day->date)->format('d/m') }}</span>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $maxRevenue = $data['sales_report']['daily_sales']->max('daily_revenue');
                                        $percentage = $maxRevenue > 0 ? ($day->daily_revenue / $maxRevenue) * 100 : 0;
                                    @endphp
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($day->daily_revenue, 0, ',', ' ') }}€</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune donnée de vente pour cette période</p>
            @endif
        </div>
    </div>

    {{-- Ventes par heure aujourd'hui --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">⏰ Ventes par heure (aujourd'hui)</h3>
            @if(count($data['hourly_today'] ?? []) > 0)
                <div class="space-y-2">
                    @foreach($data['hourly_today'] as $hour)
                        <div class="flex items-center justify-between py-1">
                            <span class="text-sm text-gray-600">{{ sprintf('%02d:00', $hour->hour) }}</span>
                            <div class="flex-1 mx-3">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    @php
                                        $maxHourly = $data['hourly_today']->max('revenue');
                                        $percentage = $maxHourly > 0 ? ($hour->revenue / $maxHourly) * 100 : 0;
                                    @endphp
                                    <div class="bg-green-600 h-2 rounded-full" style="width: {{ $percentage }}%"></div>
                                </div>
                            </div>
                            <span class="text-sm font-medium text-gray-900">{{ number_format($hour->revenue, 0, ',', ' ') }}€</span>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune vente aujourd'hui</p>
            @endif
        </div>
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
    {{-- Top produits par ventes --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">🏆 Top produits vendus</h3>
            @if(count($data['sales_report']['top_products_by_revenue'] ?? []) > 0)
                <div class="space-y-3">
                    @foreach($data['sales_report']['top_products_by_revenue'] as $index => $product)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full 
                                                {{ $index < 3 ? 'bg-yellow-100 text-yellow-800' : 'bg-gray-100 text-gray-600' }} text-xs font-medium">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $product->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $product->total_quantity }} vendus</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($product->total_revenue, 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-500">{{ number_format($product->average_price, 2, ',', ' ') }} FCFA/unité</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune vente pour cette période</p>
            @endif
        </div>
    </div>

    {{-- Top clients --}}
    <div class="bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">👥 Meilleurs clients</h3>
            @if(count($data['sales_report']['top_customers'] ?? []) > 0)
                <div class="space-y-3">
                    @foreach($data['sales_report']['top_customers'] as $index => $customer)
                        <div class="flex items-center justify-between py-2 border-b border-gray-100 last:border-b-0">
                            <div class="flex items-center space-x-3">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-6 w-6 rounded-full 
                                                {{ $index < 3 ? 'bg-blue-100 text-blue-800' : 'bg-gray-100 text-gray-600' }} text-xs font-medium">
                                        {{ $index + 1 }}
                                    </span>
                                </div>
                                <div>
                                    <p class="text-sm font-medium text-gray-900">{{ $customer->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $customer->invoices_count }} commandes</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-medium text-gray-900">{{ number_format($customer->total_spent, 0, ',', ' ') }} FCFA</p>
                                <p class="text-xs text-gray-500">{{ number_format($customer->average_order_value, 0, ',', ' ') }} FCFA/commande</p>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <p class="text-gray-500 text-sm">Aucune donnée client pour cette période</p>
            @endif
        </div>
    </div>
</div>

{{-- Ventes par catégorie --}}
@if(count($data['sales_report']['sales_by_category'] ?? []) > 0)
    <div class="mt-6 bg-white shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
            <h3 class="text-lg font-medium text-gray-900 mb-4">📊 Ventes par catégorie</h3>
            <div class="space-y-3">
                @foreach($data['sales_report']['sales_by_category'] as $category)
                    <div class="flex items-center justify-between py-2">
                        <div>
                            <p class="text-sm font-medium text-gray-900">{{ $category->name ?? 'Sans catégorie' }}</p>
                            <p class="text-xs text-gray-500">{{ $category->products_count }} produits • {{ $category->total_quantity }} vendus</p>
                        </div>
                        <div class="text-right">
                            <p class="text-sm font-medium text-gray-900">{{ number_format($category->total_revenue, 0, ',', ' ') }} FCFA</p>
                            @php
                                $totalRevenue = $data['sales_report']['summary']->total_revenue ?? 1;
                                $percentage = ($category->total_revenue / $totalRevenue) * 100;
                            @endphp
                            <p class="text-xs text-gray-500">{{ number_format($percentage, 1) }}% du CA</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endif