<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Facture {{ $invoice->invoice_number }}</h1>
            <p class="text-gray-600">
                Détails de la facture pour {{ $invoice->company->name }}
            </p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.invoices.edit', $invoice) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                </svg>
                Modifier
            </a>
            <a href="{{ route('admin.invoices.index') }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour
            </a>
        </div>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Informations principales -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Détails de la facture -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <div class="flex justify-between items-start mb-6">
                    <div>
                        <h2 class="text-xl font-semibold text-gray-900 mb-2">{{ $invoice->invoice_number }}</h2>
                        <div class="space-y-1 text-sm text-gray-600">
                            <p><span class="font-medium">Date d'émission :</span> {{ $invoice->issue_date->format('d/m/Y') }}</p>
                            <p><span class="font-medium">Date d'échéance :</span> {{ $invoice->due_date->format('d/m/Y') }}</p>
                            @if ($invoice->paid_at)
                                <p><span class="font-medium">Payée le :</span> {{ $invoice->paid_at->format('d/m/Y') }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="text-right">
                        @if ($invoice->status === 'paid')
                            <span class="inline-flex items-center gap-1 rounded-full bg-green-100 px-3 py-1 text-sm font-medium text-green-800 ring-1 ring-green-600/20">
                                <div class="h-2 w-2 rounded-full bg-green-500"></div>
                                Payée
                            </span>
                        @elseif ($invoice->isOverdue())
                            <span class="inline-flex items-center gap-1 rounded-full bg-red-100 px-3 py-1 text-sm font-medium text-red-800 ring-1 ring-red-600/20">
                                <div class="h-2 w-2 rounded-full bg-red-500"></div>
                                En retard
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1 rounded-full bg-yellow-100 px-3 py-1 text-sm font-medium text-yellow-800 ring-1 ring-yellow-600/20">
                                <div class="h-2 w-2 rounded-full bg-yellow-500"></div>
                                En attente
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Informations client -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Informations client</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <h4 class="font-medium text-gray-900">{{ $invoice->company->name }}</h4>
                            <p class="text-sm text-gray-600 mt-1">{{ $invoice->company->legal_name }}</p>
                            @if ($invoice->company->email)
                                <p class="text-sm text-gray-600">{{ $invoice->company->email }}</p>
                            @endif
                            @if ($invoice->company->phone_number)
                                <p class="text-sm text-gray-600">{{ $invoice->company->phone_number }}</p>
                            @endif
                        </div>
                        @if ($invoice->billing_address)
                            <div>
                                <h4 class="font-medium text-gray-900 mb-2">Adresse de facturation</h4>
                                <div class="text-sm text-gray-600">
                                    @if (is_array($invoice->billing_address))
                                        @foreach ($invoice->billing_address as $line)
                                            <p>{{ $line }}</p>
                                        @endforeach
                                    @else
                                        <p>{{ $invoice->billing_address }}</p>
                                    @endif
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Détails de l'abonnement -->
                @if ($invoice->subscription)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Abonnement</h3>
                        <div class="bg-gray-50 rounded-lg p-4">
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                                <div>
                                    <span class="font-medium text-gray-700">Plan :</span>
                                    <span class="text-gray-900 ml-2">{{ $invoice->subscription->plan->name }}</span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Période :</span>
                                    <span class="text-gray-900 ml-2">
                                        {{ $invoice->subscription->starts_at->format('d/m/Y') }} - 
                                        {{ $invoice->subscription->ends_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <div>
                                    <span class="font-medium text-gray-700">Statut :</span>
                                    <span class="text-gray-900 ml-2 capitalize">{{ $invoice->subscription->status }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Montants -->
                <div class="border-t border-gray-200 pt-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Détail des montants</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <div class="space-y-2">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Montant HT :</span>
                                <span class="font-medium text-gray-900">{{ number_format($invoice->amount, 2) }}€</span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">TVA :</span>
                                <span class="font-medium text-gray-900">{{ number_format($invoice->tax_amount, 2) }}€</span>
                            </div>
                            <div class="border-t border-gray-300 pt-2 flex justify-between">
                                <span class="font-medium text-gray-900">Total TTC :</span>
                                <span class="font-bold text-gray-900 text-lg">{{ number_format($invoice->total_amount, 2) }}€</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Notes -->
                @if ($invoice->notes)
                    <div class="border-t border-gray-200 pt-6">
                        <h3 class="text-lg font-medium text-gray-900 mb-2">Notes</h3>
                        <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
                    </div>
                @endif
            </div>

            <!-- Historique des paiements -->
            @if ($invoice->payments->count() > 0)
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Historique des paiements</h3>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Montant</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Méthode</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Statut</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-200">
                                @foreach ($invoice->payments as $payment)
                                    <tr>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ $payment->created_at->format('d/m/Y H:i') }}
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900">
                                            {{ number_format($payment->amount, 2) }}€
                                        </td>
                                        <td class="px-4 py-2 text-sm text-gray-900 capitalize">
                                            {{ $payment->payment_method }}
                                        </td>
                                        <td class="px-4 py-2 text-sm">
                                            @if ($payment->status === 'completed')
                                                <span class="text-green-600 font-medium">Confirmé</span>
                                            @elseif ($payment->status === 'pending')
                                                <span class="text-yellow-600 font-medium">En attente</span>
                                            @else
                                                <span class="text-red-600 font-medium">Échoué</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif
        </div>

        <!-- Actions et informations secondaires -->
        <div class="space-y-6">
            <!-- Actions rapides -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Actions</h3>
                <div class="space-y-3">
                    @if (!$invoice->isPaid())
                        <button wire:click="markAsPaid"
                                class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                            Marquer comme payée
                        </button>
                    @else
                        <button wire:click="markAsUnpaid"
                                class="w-full bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded-md text-sm">
                            Marquer comme non payée
                        </button>
                    @endif
                    
                    <button wire:click="sendInvoice"
                            class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-md text-sm">
                        Envoyer par email
                    </button>
                    
                    <button wire:click="viewPdf"
                            class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm">
                        Voir PDF
                    </button>
                    
                    <button wire:click="downloadPdf"
                            class="w-full bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-md text-sm">
                        Télécharger PDF
                    </button>
                </div>
            </div>

            <!-- Informations système -->
            <div class="bg-white rounded-lg shadow-sm p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Informations système</h3>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">ID :</span>
                        <span class="text-gray-900">{{ $invoice->id }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Créée le :</span>
                        <span class="text-gray-900">{{ $invoice->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Modifiée le :</span>
                        <span class="text-gray-900">{{ $invoice->updated_at->format('d/m/Y H:i') }}</span>
                    </div>
                </div>
            </div>

            <!-- Statistiques rapides -->
            @if ($invoice->isOverdue())
                <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">Facture en retard</h3>
                            <p class="text-sm text-red-700 mt-1">
                                Cette facture est en retard de {{ $invoice->due_date->diffInDays(now()) }} jour(s).
                            </p>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>