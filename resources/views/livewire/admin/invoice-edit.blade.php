<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Modifier la Facture</h1>
            <p class="text-gray-600">Modifier la facture "{{ $invoice->invoice_number }}"</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.invoices.show', $invoice) }}" 
               class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                </svg>
                Voir détails
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

    <div class="bg-white rounded-lg shadow-sm">
        <form wire:submit.prevent="save" class="p-6">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Informations de base -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Informations de base</h3>
                        
                        <!-- Numéro de facture -->
                        <div class="mb-4">
                            <label for="invoice_number" class="block text-sm font-medium text-gray-700 mb-2">
                                Numéro de facture *
                            </label>
                            <input type="text" 
                                   id="invoice_number"
                                   wire:model="invoice_number" 
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="INV-2024-0001">
                            @error('invoice_number') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Entreprise -->
                        <div class="mb-4">
                            <label for="company_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Entreprise *
                            </label>
                            <select id="company_id" 
                                    wire:model="company_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Sélectionner une entreprise</option>
                                @foreach ($companies as $company)
                                    <option value="{{ $company->id }}">
                                        {{ $company->name }}
                                        @if ($company->email)
                                            ({{ $company->email }})
                                        @endif
                                    </option>
                                @endforeach
                            </select>
                            @error('company_id') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Abonnement -->
                        <div class="mb-4">
                            <label for="subscription_id" class="block text-sm font-medium text-gray-700 mb-2">
                                Abonnement (optionnel)
                            </label>
                            <select id="subscription_id" 
                                    wire:model="subscription_id"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="">Aucun abonnement</option>
                                @foreach ($subscriptions as $subscription)
                                    <option value="{{ $subscription->id }}">
                                        {{ $subscription->company->name }} - {{ $subscription->plan->name }}
                                        ({{ $subscription->starts_at->format('d/m/Y') }} - {{ $subscription->ends_at->format('d/m/Y') }})
                                    </option>
                                @endforeach
                            </select>
                            @error('subscription_id') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>

                        <!-- Statut -->
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Statut *
                            </label>
                            <select id="status" 
                                    wire:model="status"
                                    class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                <option value="pending">En attente</option>
                                <option value="paid">Payée</option>
                                <option value="cancelled">Annulée</option>
                            </select>
                            @error('status') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Montants et dates -->
                <div class="space-y-6">
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 mb-4">Montants et dates</h3>
                        
                        <!-- Dates -->
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label for="issue_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date d'émission *
                                </label>
                                <input type="date" 
                                       id="issue_date"
                                       wire:model="issue_date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('issue_date') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label for="due_date" class="block text-sm font-medium text-gray-700 mb-2">
                                    Date d'échéance *
                                </label>
                                <input type="date" 
                                       id="due_date"
                                       wire:model="due_date" 
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                @error('due_date') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>
                        </div>

                        <!-- Montants -->
                        <div class="space-y-4">
                            <div>
                                <label for="amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant HT (€) *
                                </label>
                                <input type="number" 
                                       id="amount"
                                       wire:model.live="amount" 
                                       step="0.01"
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.00">
                                @error('amount') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label for="tax_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant TVA (€) *
                                </label>
                                <input type="number" 
                                       id="tax_amount"
                                       wire:model.live="tax_amount" 
                                       step="0.01"
                                       min="0"
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.00">
                                @error('tax_amount') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                            </div>

                            <div>
                                <label for="total_amount" class="block text-sm font-medium text-gray-700 mb-2">
                                    Montant total TTC (€) *
                                </label>
                                <input type="number" 
                                       id="total_amount"
                                       wire:model="total_amount" 
                                       step="0.01"
                                       min="0"
                                       readonly
                                       class="w-full px-3 py-2 border border-gray-300 rounded-md bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                       placeholder="0.00">
                                @error('total_amount') 
                                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                                @enderror
                                <p class="text-xs text-gray-500 mt-1">Calculé automatiquement (HT + TVA)</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Adresse de facturation -->
            <div class="mt-6">
                <label for="billing_address" class="block text-sm font-medium text-gray-700 mb-2">
                    Adresse de facturation
                </label>
                <textarea id="billing_address"
                          wire:model="billing_address" 
                          rows="4"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Adresse de facturation (une ligne par champ)..."></textarea>
                @error('billing_address') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
                <p class="text-xs text-gray-500 mt-1">Séparez chaque ligne d'adresse par un retour à la ligne</p>
            </div>

            <!-- Notes -->
            <div class="mt-6">
                <label for="notes" class="block text-sm font-medium text-gray-700 mb-2">
                    Notes internes
                </label>
                <textarea id="notes"
                          wire:model="notes" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Notes et commentaires internes..."></textarea>
                @error('notes') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Informations sur la facture actuelle -->
            <div class="mt-6 p-4 bg-gray-50 border border-gray-200 rounded-md">
                <h4 class="text-sm font-medium text-gray-900 mb-2">Informations actuelles</h4>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                    <div>
                        <span class="font-medium text-gray-700">Créée le :</span>
                        <span class="text-gray-900 ml-2">{{ $invoice->created_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    <div>
                        <span class="font-medium text-gray-700">Dernière modification :</span>
                        <span class="text-gray-900 ml-2">{{ $invoice->updated_at->format('d/m/Y à H:i') }}</span>
                    </div>
                    @if ($invoice->paid_at)
                        <div>
                            <span class="font-medium text-gray-700">Payée le :</span>
                            <span class="text-gray-900 ml-2">{{ $invoice->paid_at->format('d/m/Y à H:i') }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.invoices.show', $invoice) }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">
                    Enregistrer les modifications
                </button>
            </div>
        </form>
    </div>
</div>