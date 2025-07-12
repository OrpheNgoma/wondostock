<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Global - WondoStock' }}</title>
    @vite('resources/css/app.css')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        .font-inter { font-family: 'Inter', system-ui, -apple-system, sans-serif; }
        .admin-scroll { 
            scrollbar-width: thin; 
            scrollbar-color: #d1d5db #f9fafb; 
        }
        .admin-scroll::-webkit-scrollbar { 
            width: 6px; 
        }
        .admin-scroll::-webkit-scrollbar-track { 
            background: #f9fafb; 
            border-radius: 3px;
        }
        .admin-scroll::-webkit-scrollbar-thumb { 
            background: #d1d5db; 
            border-radius: 3px; 
        }
        .admin-scroll::-webkit-scrollbar-thumb:hover { 
            background: #9ca3af; 
        }
        .admin-glass {
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
        <div class="hidden lg:fixed lg:inset-y-0 lg:z-50 lg:flex lg:w-80 lg:flex-col">
<div class="flex grow flex-col gap-y-5 overflow-y-auto bg-white px-6 pb-4 shadow-sm border-r border-gray-200 admin-scroll">
                <!-- Admin Logo -->
<div class="flex h-16 shrink-0 items-center border-b border-gray-100">
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center space-x-3 group">
<div class="flex h-12 w-12 items-center justify-center rounded-xl bg-purple-600 shadow-sm group-hover:shadow-md group-hover:bg-purple-700 transition-all duration-200">
                            <svg class="h-7 w-7 text-white" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.623 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
<span class="text-xl font-semibold text-gray-900 group-hover:text-purple-700 transition-colors duration-200">Admin Global</span>
                            <span class="text-xs text-purple-600 font-medium">WondoStock</span>
                        </div>
                    </a>
                </div>

                <!-- Admin Status Badge -->
                <div class="mx-2">
<div class="flex items-center gap-x-3 rounded-xl bg-purple-50 px-4 py-3 border border-purple-200">
                        <div class="flex h-8 w-8 items-center justify-center rounded-lg bg-purple-600">
                            <svg class="h-4 w-4 text-white" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 1l3 3h4a2 2 0 012 2v10a2 2 0 01-2 2H3a2 2 0 01-2-2V6a2 2 0 012-2h4l3-3z" clip-rule="evenodd"/>
                            </svg>
                        </div>
                        <div class="flex flex-col">
<span class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                            <span class="text-xs text-purple-600">Administrateur Global</span>
                        </div>
                    </div>
                </div>

                <!-- Admin Navigation -->
                <nav class="flex flex-1 flex-col">
                    <ul role="list" class="flex flex-1 flex-col gap-y-7">
                        <!-- Admin Global Section -->
                        <li>
<div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Administration Système</div>
                            <ul role="list" class="space-y-2">
                                <li>
                                    <a href="{{ route('admin.dashboard') }}" 
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.dashboard') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-600' : 'text-gray-700 hover:text-purple-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l-1-3m1 3l-1-3m-16.5 0l1 3m-1-3l1-3"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>Dashboard Global</span>
<span class="text-xs text-purple-600 group-hover:text-purple-500">Vue d'ensemble</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.companies.index') }}" 
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.companies.*') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-600' : 'text-gray-700 hover:text-purple-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>Gestion Entreprises</span>
<span class="text-xs text-purple-600 group-hover:text-purple-500">Clients multi-tenant</span>
                                        </div>
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.system-stats') }}" 
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.system-stats') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-600' : 'text-gray-700 hover:text-purple-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>Statistiques Système</span>
<span class="text-xs text-purple-600 group-hover:text-purple-500">Performance & Monitoring</span>
                                        </div>
                                    </a>
                                </li>
                            </ul>
                        </li>

                        <!-- Quick Actions -->
                        <li>
<div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Actions Rapides</div>
                            <ul role="list" class="space-y-2">
                                <li>
<button class="group flex w-full gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-purple-700 hover:bg-gray-50 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>Nouvelle Entreprise</span>
<span class="text-xs text-purple-600 group-hover:text-purple-500">Ajouter un client</span>
                                        </div>
                                    </button>
                                </li>
                                <li>
<button class="group flex w-full gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-purple-700 hover:bg-gray-50 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m.75 12l3 3m0 0l3-3m-3 3v-6m-1.5-9H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>Exporter Données</span>
<span class="text-xs text-purple-600 group-hover:text-purple-500">Rapports système</span>
                                        </div>
                                    </button>
                                </li>
                            </ul>
                        </li>

                        <!-- Access Regular App -->
                        <li class="mt-auto">
<div class="text-xs font-medium leading-6 text-gray-500 uppercase tracking-wide mb-3">Navigation</div>
                            <ul role="list" class="space-y-2">
                                <li>
                                    <a href="{{ route('dashboard') }}" 
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 text-gray-700 hover:text-emerald-700 hover:bg-emerald-50 border border-transparent hover:border-emerald-200 transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 15L3 9m0 0l6-6m-6 6h18"/>
                                        </svg>
                                        <div class="flex flex-col">
                                            <span>Retour Application</span>
<span class="text-xs text-emerald-600 group-hover:text-emerald-500">Interface utilisateur</span>
                                        </div>
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
                    <a href="{{ route('admin.dashboard') }}" wire:navigate class="flex items-center space-x-3 group">
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
                                    <a href="{{ route('admin.companies.index') }}" 
                                       @click="sidebarOpen = false"
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.companies.*') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-600' : 'text-gray-700 hover:text-purple-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18M6.75 9.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-.75 3h.75"/>
                                        </svg>
                                        Entreprises
                                    </a>
                                </li>
                                <li>
                                    <a href="{{ route('admin.system-stats') }}" 
                                       @click="sidebarOpen = false"
class="group flex gap-x-3 rounded-lg p-3 text-sm font-medium leading-6 {{ request()->routeIs('admin.system-stats') ? 'bg-purple-50 text-purple-700 border-r-2 border-purple-600' : 'text-gray-700 hover:text-purple-700 hover:bg-gray-50' }} transition-all duration-200">
                                        <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 107.5 7.5h-7.5V6z"/>
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0013.5 3v7.5z"/>
                                        </svg>
                                        Statistiques Système
                                    </a>
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
        <div class="lg:pl-80">
            <!-- Admin Top navigation -->
<div class="sticky top-0 z-40 flex h-16 shrink-0 items-center gap-x-4 border-b border-gray-200 admin-glass px-4 shadow-sm sm:gap-x-6 sm:px-6 lg:px-8">
                <!-- Mobile menu button -->
                <button type="button" 
                        @click="sidebarOpen = true" 
class="-m-2.5 p-2.5 text-gray-400 lg:hidden hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-inset focus:ring-purple-500 transition-colors duration-200">
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
class="flex items-center gap-x-4 text-sm font-semibold leading-6 text-gray-900 hover:text-purple-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500 focus:ring-offset-white transition-colors duration-200" 
                                :aria-expanded="open" 
                                aria-haspopup="true">
                            <span class="sr-only">Ouvrir le menu admin</span>
                            <div class="hidden sm:flex sm:flex-col sm:items-end">
<span class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</span>
                                <span class="text-xs text-purple-600">Administrateur Global</span>
                            </div>
<div class="h-8 w-8 rounded-full bg-purple-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-purple-200">
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