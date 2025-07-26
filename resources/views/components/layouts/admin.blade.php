<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'WondoStock Admin' }}</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .font-inter { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .sidebar-scroll { 
            scrollbar-width: thin; 
            scrollbar-color: #fca5a5 #fef2f2; 
        }
        .sidebar-scroll::-webkit-scrollbar { 
            width: 6px; 
        }
        .sidebar-scroll::-webkit-scrollbar-track { 
            background: #fef2f2; 
            border-radius: 3px;
        }
        .sidebar-scroll::-webkit-scrollbar-thumb { 
            background: #fca5a5; 
            border-radius: 3px; 
        }
        .sidebar-scroll::-webkit-scrollbar-thumb:hover { 
            background: #f87171; 
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(229, 231, 235, 0.8);
        }
        .admin-gradient {
            background: linear-gradient(135deg, #dc2626 0%, #ea580c 100%);
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
            <div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-lg border-r border-red-200 sidebar-scroll">
                <!-- Logo et informations admin -->
                <div class="flex h-20 shrink-0 items-center border-b border-red-100 px-2">
                    <div class="flex items-center space-x-3 group w-full">
                        <div class="flex h-12 w-12 items-center justify-center rounded-xl admin-gradient shadow-lg group-hover:shadow-xl transition-all duration-300">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 5.25a8.25 8.25 0 01-16.5 0 8.25 8.25 0 0116.5 0z" />
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="text-lg font-bold text-gray-900 group-hover:text-red-700 transition-colors duration-200 truncate">WondoStock</div>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="h-2 w-2 rounded-full bg-red-500 animate-pulse"></div>
                                <span class="text-xs font-medium text-red-600 truncate">Administration Globale</span>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Navigation Admin -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Dashboard -->
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Tableau de Bord</div>
                            <ul role="list" class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.dashboard') ? 'bg-red-50 text-red-700 border-r-2 border-red-600' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A1.125 1.125 0 013 19.875v-6.75zM9.75 8.625c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v11.25c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V8.625zM16.5 4.125c0-.621.504-1.125 1.125-1.125h2.25C20.496 3 21 3.504 21 4.125v15.75c0 .621-.504 1.125-1.125 1.125h-2.25a1.125 1.125 0 01-1.125-1.125V4.125z"/>
                                        </svg>
                                        Vue d'Ensemble
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Gestion -->
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Gestion SaaS</div>
                            <ul role="list" class="space-y-1">
                                <li>
                                    <a href="{{ route('admin.companies.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.companies.*') ? 'bg-red-50 text-red-700 border-r-2 border-red-600' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        Entreprises Clientes
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.plans.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.plans.*') ? 'bg-red-50 text-red-700 border-r-2 border-red-600' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        Plans Tarifaires
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.subscriptions.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.subscriptions.*') ? 'bg-red-50 text-red-700 border-r-2 border-red-600' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-3.75 3h15a2.25 2.25 0 002.25-2.25V6.75A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25v10.5A2.25 2.25 0 004.5 19.5z"/>
                                        </svg>
                                        Abonnements
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.invoices.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.invoices.*') ? 'bg-red-50 text-red-700 border-r-2 border-red-600' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m6.75 18H3.75a1.5 1.5 0 01-1.5-1.5V4.875c0-.621.504-1.125 1.125-1.125H8.25m0 0h8.25A1.5 1.5 0 0118 4.875c0 .621-.504 1.125-1.125 1.125m0 0V9a.75.75 0 01-.75.75h-4.5A.75.75 0 0112 9V4.875z"/>
                                        </svg>
                                        Facturation & Paiements
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.payment-notifications.index') }}" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.payment-notifications.*') ? 'bg-red-50 text-red-700' : 'text-gray-700 hover:text-red-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                                        </svg>
                                        Notifications Paiement
                                    </a>
                                </li>
                                <li>
                                    <a href="#" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-red-700 hover:bg-gray-50 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/>
                                        </svg>
                                        Analytics & Rapports
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Système -->
                        <li>
                            <div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Système</div>
                            <ul role="list" class="space-y-1">
                                <li>
                                    <a href="#" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-red-700 hover:bg-gray-50 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"/>
                                        </svg>
                                        Logs & Monitoring
                                    </a>
                                </li>
                                <li>
                                    <a href="#" 
                                       class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-red-700 hover:bg-gray-50 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        </svg>
                                        Configuration
                                    </a>
                                </li>
                            </ul>
                        </li>
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
<div class="flex w-80 max-w-xs flex-col grow gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-sm border-r border-gray-200 admin-scroll">
                <!-- Mobile Admin Logo -->
<div class="flex h-16 shrink-0 items-center justify-between border-b border-gray-100">
                    <a href="{{ route('admin.dashboard') }}"  class="flex items-center space-x-3 group">
<div class="flex h-10 w-10 items-center justify-center rounded-lg bg-purple-600 shadow-sm">
                            <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
<span class="text-lg font-semibold text-gray-900">Admin Global</span>
                            <span class="text-xs text-purple-600">WondoStock</span>
                        </div>
                    </a>
                    <!-- Close button for mobile -->
                    <button @click="sidebarOpen = false" 
                            type="button"
class="lg:hidden rounded-md p-2 text-gray-400 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-purple-500 focus:ring-offset-2 focus:ring-offset-white transition-colors duration-200">
                        <span class="sr-only">Fermer la sidebar</span>
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                        </svg>
                    </button>
                </div>

                <!-- Mobile Admin Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Admin Global Section Mobile -->
                        <li>
<div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Administration Système</div>
                            <ul role="list" class="space-y-2">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" 
                                       @click="sidebarOpen = false"
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.dashboard') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-600' : 'text-gray-700 hover:text-purple-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5 0l1 3m-1-3l1-3"/>
                                        </svg>
                                        Dashboard Global
                                    </a>
                                </li>
                                <li>
                                    <span class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-400 cursor-not-allowed">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        Entreprises (à venir)
                                    </span>
                                </li>
                                <li>
                                    <span class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-400 cursor-not-allowed">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                                        </svg>
                                        Statistiques Système (à venir)
                                    </span>
                                </li>
                                <li>
                                    <a href="{{ route('dashboard') }}" 
                                       @click="sidebarOpen = false"
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6m-6 6h18"/>
                                        </svg>
                                        Retour Application
                                    </a>
                                </li>
                            </ul>
                        </li>
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
class="-m-2.5 p-2.5 text-gray-400 lg:hidden hover:text-gray-600 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-red-500 transition-colors duration-200">
                    <span class="sr-only">Ouvrir la sidebar admin</span>
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                </button>

                <!-- Separator -->
<div class="h-6 w-px bg-gray-300 lg:hidden"></div>

                <!-- Admin Breadcrumb -->
                <nav class="flex flex-1" aria-label="Breadcrumb Admin">
                    <ol role="list" class="flex items-center space-x-4">
                        <li>
                            <div class="flex items-center">
<div class="flex h-6 w-6 items-center justify-center rounded bg-purple-100">
<svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                                    </svg>
                                </div>
<a href="{{ route('admin.dashboard') }}" class="ml-3 text-sm font-medium text-purple-600 hover:text-purple-700 transition-colors duration-200">
                                    Administration Globale
                                </a>
                            </div>
                        </li>
                        @if(!request()->routeIs('admin.dashboard'))
                        <li>
                            <div class="flex items-center">
<svg class="h-5 w-5 flex-shrink-0 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M7.21 14.77a.75.75 0 01.02-1.06L11.168 10 7.23 6.29a.75.75 0 111.04-1.08l4.5 4.25a.75.75 0 010 1.08l-4.5 4.25a.75.75 0 01-1.06-.02z" clip-rule="evenodd"/>
                                </svg>
<span class="ml-4 text-sm font-medium text-gray-900 capitalize">
                                    @php
                                        $routeName = request()->route()->getName();
                                        $cleanRoute = str_replace('admin.', '', $routeName);
                                        $cleanRoute = str_replace(['.', '-', '_'], ' ', $cleanRoute);
                                        echo $cleanRoute;
                                    @endphp
                                </span>
                            </div>
                        </li>
                        @endif
                    </ol>
                </nav>

                <!-- Admin User Profile -->
                <div class="flex items-center gap-x-4 lg:gap-x-6">
                    <!-- Admin notifications -->
<button type="button" class="relative p-2 text-gray-400 hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-purple-500 transition-colors duration-200">
                        <span class="sr-only">Voir les notifications</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0"/>
                        </svg>
<span class="absolute top-0 right-0 block h-2 w-2 rounded-full bg-purple-500 ring-2 ring-white"></span>
                    </button>

                    <!-- Admin Profile dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button type="button" 
                                @click="open = !open" 
                                class="flex items-center gap-x-4 text-sm font-semibold leading-6 text-gray-900 hover:text-gray-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 transition-colors duration-200 group" 
                                :aria-expanded="open" 
                                aria-haspopup="true">
                            <span class="sr-only">Ouvrir le menu admin</span>
                            <div class="hidden sm:flex sm:flex-col sm:items-end">
                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                                    <span class="inline-flex items-center rounded-full admin-gradient px-2 py-0.5 text-xs font-medium text-white ring-1 ring-red-300">
                                        <svg class="h-3 w-3 mr-1" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3.75 5.25a8.25 8.25 0 01-16.5 0 8.25 8.25 0 0116.5 0z" />
                                        </svg>
                                        Super Admin
                                    </span>
                                </div>
                                <div class="flex items-center gap-2 mt-1">
                                    <div class="h-1.5 w-1.5 rounded-full bg-red-500"></div>
                                    <span class="text-xs text-gray-600">Administration Globale</span>
                                </div>
                            </div>
                            <div class="relative h-10 w-10 rounded-full admin-gradient flex items-center justify-center text-white font-bold text-sm shadow-lg group-hover:shadow-xl transition-all duration-200">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                        </button>

                        <!-- Admin Dropdown menu -->
                        <div x-show="open" 
                             @click.outside="open = false"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
class="absolute right-0 z-50 mt-2.5 w-64 origin-top-right rounded-xl bg-white py-2 shadow-lg ring-1 ring-gray-200 border border-gray-200"
                             x-cloak
                             style="display: none;">
<div class="px-4 py-2 border-b border-gray-100">
<p class="text-sm font-medium text-gray-900">{{ Auth::user()->name }}</p>
                                <p class="text-xs text-purple-600">Administrateur Global</p>
                            </div>
                            <a href="{{ route('profile.index') }}" 
class="block px-4 py-2 text-sm leading-6 text-gray-700 hover:bg-gray-50 hover:text-gray-900 transition-colors duration-200">
                                <div class="flex items-center gap-x-3">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                                    </svg>
                                    Mon Profil Admin
                                </div>
                            </a>
                            <a href="{{ route('dashboard') }}" 
class="block px-4 py-2 text-sm leading-6 text-gray-700 hover:bg-emerald-50 hover:text-emerald-700 transition-colors duration-200">
                                <div class="flex items-center gap-x-3">
                                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6m-6 6h18"/>
                                    </svg>
                                    Interface Utilisateur
                                </div>
                            </a>
                            <hr class="my-2 border-gray-100">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" 
                                        class="block w-full text-left px-4 py-2 text-sm leading-6 text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors duration-200">
                                    <div class="flex items-center gap-x-3">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75"/>
                                        </svg>
                                        Se Déconnecter
                                    </div>
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Admin Main content -->
            <main class="py-8 px-4 sm:px-6 lg:px-8">
                <div class="mx-auto max-w-7xl">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </div>

    @vite('resources/js/app.js')
</body>
</html>