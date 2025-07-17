<div class="min-h-screen bg-gray-50">
    {{-- Header avec sélection du magasin --}}
    <div class="bg-white shadow-sm border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                {{-- Titre et sélecteur de magasin --}}
                <div class="flex items-center space-x-4">
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ __('app.store_activity.title') }}</h1>
                        @if($selectedStore)
                            <p class="text-sm text-gray-500">{{ $selectedStore->name }}</p>
                        @endif
                    </div>

                    {{-- Sélecteur de magasin --}}
                    @if(count($stores) > 1)
                        <div class="relative">
                            <select wire:model.live="selectedStoreId" 
                                    class="appearance-none bg-white border border-gray-300 rounded-lg px-4 py-2 pr-8 text-sm font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach($stores as $store)
                                    <option value="{{ $store->id }}">{{ $store->name }}</option>
                                @endforeach
                            </select>
                            <div class="absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none">
                                <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Contrôles de période et rafraîchissement --}}
                @if($selectedStore)
                    <div class="flex items-center space-x-3">
                        {{-- Sélecteur de période --}}
                        <div class="flex bg-gray-100 rounded-lg p-1">
                            @foreach($periods as $value => $label)
                                <button wire:click="selectPeriod('{{ $value }}')"
                                        class="px-3 py-1 text-xs font-medium rounded-md transition-colors
                                               {{ $selectedPeriod === $value 
                                                  ? 'bg-white text-blue-600 shadow-sm' 
                                                  : 'text-gray-600 hover:text-gray-900' }}">
                                    {{ $label }}
                                </button>
                            @endforeach
                        </div>

                        {{-- Bouton de rafraîchissement automatique --}}
                        <button wire:click="toggleAutoRefresh"
                                class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors
                                       {{ $autoRefresh ? 'bg-blue-50 text-blue-600 border-blue-300' : '' }}">
                            <svg class="h-5 w-5 {{ $autoRefresh ? 'animate-spin' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>

                        {{-- Bouton rafraîchissement manuel --}}
                        <button wire:click="refreshData"
                                class="p-2 rounded-lg border border-gray-300 text-gray-600 hover:bg-gray-50 transition-colors">
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </div>

    @if($noStoreSelected ?? false)
        {{-- Écran de sélection de magasin --}}
        <div class="flex items-center justify-center py-12">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                </svg>
                <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('app.store_activity.no_store_selected') }}</h3>
                <p class="mt-1 text-sm text-gray-500">{{ __('app.store_activity.select_store_message') }}</p>
                @if(count($stores) > 0)
                    <div class="mt-6 grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                        @foreach($stores as $store)
                            <button wire:click="selectStore({{ $store->id }})"
                                    class="relative rounded-lg border border-gray-300 bg-white px-6 py-5 shadow-sm hover:border-gray-400 transition-colors">
                                <div class="text-sm font-medium text-gray-900">{{ $store->name }}</div>
                                <div class="text-sm text-gray-500">{{ $store->address ?? __('app.store_activity.address_not_defined') }}</div>
                            </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    @else
        {{-- Interface principale avec onglets --}}
        <div class="px-4 sm:px-6 lg:px-8 py-6">
            {{-- Navigation par onglets --}}
            <div class="border-b border-gray-200 mb-6">
                <nav class="-mb-px flex space-x-8">
                    @foreach($tabs as $tabKey => $tab)
                        <button wire:click="selectTab('{{ $tabKey }}')"
                                class="py-2 px-1 border-b-2 font-medium text-sm transition-colors
                                       {{ $selectedTab === $tabKey 
                                          ? 'border-blue-500 text-blue-600' 
                                          : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                            <div class="flex items-center space-x-2">
                                @if($tabKey === 'overview')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2 2v10z"></path>
                                    </svg>
                                @elseif($tabKey === 'stock')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                    </svg>
                                @elseif($tabKey === 'sales')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                    </svg>
                                @elseif($tabKey === 'transfers')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7h12m0 0l-4-4m4 4l-4 4m0 6H4m0 0l4 4m-4-4l4-4"></path>
                                    </svg>
                                @elseif($tabKey === 'analytics')
                                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path>
                                    </svg>
                                @endif
                                <span>{{ $tab['label'] }}</span>
                            </div>
                        </button>
                    @endforeach
                </nav>
            </div>

            {{-- Contenu des onglets --}}
            <div class="space-y-6">
                @if($selectedTab === 'overview')
                    @include('livewire.store-activity.partials.overview')
                @elseif($selectedTab === 'stock')
                    @include('livewire.store-activity.partials.stock')
                @elseif($selectedTab === 'sales')
                    @include('livewire.store-activity.partials.sales')
                @elseif($selectedTab === 'transfers')
                    @include('livewire.store-activity.partials.transfers')
                @elseif($selectedTab === 'analytics')
                    @include('livewire.store-activity.partials.analytics')
                @endif
            </div>
        </div>
    @endif

    {{-- JavaScript pour le rafraîchissement automatique --}}
    <script>
        let refreshInterval;

        document.addEventListener('livewire:initialized', () => {
            Livewire.on('start-auto-refresh', (interval) => {
                if (refreshInterval) clearInterval(refreshInterval);
                refreshInterval = setInterval(() => {
                    Livewire.dispatch('refreshData');
                }, interval * 1000);
            });

            Livewire.on('stop-auto-refresh', () => {
                if (refreshInterval) {
                    clearInterval(refreshInterval);
                    refreshInterval = null;
                }
            });
        });

        // Nettoyer l'interval quand on quitte la page
        window.addEventListener('beforeunload', () => {
            if (refreshInterval) clearInterval(refreshInterval);
        });
    </script>
</div>