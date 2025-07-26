<div class="bg-white rounded-lg shadow-lg p-8 max-w-4xl mx-auto">
    <!-- En-tête -->
    <div class="flex justify-between items-start mb-8">
        <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">WondoStock</h1>
            <p class="text-gray-600">Plateforme SaaS de gestion d'inventaire</p>
            <div class="mt-4 text-sm text-gray-500">
                <p>www.wondostock.com</p>
                <p>contact@wondostock.com</p>
            </div>
        </div>
        <div class="text-right">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">FACTURE</h2>
            <div class="text-sm text-gray-600">
                <p><strong>N°:</strong> {{ $invoice->invoice_number }}</p>
                <p><strong>Date d'émission:</strong> {{ $invoice->issue_date->format('d/m/Y') }}</p>
                <p><strong>Date d'échéance:</strong> {{ $invoice->due_date->format('d/m/Y') }}</p>
            </div>
        </div>
    </div>

    <!-- Informations client -->
    <div class="grid grid-cols-2 gap-8 mb-8">
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Facturé à:</h3>
            <div class="text-gray-700">
                <p class="font-medium text-lg">{{ $invoice->company->name }}</p>
                @if($invoice->billing_address)
                    @foreach($invoice->billing_address as $line)
                        <p>{{ $line }}</p>
                    @endforeach
                @endif
            </div>
        </div>
        <div>
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Abonnement:</h3>
            <div class="text-gray-700">
                @if($invoice->subscription && $invoice->subscription->plan)
                    <p class="font-medium">{{ $invoice->subscription->plan->name }}</p>
                    <p class="text-sm">{{ $invoice->subscription->plan->description }}</p>
                    <p class="text-sm mt-2">
                        <span class="font-medium">Période:</span> 
                        {{ $invoice->subscription->starts_at->format('d/m/Y') }} - 
                        {{ $invoice->subscription->ends_at->format('d/m/Y') }}
                    </p>
                @else
                    <p class="text-gray-500">Aucun abonnement associé</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Détails de la facture -->
    <div class="border border-gray-200 rounded-lg overflow-hidden mb-8">
        <table class="w-full">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Description
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Montant HT
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        TVA
                    </th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                        Montant TTC
                    </th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                <tr>
                    <td class="px-6 py-4">
                        <div>
                            @if($invoice->subscription && $invoice->subscription->plan)
                                <p class="font-medium">Abonnement {{ $invoice->subscription->plan->name }}</p>
                                <p class="text-sm text-gray-500">
                                    Période: {{ $invoice->subscription->starts_at->format('d/m/Y') }} - 
                                    {{ $invoice->subscription->ends_at->format('d/m/Y') }}
                                </p>
                            @else
                                <p class="font-medium">Service d'abonnement WondoStock</p>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 text-right">
                        {{ number_format($invoice->amount, 0, ',', ' ') }} FCFA
                    </td>
                    <td class="px-6 py-4 text-right">
                        @if($invoice->tax_amount > 0)
                            {{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA
                        @else
                            -
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right font-medium">
                        {{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA
                    </td>
                </tr>
            </tbody>
        </table>
    </div>

    <!-- Totaux -->
    <div class="flex justify-end mb-8">
        <div class="w-80">
            <div class="bg-gray-50 p-6 rounded-lg">
                <div class="flex justify-between py-2">
                    <span class="font-medium">Sous-total HT:</span>
                    <span>{{ number_format($invoice->amount, 0, ',', ' ') }} FCFA</span>
                </div>
                @if($invoice->tax_amount > 0)
                    <div class="flex justify-between py-2 border-t border-gray-200">
                        <span class="font-medium">TVA:</span>
                        <span>{{ number_format($invoice->tax_amount, 0, ',', ' ') }} FCFA</span>
                    </div>
                @endif
                <div class="flex justify-between py-3 border-t-2 border-gray-300 text-lg font-bold">
                    <span>Total TTC:</span>
                    <span>{{ number_format($invoice->total_amount, 0, ',', ' ') }} FCFA</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Statut de paiement -->
    <div class="mb-8">
        <div class="flex items-center justify-between p-4 rounded-lg {{ $invoice->isPaid() ? 'bg-green-50 border border-green-200' : 'bg-yellow-50 border border-yellow-200' }}">
            <div class="flex items-center">
                @if($invoice->isPaid())
                    <svg class="w-6 h-6 text-green-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    <span class="font-medium text-green-900">Facture payée</span>
                @else
                    <svg class="w-6 h-6 text-yellow-600 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span class="font-medium text-yellow-900">
                        @if($invoice->isOverdue())
                            Facture en retard ({{ $invoice->due_date->diffInDays(now()) }} jour(s))
                        @else
                            En attente de paiement
                        @endif
                    </span>
                @endif
            </div>
            @if($invoice->paid_at)
                <span class="text-sm text-green-700">
                    Payée le {{ $invoice->paid_at->format('d/m/Y') }}
                </span>
            @endif
        </div>
    </div>

    <!-- Conditions de paiement -->
    <div class="border-t border-gray-200 pt-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Conditions de paiement</h3>
        <div class="text-sm text-gray-600 space-y-2">
            <p>• Paiement par virement bancaire ou carte de crédit</p>
            <p>• Aucun escompte accordé en cas de paiement anticipé</p>
            <p>• En cas de retard de paiement, des intérêts de retard pourront être appliqués</p>
            <p>• Tout retard de paiement supérieur à 30 jours peut entraîner la suspension du service</p>
        </div>
        
        @if($invoice->notes)
            <div class="mt-4">
                <h4 class="font-medium text-gray-900 mb-2">Notes:</h4>
                <p class="text-sm text-gray-600">{{ $invoice->notes }}</p>
            </div>
        @endif
    </div>

    <!-- Actions PDF -->
    <div class="mt-8 flex justify-center space-x-4">
        <button wire:click="downloadPDF" 
                class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-lg shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
            <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
            </svg>
            Télécharger PDF
        </button>
    </div>
</div>