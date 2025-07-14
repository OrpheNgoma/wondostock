<div class="space-y-6">
    <!-- En-tête moderne sobre -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Bons de Commande Fournisseurs
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Gérez vos commandes et approvisionnements auprès de vos fournisseurs
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('purchases.create') }}" 
                    
                   class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nouvelle Commande
                </a>
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

    <!-- Tableau des bons de commande -->
    <div class="mx-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-gray-200">
            <!-- En-tête du tableau -->
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Liste des Commandes</h3>
                        <p class="text-xs text-gray-600 mt-1">{{ $purchaseOrders->total() }} commande(s) au total</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                        <span>Approvisionnements</span>
                    </div>
                </div>
            </div>

            <!-- Contenu du tableau -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Commande
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Fournisseur
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Date
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Total
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Statut
                            </th>
                            <th scope="col" class="relative py-3 pl-3 pr-6">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($purchaseOrders as $order)
                            <tr wire:key="{{ $order->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $order->document_number }}</p>
                                            <p class="text-xs text-gray-500">Bon de commande</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-8 w-8 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <svg class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">{{ $order->supplier->name }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-700">{{ $order->document_date->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($order->total_amount, 0, ',', ' ') }} XAF
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @php
                                        // Mapping des statuts avec couleurs sobres
                                        $statusConfig = match($order->status->value ?? 'pending') {
                                            'completed' => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'ring' => 'ring-green-600/20', 'dot' => 'bg-green-500'],
                                            'pending' => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'ring' => 'ring-yellow-600/20', 'dot' => 'bg-yellow-500'],
                                            'cancelled' => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'ring' => 'ring-red-600/20', 'dot' => 'bg-red-500'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20', 'dot' => 'bg-gray-500'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full {{ $statusConfig['bg'] }} px-2 py-1 text-xs font-medium {{ $statusConfig['text'] }} ring-1 {{ $statusConfig['ring'] }}">
                                        <div class="h-1.5 w-1.5 rounded-full {{ $statusConfig['dot'] }}"></div>
                                        {{ $order->status->label() }}
                                    </span>
                                </td>
                                <td class="relative py-4 pl-3 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('purchases.show', $order) }}" 
                                            
                                           class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Voir
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Aucune commande trouvée</p>
                                            <p class="text-xs text-gray-500 mt-1">Commencez par créer votre première commande fournisseur</p>
                                        </div>
                                        <a href="{{ route('purchases.create') }}" 
                                            
                                           class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Nouvelle Commande
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($purchaseOrders->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    {{ $purchaseOrders->links() }}
                </div>
            @endif
        </div>
    </div>
</div>