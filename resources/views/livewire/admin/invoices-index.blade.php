<div class="p-6">
    <!-- En-tête avec statistiques -->
    <div class="mb-8">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Gestion des Factures</h1>
                <p class="text-gray-600">Gérez les factures et paiements SaaS</p>
            </div>
            <button wire:click="generateMonthlyInvoices" 
                    class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium">
                Générer Factures Mensuelles
            </button>
        </div>

        <!-- Statistiques -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-blue-100 rounded-lg">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total Factures</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->stats['total_invoices'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-green-100 rounded-lg">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Chiffre d'Affaires Total</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($this->stats['total_revenue'], 0, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-yellow-100 rounded-lg">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">En Retard</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ $this->stats['overdue_invoices'] }}</p>
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-2 bg-indigo-100 rounded-lg">
                        <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path>
                        </svg>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Revenus Ce Mois</p>
                        <p class="text-2xl font-semibold text-gray-900">{{ number_format($this->stats['monthly_revenue'], 0, ',', ' ') }} €</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtres et recherche -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Recherche</label>
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Numéro, entreprise..."
                       class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Statut</label>
                <select wire:model.live="statusFilter" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Tous les statuts</option>
                    <option value="draft">Brouillon</option>
                    <option value="sent">Envoyée</option>
                    <option value="paid">Payée</option>
                    <option value="overdue">En retard</option>
                    <option value="cancelled">Annulée</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-2">Entreprise</label>
                <select wire:model.live="companyFilter" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Toutes les entreprises</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-end">
                <button wire:click="$set('search', ''); $set('statusFilter', ''); $set('companyFilter', '')"
                        class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-4 py-2 rounded-lg">
                    Réinitialiser
                </button>
            </div>
        </div>
    </div>

    <!-- Liste des factures -->
    <div class="bg-white rounded-lg shadow">
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('invoice_number')">
                            <div class="flex items-center space-x-1">
                                <span>Numéro</span>
                                @if($sortBy === 'invoice_number')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Entreprise
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Plan
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('total_amount')">
                            <div class="flex items-center space-x-1">
                                <span>Montant</span>
                                @if($sortBy === 'total_amount')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Statut
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider cursor-pointer"
                            wire:click="sortBy('due_date')">
                            <div class="flex items-center space-x-1">
                                <span>Date d'échéance</span>
                                @if($sortBy === 'due_date')
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                              d="{{ $sortDirection === 'asc' ? 'M5 15l7-7 7 7' : 'M19 9l-7 7-7-7' }}"></path>
                                    </svg>
                                @endif
                            </div>
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            Actions
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($invoices as $invoice)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->invoice_number }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->issue_date->format('d/m/Y') }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $invoice->company->name }}</div>
                                <div class="text-sm text-gray-500">{{ $invoice->company->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $invoice->subscription->plan->name }}</div>
                                <div class="text-sm text-gray-500">{{ number_format($invoice->subscription->plan->price, 2) }} €/mois</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ number_format($invoice->total_amount, 2) }} €</div>
                                @if($invoice->tax_amount > 0)
                                    <div class="text-sm text-gray-500">dont {{ number_format($invoice->tax_amount, 2) }} € TVA</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                @php
                                    $statusColors = [
                                        'draft' => 'bg-gray-100 text-gray-800',
                                        'sent' => 'bg-blue-100 text-blue-800',
                                        'paid' => 'bg-green-100 text-green-800',
                                        'overdue' => 'bg-red-100 text-red-800',
                                        'cancelled' => 'bg-gray-100 text-gray-800',
                                    ];
                                    $statusLabels = [
                                        'draft' => 'Brouillon',
                                        'sent' => 'Envoyée',
                                        'paid' => 'Payée',
                                        'overdue' => 'En retard',
                                        'cancelled' => 'Annulée',
                                    ];
                                @endphp
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusColors[$invoice->status] }}">
                                    {{ $statusLabels[$invoice->status] }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900">{{ $invoice->due_date->format('d/m/Y') }}</div>
                                @if($invoice->isOverdue() && !$invoice->isPaid())
                                    <div class="text-sm text-red-600">{{ $invoice->due_date->diffForHumans() }}</div>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <!-- Liens principaux -->
                                    <a href="{{ route('admin.invoices.show', $invoice) }}"
                                       class="text-indigo-600 hover:text-indigo-900">
                                        Voir
                                    </a>
                                    
                                    <a href="{{ route('admin.invoices.edit', $invoice) }}"
                                       class="text-purple-600 hover:text-purple-900">
                                        Modifier
                                    </a>
                                    
                                    <a href="{{ route('admin.invoices.pdf', $invoice) }}"
                                       target="_blank"
                                       class="text-blue-600 hover:text-blue-900">
                                        PDF
                                    </a>
                                    
                                    @if($invoice->status === 'draft')
                                        <button wire:click="sendInvoice({{ $invoice->id }})"
                                                class="text-green-600 hover:text-green-900">
                                            Envoyer
                                        </button>
                                    @endif
                                    
                                    @if(!$invoice->isPaid() && $invoice->status !== 'cancelled')
                                        <button wire:click="showPaymentModal({{ $invoice->id }})"
                                                class="text-yellow-600 hover:text-yellow-900">
                                            Paiement
                                        </button>
                                    @endif
                                    
                                    @if($invoice->status !== 'cancelled' && !$invoice->isPaid())
                                        <button wire:click="cancelInvoice({{ $invoice->id }})"
                                                class="text-red-600 hover:text-red-900"
                                                onclick="return confirm('Êtes-vous sûr de vouloir annuler cette facture ?')">
                                            Annuler
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                                Aucune facture trouvée.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="px-6 py-4 border-t border-gray-200">
            {{ $invoices->links() }}
        </div>
    </div>

    <!-- Modal de paiement -->
    @if($showPaymentModal)
        <div class="fixed inset-0 z-50 overflow-y-auto">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity"></div>

                <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form wire:submit="recordPayment">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <div class="sm:flex sm:items-start">
                                <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">
                                        Enregistrer un paiement
                                    </h3>
                                    
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Montant</label>
                                            <input type="number" step="0.01" wire:model="paymentAmount"
                                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('paymentAmount') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Méthode de paiement</label>
                                            <select wire:model="paymentMethod" class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                                <option value="manual">Manuel</option>
                                                <option value="bank_transfer">Virement bancaire</option>
                                                <option value="credit_card">Carte de crédit</option>
                                                <option value="paypal">PayPal</option>
                                                <option value="stripe">Stripe</option>
                                            </select>
                                            @error('paymentMethod') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Date de paiement</label>
                                            <input type="date" wire:model="paymentDate"
                                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('paymentDate') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">ID de transaction (optionnel)</label>
                                            <input type="text" wire:model="transactionId"
                                                   class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">
                                            @error('transactionId') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>

                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Notes (optionnel)</label>
                                            <textarea wire:model="paymentNotes" rows="3"
                                                      class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                                            @error('paymentNotes') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit"
                                    class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500 sm:ml-3 sm:w-auto sm:text-sm">
                                Enregistrer le paiement
                            </button>
                            <button type="button" wire:click="closePaymentModal"
                                    class="mt-3 w-full inline-flex justify-center rounded-lg border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Annuler
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endif
</div>
