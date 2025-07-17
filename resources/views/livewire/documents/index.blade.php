<div class="space-y-6">
    <!-- En-tête moderne sobre -->
    <div class="bg-white border-b border-gray-200 px-6 py-8">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">
                    Documents de Vente
                </h1>
                <p class="mt-1 text-sm text-gray-600">
                    Suivez vos devis, factures, et bons de livraison
                </p>
            </div>
            <div class="flex items-center gap-3">
                <a href="{{ route('documents.create') }}" 
                    
                   class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition-colors duration-200">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Nouveau Document
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

    <!-- Filtres et recherche -->
    <div class="mx-6">
        <div class="rounded-lg bg-white p-6 shadow-sm border border-gray-200">
            <h3 class="text-sm font-semibold text-gray-900 mb-4">Filtres et recherche</h3>
            <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <!-- Recherche -->
                <div class="sm:col-span-2">
                    <label for="search" class="block text-xs font-medium text-gray-700 mb-2">Rechercher</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                            </svg>
                        </div>
                        <input wire:model.live.debounce.300ms="search" 
                               type="text" 
                               placeholder="N° document, client..."
                               class="block w-full pl-10 pr-3 py-2.5 rounded-lg border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500 transition-colors duration-200">
                    </div>
                </div>
                
                <!-- Type de document -->
                <div>
                    <label for="typeFilter" class="block text-xs font-medium text-gray-700 mb-2">Type de document</label>
                    <select wire:model.live="typeFilter" 
                            class="block w-full rounded-lg border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500 transition-colors duration-200">
                        <option value="">Tous les types</option>
                        @foreach($documentTypes as $type)
                            <option value="{{ $type->value }}">{{ $type->label() }}</option>
                        @endforeach
                    </select>
                </div>
                
                <!-- Statut -->
                <div>
                    <label for="statusFilter" class="block text-xs font-medium text-gray-700 mb-2">Statut</label>
                    <select wire:model.live="statusFilter" 
                            class="block w-full rounded-lg border-gray-300 text-sm focus:border-gray-500 focus:ring-gray-500 transition-colors duration-200">
                        <option value="">Tous les statuts</option>
                        @foreach($documentStatuses as $status)
                            <option value="{{ $status->value }}">{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            
            <!-- Filtre factures en retard -->
            <div class="mt-4 pt-4 border-t border-gray-200">
                <div class="flex items-center gap-3">
                    <input id="showOverdueOnly" 
                           wire:model.live="showOverdueOnly" 
                           type="checkbox" 
                           class="h-4 w-4 rounded border-gray-300 text-gray-900 focus:ring-gray-500">
                    <label for="showOverdueOnly" class="text-sm font-medium text-gray-700">
                        Afficher uniquement les factures en retard
                    </label>
                </div>
            </div>
        </div>
    </div>

    <!-- Tableau des documents -->
    <div class="mx-6">
        <div class="overflow-hidden rounded-lg bg-white shadow-sm border border-gray-200">
            <!-- En-tête du tableau -->
            <div class="border-b border-gray-200 bg-gray-50 px-6 py-4">
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-semibold text-gray-900">Liste des Documents</h3>
                        <p class="text-xs text-gray-600 mt-1">{{ $documents->total() }} document(s) au total</p>
                    </div>
                    <div class="flex items-center gap-2 text-xs text-gray-500">
                        <div class="h-2 w-2 rounded-full bg-gray-400"></div>
                        <span>Documents commerciaux</span>
                    </div>
                </div>
            </div>

            <!-- Contenu du tableau -->
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="py-3 pl-6 pr-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Document
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Client
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Date d'émission
                            </th>
                            <th scope="col" class="px-3 py-3 text-left text-xs font-semibold text-gray-700 uppercase tracking-wide">
                                Date d'échéance
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
                        @forelse ($documents as $doc)
                            <tr wire:key="{{ $doc->id }}" class="group hover:bg-gray-50 transition-colors duration-200">
                                <td class="py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-lg bg-gray-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ $doc->document_number }}</p>
                                            <p class="text-xs text-gray-500">{{ $doc->type->label() }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $doc->customer->name }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-700">{{ $doc->document_date->format('d/m/Y') }}</div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm text-gray-700">
                                        @if($doc->due_date)
                                            {{ $doc->due_date->format('d/m/Y') }}
                                            @if($doc->due_date->isPast() && $doc->status !== \App\Enums\DocumentStatus::Paid)
                                                <span class="ml-1 text-xs text-red-600 font-medium">(En retard)</span>
                                            @endif
                                        @else
                                            <span class="text-gray-400">Non définie</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    <div class="text-sm font-semibold text-gray-900">
                                        {{ number_format($doc->total_amount, 0, ',', ' ') }} FCFA
                                    </div>
                                </td>
                                <td class="px-3 py-4">
                                    @php
                                        $statusConfig = match($doc->status) {
                                            \App\Enums\DocumentStatus::Paid => ['bg' => 'bg-green-100', 'text' => 'text-green-800', 'ring' => 'ring-green-600/20'],
                                            \App\Enums\DocumentStatus::PartiallyPaid => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-800', 'ring' => 'ring-yellow-600/20'],
                                            \App\Enums\DocumentStatus::Overdue => ['bg' => 'bg-red-100', 'text' => 'text-red-800', 'ring' => 'ring-red-600/20'],
                                            default => ['bg' => 'bg-gray-100', 'text' => 'text-gray-800', 'ring' => 'ring-gray-600/20'],
                                        };
                                    @endphp
                                    <span class="inline-flex items-center gap-1 rounded-full {{ $statusConfig['bg'] }} px-2 py-1 text-xs font-medium {{ $statusConfig['text'] }} ring-1 {{ $statusConfig['ring'] }}">
                                        <div class="h-1.5 w-1.5 rounded-full {{ str_replace('bg-', 'bg-', $statusConfig['bg']) === 'bg-green-100' ? 'bg-green-500' : (str_replace('bg-', 'bg-', $statusConfig['bg']) === 'bg-yellow-100' ? 'bg-yellow-500' : (str_replace('bg-', 'bg-', $statusConfig['bg']) === 'bg-red-100' ? 'bg-red-500' : 'bg-gray-500')) }}"></div>
                                        {{ $doc->status->label() }}
                                    </span>
                                </td>
                                <td class="relative py-4 pl-3 pr-6 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('documents.show', $doc) }}" 
                                            
                                           class="inline-flex items-center gap-1 rounded-lg bg-gray-100 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-200 transition-colors duration-200">
                                            <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            </svg>
                                            Voir
                                        </a>
                                        @if($doc->status === \App\Enums\DocumentStatus::Draft)
                                            <a href="{{ route('documents.edit', $doc) }}" 
                                                
                                               class="inline-flex items-center gap-1 rounded-lg bg-blue-100 px-3 py-1.5 text-xs font-medium text-blue-700 hover:bg-blue-200 transition-colors duration-200">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                                                </svg>
                                                Modifier
                                            </a>
                                        @endif
                                        @if($doc->due_date && $doc->due_date->isPast() && $doc->status !== \App\Enums\DocumentStatus::Paid)
                                            <div class="flex flex-col items-end gap-1">
                                                <button wire:click="markReminderSent({{ $doc->id }})" 
                                                        class="inline-flex items-center gap-1 rounded-lg bg-orange-100 px-3 py-1.5 text-xs font-medium text-orange-700 hover:bg-orange-200 transition-colors duration-200">
                                                    <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0021.75 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75z" />
                                                    </svg>
                                                    Relancer
                                                </button>
                                                @if($doc->last_reminder_sent_at)
                                                    <span class="text-xs text-gray-400">Dernière: {{ $doc->last_reminder_sent_at->format('d/m/y') }}</span>
                                                @endif
                                            </div>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-16 text-center">
                                    <div class="flex flex-col items-center gap-4">
                                        <div class="h-12 w-12 rounded-full bg-gray-100 flex items-center justify-center">
                                            <svg class="h-6 w-6 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-gray-900">Aucun document trouvé</p>
                                            <p class="text-xs text-gray-500 mt-1">Commencez par créer votre premier document de vente</p>
                                        </div>
                                        <a href="{{ route('documents.create') }}" 
                                            
                                           class="inline-flex items-center gap-2 rounded-lg bg-gray-900 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-gray-800 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                            </svg>
                                            Nouveau Document
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($documents->hasPages())
                <div class="border-t border-gray-200 bg-white px-6 py-4">
                    {{ $documents->links() }}
                </div>
            @endif
        </div>
    </div>
</div>