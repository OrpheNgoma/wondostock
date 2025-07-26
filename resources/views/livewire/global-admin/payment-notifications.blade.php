<div>
    <div class="header py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="md:flex md:items-center md:justify-between">
                <div class="min-w-0 flex-1">
                    <h1 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                        Notifications de Paiement
                    </h1>
                    <p class="mt-1 text-sm text-gray-500">
                        Gestion des notifications automatiques de paiement pour toutes les entreprises
                    </p>
                </div>
                <div class="mt-4 flex md:ml-4 md:mt-0">
                    <button wire:click="generateNotifications" 
                            class="inline-flex items-center rounded-lg bg-blue-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                        Générer Notifications
                    </button>
                    <button wire:click="processNotifications" 
                            class="ml-3 inline-flex items-center rounded-lg bg-green-600 px-3 py-2 text-sm font-medium text-white shadow-sm hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/>
                        </svg>
                        Envoyer en Attente
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-yellow-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">En attente</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalPending }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-green-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Envoyées</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalSent }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-red-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Échouées</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $totalFailed }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <h3 class="text-sm font-medium text-gray-500">Total</h3>
                        <p class="text-2xl font-semibold text-gray-900">{{ $notifications->total() }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtres et recherche -->
        <div class="bg-white rounded-lg shadow p-6 mb-8">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Rechercher</label>
                    <input wire:model.live.debounce.300ms="search" 
                           type="text" 
                           id="search"
                           class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500" 
                           placeholder="Titre, message, entreprise...">
                </div>

                <div>
                    <label for="typeFilter" class="block text-sm font-medium text-gray-700 mb-2">Type</label>
                    <select wire:model.live="typeFilter" 
                            id="typeFilter"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les types</option>
                        <option value="reminder">Rappel</option>
                        <option value="overdue">En retard</option>
                        <option value="payment_received">Paiement reçu</option>
                        <option value="suspension_warning">Avertissement suspension</option>
                    </select>
                </div>

                <div>
                    <label for="statusFilter" class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                    <select wire:model.live="statusFilter" 
                            id="statusFilter"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="">Tous les statuts</option>
                        <option value="pending">En attente</option>
                        <option value="sent">Envoyée</option>
                        <option value="failed">Échouée</option>
                        <option value="cancelled">Annulée</option>
                    </select>
                </div>

                <div>
                    <label for="perPage" class="block text-sm font-medium text-gray-700 mb-2">Par page</label>
                    <select wire:model.live="perPage" 
                            id="perPage"
                            class="w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <option value="10">10</option>
                        <option value="15">15</option>
                        <option value="25">25</option>
                        <option value="50">50</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Liste des notifications -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Notification
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Entreprise
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Type
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Statut
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Programmée
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                Actions
                            </th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($notifications as $notification)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4">
                                    <div>
                                        <div class="text-sm font-medium text-gray-900">
                                            {{ $notification->title }}
                                        </div>
                                        <div class="text-sm text-gray-500 mt-1">
                                            {{ Str::limit($notification->message, 100) }}
                                        </div>
                                        @if($notification->invoice)
                                            <div class="text-xs text-blue-600 mt-1">
                                                Facture: {{ $notification->invoice->invoice_number }}
                                            </div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm text-gray-900">{{ $notification->company->name }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $typeLabels = [
                                            'reminder' => ['Rappel', 'bg-yellow-100 text-yellow-800'],
                                            'overdue' => ['En retard', 'bg-red-100 text-red-800'],
                                            'payment_received' => ['Paiement reçu', 'bg-green-100 text-green-800'],
                                            'suspension_warning' => ['Avertissement', 'bg-orange-100 text-orange-800'],
                                        ];
                                        $typeInfo = $typeLabels[$notification->type] ?? ['Inconnu', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $typeInfo[1] }}">
                                        {{ $typeInfo[0] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $statusLabels = [
                                            'pending' => ['En attente', 'bg-yellow-100 text-yellow-800'],
                                            'sent' => ['Envoyée', 'bg-green-100 text-green-800'],
                                            'failed' => ['Échouée', 'bg-red-100 text-red-800'],
                                            'cancelled' => ['Annulée', 'bg-gray-100 text-gray-800'],
                                        ];
                                        $statusInfo = $statusLabels[$notification->status] ?? ['Inconnu', 'bg-gray-100 text-gray-800'];
                                    @endphp
                                    <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $statusInfo[1] }}">
                                        {{ $statusInfo[0] }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    @if($notification->scheduled_for)
                                        {{ $notification->scheduled_for->format('d/m/Y H:i') }}
                                    @else
                                        -
                                    @endif
                                    @if($notification->sent_at)
                                        <div class="text-xs text-green-600">
                                            Envoyée: {{ $notification->sent_at->format('d/m/Y H:i') }}
                                        </div>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                    <div class="flex space-x-2">
                                        @if($notification->status === 'failed')
                                            <button wire:click="resendNotification({{ $notification->id }})"
                                                    class="text-blue-600 hover:text-blue-900">
                                                Renvoyer
                                            </button>
                                        @endif
                                        @if($notification->status === 'pending')
                                            <button wire:click="cancelNotification({{ $notification->id }})"
                                                    class="text-red-600 hover:text-red-900">
                                                Annuler
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="text-gray-500">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-5 5v-5zM9 7H4l5-5v5zM12 3v18"/>
                                        </svg>
                                        <h3 class="mt-2 text-sm font-medium text-gray-900">Aucune notification</h3>
                                        <p class="mt-1 text-sm text-gray-500">Aucune notification trouvée avec les critères actuels.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($notifications->hasPages())
                <div class="px-6 py-4 border-t border-gray-200">
                    {{ $notifications->links() }}
                </div>
            @endif
        </div>
    </div>
</div>