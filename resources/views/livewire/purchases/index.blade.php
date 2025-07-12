<div>
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900">Bons de Commande Fournisseurs</h2>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <a href="{{ route('purchases.create') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nouvelle Commande
            </a>
        </div>
    </div>
    <!-- Tableau -->
    <div class="mt-8 flow-root">
        <div class="inline-block min-w-full py-2 align-middle sm:px-1">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Commande N°</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Fournisseur</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                            <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                            <th class="relative py-3.5 pl-3 pr-4 sm:pr-6"></th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($purchaseOrders as $order)
                            <tr wire:key="{{ $order->id }}">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm font-medium text-gray-900 sm:pl-6">{{ $order->document_number }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->supplier->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $order->document_date->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-semibold text-gray-900">{{ number_format($order->total_amount, 0, ',', ' ') }} XAF</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500"><span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium bg-gray-100 text-gray-700">{{ $order->status->label() }}</span></td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('purchases.show', $order) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-10 text-gray-500">Aucun bon de commande trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="mt-4">{{ $purchaseOrders->links() }}</div>
        </div>
    </div>
</div>