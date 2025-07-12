<div>
    <!-- En-tête de la page -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">Documents de Vente</h2>
            <p class="mt-1 text-sm text-gray-500">Suivez vos devis, factures, et bons de livraison.</p>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <a href="{{ route('documents.create') }}" wire:navigate class="inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">
                Nouveau Document
            </a>
        </div>
    </div>
    
    <!-- Filtres et recherche -->
    <div class="mt-6 grid grid-cols-1 gap-x-4 gap-y-4 sm:grid-cols-4">
        <div class="sm:col-span-2">
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Rechercher (N°, client...)" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
        </div>
        <div>
            <select wire:model.live="typeFilter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Tous les types</option>
                @foreach($documentTypes as $type)
                    <option value="{{ $type->value }}">{{ ucfirst(str_replace('_', ' ', $type->value)) }}</option>
                @endforeach
            </select>
        </div>
         <div>
            <select wire:model.live="statusFilter" class="block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                <option value="">Tous les statuts</option>
                 @foreach($documentStatuses as $status)
                    <option value="{{ $status->value }}">{{ ucfirst(str_replace('_', ' ', $status->value)) }}</option>
                @endforeach
            </select>
        </div>
        {{-- Nouveau filtre pour les factures en retard --}}
        <div class="sm:col-span-2 flex items-end">
            <div class="relative flex items-start">
                <div class="flex h-6 items-center">
                    <input id="showOverdueOnly" wire:model.live="showOverdueOnly" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                </div>
                <div class="ml-3 text-sm leading-6">
                    <label for="showOverdueOnly" class="font-medium text-gray-900">Afficher uniquement les factures en retard</label>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des documents -->
    <div class="mt-8 flow-root">
        <div class="inline-block min-w-full py-2 align-middle sm:px-1">
            <div class="overflow-hidden shadow ring-1 ring-black ring-opacity-5 sm:rounded-lg">
                <table class="min-w-full divide-y divide-gray-300">
                    <thead class="bg-gray-50"><tr>
                        <th class="py-3.5 pl-4 pr-3 text-left text-sm font-semibold text-gray-900 sm:pl-6">Document</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Client</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date d'émission</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Date d'échéance</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Total</th>
                        <th class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900">Statut</th>
                        <th class="relative py-3.5 pl-3 pr-4 font-semibold text-gray-900 sm:pr-6">Actions</th></tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200 bg-white">
                        @forelse ($documents as $doc)
                            <tr wire:key="{{ $doc->id }}">
                                <td class="whitespace-nowrap py-4 pl-4 pr-3 text-sm sm:pl-6">
                                    <div class="font-medium text-gray-900">{{ $doc->document_number }}</div>
                                    <div class="text-gray-500">{{ ucfirst(str_replace('_', ' ', $doc->type->value)) }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doc->customer->name }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doc->document_date->format('d/m/Y') }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">{{ $doc->due_date }}</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500 font-semibold">{{ number_format($doc->total_amount, 0, ',', ' ') }} FCFA</td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    @php
                                        $statusClasses = match($doc->status) {
                                            \App\Enums\DocumentStatus::Paid => 'bg-green-100 text-green-700',
                                            \App\Enums\DocumentStatus::PartiallyPaid => 'bg-yellow-100 text-yellow-700',
                                            \App\Enums\DocumentStatus::Overdue => 'bg-red-100 text-red-700',
                                            default => 'bg-gray-100 text-gray-700',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center rounded-md px-2 py-1 text-xs font-medium {{ $statusClasses }}">{{ ucfirst(str_replace('_', ' ', $doc->status->value)) }}</span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                    <a href="{{ route('documents.show', $doc) }}" wire:navigate class="text-indigo-600 hover:text-indigo-900">Voir</a>
                                    @if($doc->status === \App\Enums\DocumentStatus::Draft)
                                        <a href="{{ route('documents.edit', $doc) }}" wire:navigate class="ml-4 text-indigo-600 hover:text-indigo-900">Modifier</a>
                                    @endif
                                    {{-- Nouveau bouton pour les relances --}}
                                    @if($doc->due_date < now() && $doc->status !== \App\Enums\DocumentStatus::Paid)
                                        <button wire:click="markReminderSent({{ $doc->id }})" class="ml-4 text-gray-600 hover:text-gray-900">Relancer</button>
                                        @if($doc->last_reminder_sent_at)
                                            <span class="block text-xs text-gray-400">Dernière: {{ $doc->last_reminder_sent_at->format('d/m/y') }}</span>
                                        @endif
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="6" class="text-center py-10 text-gray-500">Aucun document trouvé.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <div class="mt-4">{{ $documents->links() }}</div>
        </div>
    </div>
</div>