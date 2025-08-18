<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'WondoStock')</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .font-inter { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .sidebar-scroll { 
            scrollbar-width: thin; 
            scrollbar-color: #d1d5db #f9fafb; 
        }
        .sidebar-scroll::-webkit-scrollbar { 
            width: 6px; 
        }
        .sidebar-scroll::-webkit-scrollbar-track { 
            background: #f9fafb; 
            border-radius: 3px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb { 
            background: #d1d5db; 
            border-radius: 3px; 
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { 
            background: #9ca3af; 
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.8);
        }
    </style>
</head>
<body class="h-full bg-gray-50 font-inter antialiased">
    <div x-data="{ sidebarOpen: false }" @keydown.escape.window="sidebarOpen = false" class="h-full">
        <!-- Mobile sidebar backdrop -->
        <div x-show="sidebarOpen" 
             class="fixed inset-0 z-40 lg:hidden" 
             x-transition:enter="transition-opacity ease-linear duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition-opacity ease-linear duration-300"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             style="display: none;">
            <div class="fixed inset-0 bg-gray-900/20 backdrop-blur-sm" @click="sidebarOpen = false"></div>
        </div>

        <!-- Desktop sidebar -->
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-72 lg:flex-col">
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-sm border-r border-gray-200 sidebar-scroll">
                <!-- Logo et informations entreprise -->
                <div class="flex h-20 shrink-0 items-center border-b border-gray-100 px-2">
                    <a href="{{ route('dashboard') }}"  class="flex items-center space-x-3 group w-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 shadow-lg group-hover:shadow-xl group-hover:from-emerald-600 group-hover:to-emerald-700 transition-all duration-300">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-lg font-bold text-gray-900 group-hover:text-emerald-700 transition-colors duration-200 truncate">WondoStock</div>
                            @if(auth()->user()->company)
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="h-2 w-2 rounded-full bg-green-500 animate-pulse"></div>
                                    <span class="text-xs font-medium text-gray-600 truncate">{{ auth()->user()->company->name }}</span>
                                </div>
                            @endif
                        </div>
                    </a>
                </div>

                <!-- Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Main Navigation -->
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Navigation Principale</div>
                            <ul role="list" class="space-y-1">
                                @can('view_dashboard_stats')
                                <li>
                                    <a href="{{ route('dashboard') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                        </svg>
                                        Tableau de bord
                                    </a>
                                </li>
                                @endcan
                                
                                @can('view_dashboard_stats')
                                <li>
                                    <a href="{{ route('store-activity.dashboard') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('store-activity.*') ? 'bg-blue-50 text-blue-700 border-r-2 border-blue-600' : 'text-gray-700 hover:text-blue-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
                                        </svg>
                                        Activité Magasin
                                        <span class="ml-auto inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                            Nouveau
                                        </span>
                                    </a>
                                </li>
                                @endcan
                                @can('manage_stores')
                                <li>
                                    <a href="{{ route('stores.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('stores.*') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                                        </svg>
                                        Magasins
                                    </a>
                                </li>
                                @endcan

                                <!-- Products Dropdown -->
                                @if(auth()->user()->can('view_products') || auth()->user()->can('manage_products'))
                                <li x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            type="button"
                                            class="group flex w-full items-center justify-between gap-x-3 rounded-lg p-3 text-left text-sm font-medium leading-6 text-gray-700 hover:text-emerald-700 hover:bg-gray-50 transition-all duration-200"
                                            :class="{ 'bg-gray-50 text-emerald-700': open }"
                                            :aria-expanded="open">
                                        <div class="flex gap-x-3">
                                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                                            </svg>
                                            Produits
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                        </svg>
                                    </button>
                                    <ul x-show="open" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                                        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                        x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                                        class="mt-2 space-y-1 pl-8"
                                        style="display: none;">
                                        @can('view_products')
                                        <li>
                                            <a href="{{ route('products.index') }}" 
                                               class="block rounded-md py-2 px-3 text-sm leading-6 {{ request()->routeIs('products.index') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                                Liste des Produits
                                            </a>
                                        </li>
                                        @endcan
                                        @can('manage_products')
                                        <li>
                                            <a href="{{ route('products.print-labels') }}" 
                                               class="block rounded-md py-2 px-3 text-sm leading-6 {{ request()->routeIs('products.print-labels') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                                Imprimer Étiquettes
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('products.settings') }}" 
                                               class="block rounded-md py-2 px-3 text-sm leading-6 {{ request()->routeIs('products.settings') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                                Paramètres
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                @endif

                                <!-- Stock Dropdown -->
                                @if(auth()->user()->can('manage_inventory') || auth()->user()->can('transfer_stock'))
                                <li x-data="{ open: false }">
                                    <button @click="open = !open" 
                                            type="button"
                                            class="group flex w-full items-center justify-between gap-x-3 rounded-lg p-3 text-left text-sm font-medium leading-6 text-gray-700 hover:text-emerald-700 hover:bg-gray-50 transition-all duration-200"
                                            :class="{ 'bg-gray-50 text-emerald-700': open }"
                                            :aria-expanded="open">
                                        <div class="flex gap-x-3">
                                            <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m6-3a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125C17.25 11.25 18 10.5 18 9.375v-2.25"/>
                                            </svg>
                                            Inventaire
                                        </div>
                                        <svg class="h-4 w-4 shrink-0 transition-transform duration-200" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                                        </svg>
                                    </button>
                                    <ul x-show="open" 
                                        x-transition:enter="transition ease-out duration-200"
                                        x-transition:enter-start="transform opacity-0 scale-95 -translate-y-2"
                                        x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                                        x-transition:leave="transition ease-in duration-150"
                                        x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                                        x-transition:leave-end="transform opacity-0 scale-95 -translate-y-2"
                                        class="mt-2 space-y-1 pl-8"
                                        style="display: none;">
                                        @can('manage_inventory')
                                        <li>
                                            <a href="{{ route('stock.entry') }}" 
                                               class="block rounded-md py-2 px-3 text-sm leading-6 {{ request()->routeIs('stock.entry') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                                Entrée de Stock
                                            </a>
                                        </li>
                                        <li>
                                            <a href="{{ route('stock.movements.index') }}" 
                                               class="block rounded-md py-2 px-3 text-sm leading-6 {{ request()->routeIs('stock.movements.*') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                                Mouvements
                                            </a>
                                        </li>
                                        @endcan
                                        @can('transfer_stock')
                                        <li>
                                            <a href="{{ route('stock.transfer') }}" 
                                               class="block rounded-md py-2 px-3 text-sm leading-6 {{ request()->routeIs('stock.transfer') ? 'text-emerald-700 bg-emerald-50' : 'text-gray-600 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                                Transferts
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li>
                                @endif

                                @can('manage_customers')
                                <li>
                                    <a href="{{ route('customers.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('customers.*') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                        </svg>
                                        Clients
                                    </a>
                                </li>
                                @endcan

                                @hasrole('Super-Administrateur|Administrateur|Gérant de Magasin')
                                <li>
                                    <a href="{{ route('suppliers.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('suppliers.*') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m6-3a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125C17.25 11.25 18 10.5 18 9.375v-2.25M6 10.5a.75.75 0 01-.75-.75V6.75a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v3a.75.75 0 01-.75.75H6zM6 21.75a.75.75 0 01-.75-.75v-3a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v3a.75.75 0 01-.75.75H6z"/>
                                        </svg>
                                        Fournisseurs
                                    </a>
                                </li>
                                @endhasrole

                                @if(auth()->user()->can('create_sales_documents') || auth()->user()->can('view_all_sales_documents'))
                                <li>
                                    <a href="{{ route('documents.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('documents.*') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        Ventes
                                    </a>
                                </li>
                                @endif

                                @hasrole('Super-Administrateur|Administrateur|Gérant de Magasin')
                                <li>
                                    <a href="{{ route('purchases.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('purchases.*') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119.993zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                        </svg>
                                        Achats
                                    </a>
                                </li>
                                @endhasrole

                                @if(auth()->user()->can('view_store_reports') || auth()->user()->can('view_global_reports'))
                                <li>
                                    <a href="{{ route('reports.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-700 border-r-2 border-emerald-600' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                        </svg>
                                        Rapports
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>

                        <!-- Settings Section -->
                        @if(auth()->user()->can('manage_settings') || auth()->user()->can('manage_users') || auth()->user()->can('manage_subscriptions'))
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Paramètres</div>
                            <ul role="list" class="space-y-1">
                                @can('manage_settings')
                                <li>
                                    <a href="{{ route('settings.company.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.company.*') ? 'bg-violet-50 text-violet-700 border-r-2 border-violet-600' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        Entreprise
                                    </a>
                                </li>
                                @endcan
                                @can('manage_users')
                                <li>
                                    <a href="{{ route('settings.users.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.users.*') ? 'bg-violet-50 text-violet-700 border-r-2 border-violet-600' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                        </svg>
                                        Utilisateurs
                                    </a>
                                </li>
                                @endcan
                                @can('feature-roles-permissions')
                                <li>
                                    <a href="{{ route('settings.roles.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.roles.*') ? 'bg-violet-50 text-violet-700 border-r-2 border-violet-600' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 5.25a8.25 8.25 0 01-16.5 0 8.25 8.25 0 0116.5 0z"/>
                                        </svg>
                                        Rôles & Permissions
                                    </a>
                                </li>
                                @endcan
                                @can('manage_subscriptions')
                                <li>
                                    <a href="{{ route('settings.subscription.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.subscription.*') ? 'bg-violet-50 text-violet-700 border-r-2 border-violet-600' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                                        </svg>
                                        Abonnement
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif

                        <!-- Administration Globale -->
                        @if(auth()->user()->is_global_admin)
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Administration</div>
                            <ul role="list" class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.companies.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.companies.*') ? 'bg-red-50 text-red-700 border-r-2 border-red-600' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        Gestion Entreprises
                                    </a>
                                </li>
                            </ul>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Mobile sidebar -->
        <div x-show="sidebarOpen" 
             class="relative z-50 lg:hidden" 
             x-transition:enter="transition ease-in-out duration-300 transform"
             x-transition:enter-start="-translate-x-full"
             x-transition:enter-end="translate-x-0"
             x-transition:leave="transition ease-in-out duration-300 transform"
             x-transition:leave-start="translate-x-0"
             x-transition:leave-end="-translate-x-full"
             style="display: none;">
            <div class="flex w-72 max-w-xs flex-col grow gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-lg border-r border-gray-200 sidebar-scroll">
                <!-- Mobile Logo -->
                <div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-100">
                    <a href="{{ route('dashboard') }}"  class="flex items-center space-x-3 group">
                        <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-emerald-600 shadow-sm">
                            <svg class="h-6 w-6 text-white" fill="currentColor" viewBox="0 0 24 24">
                                <path d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"/>
                            </svg>
                        </div>
                        <span class="text-xl font-semibold text-gray-900">WondoStock</span>
                    </a>
                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false" 
                            type="button"
                            class="lg:hidden rounded-md p-2 text-gray-400 hover:text-gray-600 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-colors duration-200">
                        <span class="sr-only">Fermer la sidebar</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Navigation - Same structure as desktop but simplified -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Main Navigation Mobile -->
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Navigation Principale</div>
                            <ul role="list" class="space-y-1">
                                @can('view_dashboard_stats')
                                <li>
                                    <a href="{{ route('dashboard') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/>
                                        </svg>
                                        Tableau de bord
                                    </a>
                                </li>
                                @endcan
                                
                                @can('view_dashboard_stats')
                                <li>
                                    <a href="{{ route('store-activity.dashboard') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('store-activity.*') ? 'bg-blue-50 text-blue-700' : 'text-gray-700 hover:text-blue-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/>
                                        </svg>
                                        Activité Magasin
                                        <span class="ml-auto inline-flex items-center rounded-full bg-blue-100 px-2 py-0.5 text-xs font-medium text-blue-800">
                                            Nouveau
                                        </span>
                                    </a>
                                </li>
                                @endcan
                                @can('manage_stores')
                                <li>
                                    <a href="{{ route('stores.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('stores.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z"/>
                                        </svg>
                                        Magasins
                                    </a>
                                </li>
                                @endcan
                                <!-- Simplified mobile navigation without dropdowns -->
                                @can('view_products')
                                <li>
                                    <a href="{{ route('products.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('products.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"/>
                                        </svg>
                                        Produits
                                    </a>
                                </li>
                                @endcan
                                @can('manage_inventory')
                                <li>
                                    <a href="{{ route('stock.entry') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('stock.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m6-3a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125C17.25 11.25 18 10.5 18 9.375v-2.25"/>
                                        </svg>
                                        Inventaire
                                    </a>
                                </li>
                                @endcan
                                @can('manage_customers')
                                <li>
                                    <a href="{{ route('customers.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('customers.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                        </svg>
                                        Clients
                                    </a>
                                </li>
                                @endcan
                                @hasrole('Super-Administrateur|Administrateur|Gérant de Magasin')
                                <li>
                                    <a href="{{ route('suppliers.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('suppliers.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 18.75a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 01-1.125-1.125V14.25m6-3a1.5 1.5 0 01-3 0m3 0a1.5 1.5 0 00-3 0m3 0h1.125C17.25 11.25 18 10.5 18 9.375v-2.25M6 10.5a.75.75 0 01-.75-.75V6.75a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v3a.75.75 0 01-.75.75H6zM6 21.75a.75.75 0 01-.75-.75v-3a.75.75 0 01.75-.75h2.25a.75.75 0 01.75.75v3a.75.75 0 01-.75.75H6z"/>
                                        </svg>
                                        Fournisseurs
                                    </a>
                                </li>
                                @endhasrole
                                @if(auth()->user()->can('create_sales_documents') || auth()->user()->can('view_all_sales_documents'))
                                <li>
                                    <a href="{{ route('documents.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('documents.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        Ventes
                                    </a>
                                </li>
                                @endif
                                @hasrole('Super-Administrateur|Administrateur|Gérant de Magasin')
                                <li>
                                    <a href="{{ route('purchases.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('purchases.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119.993zM8.625 10.5a.375.375 0 11-.75 0 .375.375 0 01.75 0zm7.5 0a.375.375 0 11-.75 0 .375.375 0 01.75 0z"/>
                                        </svg>
                                        Achats
                                    </a>
                                </li>
                                @endhasrole
                                @if(auth()->user()->can('view_store_reports') || auth()->user()->can('view_global_reports'))
                                <li>
                                    <a href="{{ route('reports.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('reports.*') ? 'bg-emerald-50 text-emerald-700' : 'text-gray-700 hover:text-emerald-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                        </svg>
                                        Rapports
                                    </a>
                                </li>
                                @endif
                            </ul>
                        </li>

                        <!-- Settings Section Mobile -->
                        @if(auth()->user()->can('manage_settings') || auth()->user()->can('manage_users') || auth()->user()->can('manage_subscriptions'))
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Paramètres</div>
                            <ul role="list" class="space-y-1">
                                @can('manage_settings')
                                <li>
                                    <a href="{{ route('settings.company.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.company.*') ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        Entreprise
                                    </a>
                                </li>
                                @endcan
                                @can('manage_users')
                                <li>
                                    <a href="{{ route('settings.users.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.users.*') ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/>
                                        </svg>
                                        Utilisateurs
                                    </a>
                                </li>
                                @endcan
                                @can('feature-roles-permissions')
                                <li>
                                    <a href="{{ route('settings.roles.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.roles.*') ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 5.25a8.25 8.25 0 01-16.5 0 8.25 8.25 0 0116.5 0z"/>
                                        </svg>
                                        Rôles & Permissions
                                    </a>
                                </li>
                                @endcan
                                @can('manage_subscriptions')
                                <li>
                                    <a href="{{ route('settings.subscription.index') }}" 
                                       @click="sidebarOpen = false"
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('settings.subscription.*') ? 'bg-violet-50 text-violet-700' : 'text-gray-700 hover:text-violet-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                                        </svg>
                                        Abonnement
                                    </a>
                                </li>
                                @endcan
                            </ul>
                        </li>
                        @endif
                    </ul>
                </nav>
            </div>
        </div>

        <!-- Main content area -->
        <div class="lg:pl-72">
            <!-- Top navigation -->
            <div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 glass-effect px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" 
                        @click="sidebarOpen = true" 
                        class="-m-2.5 p-2.5 text-gray-400 lg:hidden hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-emerald-500 transition-colors duration-200">
                    <span class="sr-only">Ouvrir la sidebar</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <!-- Separator -->
                <div class="h-6 w-px bg-gray-200 lg:hidden"></div>

                <!-- Breadcrumb -->
                <nav class="flex flex-1" aria-label="Breadcrumb">
                    <ol role="list" class="flex items-center space-x-4">
                        <li>
                            <div class="flex">
                                <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-500 hover:text-gray-700 transition-colors duration-200">
                                    Accueil
                                </a>
                            </div>
                        </li>
                        @if(!request()->routeIs('dashboard'))
                        <li>
                            <div class="flex items-center">
                                <svg class="h-5 w-5 flex-shrink-0 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
                                <span class="ml-4 text-sm font-medium text-gray-700 capitalize">
                                    @php
                                        $routeName = request()->route()->getName();
                                        $cleanRoute = str_replace(['admin.', 'settings.'], '', $routeName);
                                        $cleanRoute = str_replace(['.', '-', '_'], ' ', $cleanRoute);
                                        echo $cleanRoute;
                                    @endphp
                                </span>
                            </div>
                        </li>
                        @endif
                    </ol>
                </nav>

                <!-- Profile dropdown -->
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <!-- Language selector -->
                    <livewire:language-selector />
                    
                    <!-- User menu avec informations multi-tenant -->
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" 
                                @click="open = !open" 
                                class="flex items-center gap-x-4 text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 transition-colors duration-200 group" 
                                id="user-menu-button" 
                                :aria-expanded="open" 
                                aria-haspopup="true">
                            <span class="sr-only">Ouvrir le menu utilisateur</span>
                            <div class="hidden sm:flex sm:flex-col sm:items-end">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                                    @if(auth()->user()->is_global_admin)
                                        <span class="inline-flex items-center rounded-full bg-gradient-to-r from-purple-100 to-purple-200 px-2 py-0.5 text-xs font-medium text-purple-800 ring-1 ring-purple-300">
                                            <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 5.25a8.25 8.25 0 01-16.5 0 8.25 8.25 0 0116.5 0z" />
                                            </svg>
                                            Admin Global
                                        </span>
                                    @endif
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    @if(auth()->user()->company)
                                        <div class="h-1.5 w-1.5 rounded-full bg-green-500"></div>
                                        <span class="text-xs text-gray-600">{{ Str::limit(auth()->user()->company->name, 20) }}</span>
                                    @else
                                        <div class="h-1.5 w-1.5 rounded-full bg-red-500"></div>
                                        <span class="text-xs text-red-600">Aucune entreprise</span>
                                    @endif
                                    @if(auth()->user()->store)
                                        <span class="text-xs text-gray-400">• {{ auth()->user()->store->name }}</span>
                                    @endif
                                </div>
                            </div>
                            <div class="relative h-10 w-10 rounded-full bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-sm shadow-lg group-hover:shadow-xl transition-all duration-200">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                @if(auth()->user()->company && auth()->user()->company->is_active)
                                    <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-green-400 border-2 border-white flex items-center justify-center">
                                        <div class="h-2 w-2 rounded-full bg-green-600"></div>
                                    </div>
                                @else
                                    <div class="absolute -top-1 -right-1 h-4 w-4 rounded-full bg-red-400 border-2 border-white flex items-center justify-center">
                                        <div class="h-2 w-2 rounded-full bg-red-600"></div>
                                    </div>
                                @endif
                            </div>
                        </button>

                        <!-- Dropdown menu amélioré -->
                        <div x-show="open" 
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95 translate-y-[-10px]"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="transform opacity-100 scale-100 translate-y-0"
                             x-transition:leave-end="transform opacity-0 scale-95 translate-y-[-10px]"
                             class="absolute right-0 z-50 mt-3 w-80 origin-top-right rounded-2xl bg-white shadow-2xl ring-1 ring-black/5 border border-gray-100"
                             x-cloak
                             style="display: none;">
                            
                            <!-- Informations utilisateur et entreprise -->
                            <div class="p-4 border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white rounded-t-2xl">
                                <div class="flex items-center gap-3">
                                    <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center text-white font-bold text-lg shadow-lg">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <div class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</div>
                                        <div class="text-xs text-gray-600">{{ Auth::user()->email }}</div>
                                        @if(auth()->user()->company)
                                            <div class="flex items-center gap-2 mt-1">
                                                <div class="h-2 w-2 rounded-full {{ auth()->user()->company->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>
                                                <span class="text-xs font-medium {{ auth()->user()->company->is_active ? 'text-green-700' : 'text-red-700' }}">
                                                    {{ auth()->user()->company->name }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="p-2">
                                <a href="{{ route('profile.index') }}" 
                                   class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-xl transition-colors duration-200 group">
                                    <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center group-hover:bg-blue-200 transition-colors duration-200">
                                        <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.982 18.725A7.488 7.488 0 0012 15.75a7.488 7.488 0 00-5.982 2.975m11.963 0a9 9 0 10-11.963 0m11.963 0A8.966 8.966 0 0112 21a8.966 8.966 0 01-5.982-2.275M15 9.75a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                    </div>
                                    <span>Mon Profil</span>
                                </a>
                                
                                @if(auth()->user()->is_global_admin)
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="flex items-center gap-3 px-3 py-2 text-sm text-gray-700 hover:bg-purple-50 rounded-xl transition-colors duration-200 group">
                                        <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center group-hover:bg-purple-200 transition-colors duration-200">
                                            <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 5.25a8.25 8.25 0 01-16.5 0 8.25 8.25 0 0116.5 0z" />
                                            </svg>
                                        </div>
                                        <span>Administration Globale</span>
                                    </a>
                                @endif
                            </div>
                            
                            <div class="border-t border-gray-100 p-2">
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" 
                                            class="flex items-center gap-3 w-full px-3 py-2 text-sm text-red-700 hover:bg-red-50 rounded-xl transition-colors duration-200 group">
                                        <div class="h-8 w-8 rounded-lg bg-red-100 flex items-center justify-center group-hover:bg-red-200 transition-colors duration-200">
                                            <svg class="h-4 w-4 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                                            </svg>
                                        </div>
                                        <span>Se déconnecter</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Main content -->
            <main class="py-6 px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    @yield('content')
                </div>
            </main>
        </div>
    </div>

    <!-- Notifications globales -->
    <livewire:notifications />

    @vite('resources/js/app.js')
</body>
</html>