@extends('layouts.saas')

@section('title', 'Modifier la branche pays - WondoStock')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-slate-50 via-blue-50 to-indigo-50">
    <!-- En-tête moderne avec gradient (taille réduite) -->
    <div class="relative overflow-hidden">
        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 opacity-90"></div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="text-center">
                <div class="flex items-center justify-center mb-4">
                    <div class="h-12 w-12 rounded-2xl bg-white/20 backdrop-blur-sm flex items-center justify-center shadow-lg">
                        <svg class="h-6 w-6 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3s-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
                        </svg>
                    </div>
                </div>
                <h1 class="text-3xl font-bold text-white mb-3">
                    Modifier la branche pays
                </h1>
                <p class="text-lg text-blue-100 max-w-2xl mx-auto">
                    Personnalisez <span class="font-semibold text-white">{{ $store->name }}</span> - {{ $store->country_name }}
                </p>
                <div class="mt-4 flex items-center justify-center gap-2 text-blue-100">
                    <span class="inline-flex items-center rounded-full bg-white/20 px-3 py-1 text-sm font-medium">
                        <div class="h-2 w-2 rounded-full {{ $store->is_active ? 'bg-green-400' : 'bg-red-400' }} mr-2"></div>
                        {{ $store->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 -mt-6 pb-12" x-data="{ activeTab: 'general' }">
        <!-- Navigation par onglets -->
        <div class="bg-white rounded-2xl shadow-xl ring-1 ring-gray-200 overflow-hidden">
            <div class="border-b border-gray-200">
                <nav class="flex space-x-8 px-8 pt-6">
                    <button @click="activeTab = 'general'" 
                            :class="activeTab === 'general' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            Informations générales
                        </div>
                    </button>
                    <button @click="activeTab = 'legal'" 
                            :class="activeTab === 'legal' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6h3.75m-3.75 3h3.75m-3.75 3h3.75M9 6h3.75M9 3h3.75M9 9h3.75m3-6h3.75m-3.75 3h3.75M9 21h10.5c.621 0 1.125-.504 1.125-1.125V2.25c0-.621-.504-1.125-1.125-1.125H9m0 18V3m0 18h-5.25c-.621 0-1.125-.504-1.125-1.125V2.25c0-.621.504-1.125 1.125-1.125H9" />
                            </svg>
                            Informations légales
                        </div>
                    </button>
                    <button @click="activeTab = 'templates'" 
                            :class="activeTab === 'templates' ? 'border-blue-500 text-blue-600' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300'"
                            class="whitespace-nowrap py-2 px-1 border-b-2 font-medium text-sm transition-colors duration-200">
                        <div class="flex items-center gap-2">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M18.75 9.456v5.294M6.75 9.456v5.294c0 .615-.456 1.122-1.037 1.122H4.622c-.924 0-1.622-.759-1.622-1.684V9.456c0-.925.698-1.684 1.622-1.684h1.091c.581 0 1.037.507 1.037 1.122z" />
                            </svg>
                            Templates facture
                        </div>
                    </button>
                </nav>
            </div>

            <!-- Messages d'erreur modernes -->
            @if ($errors->any())
                <div class="mx-8 mt-6 rounded-xl bg-gradient-to-r from-red-50 to-pink-50 p-6 border border-red-200 shadow-sm">
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-lg bg-red-100 flex items-center justify-center flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                        </div>
                        <div class="flex-1">
                            <h3 class="text-lg font-semibold text-red-800 mb-2">
                                Veuillez corriger les erreurs suivantes :
                            </h3>
                            <ul class="space-y-1">
                                @foreach ($errors->all() as $error)
                                    <li class="flex items-center gap-2 text-sm text-red-700">
                                        <div class="h-1.5 w-1.5 rounded-full bg-red-400"></div>
                                        {{ $error }}
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            @endif

            <form action="{{ route('country-branch.update', $store) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <!-- Contenu des onglets -->
                <div class="p-8">
                    <!-- Onglet Informations générales -->
                    <div x-show="activeTab === 'general'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0">
                        <div class="space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-blue-100 to-blue-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Informations générales</h2>
                                    <p class="text-sm text-gray-600">Configurez les informations de base de votre branche</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Nom de la branche -->
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-800">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36m11.14 0H18m0 0h3.64m-1.39 0V9.349m-16.5 11.65V9.35m0 0a3.001 3.001 0 003.75-.615A2.993 2.993 0 009.75 9.75c.896 0 1.7-.393 2.25-1.016a2.993 2.993 0 002.25 1.016c.896 0 1.7-.393 2.25-1.016a3.001 3.001 0 003.75.614m-16.5 0a3.004 3.004 0 01-.621-4.72L4.318 3.44A1.5 1.5 0 015.378 3h13.243a1.5 1.5 0 011.06.44l1.19 1.189a3 3 0 01-.621 4.72m-13.5 8.65h3.75a.75.75 0 00.75-.75V13.5a.75.75 0 00-.75-.75H6.75a.75.75 0 00-.75.75v3.75c0 .415.336.75.75.75z" />
                                            </svg>
                                            Nom de la branche *
                                        </span>
                                    </label>
                                    <input type="text" name="name" id="name" value="{{ old('name', $store->name) }}" required 
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400"
                                           placeholder="Ex: Branche Libreville">
                                    <p class="text-xs text-gray-500">Le nom qui apparaîtra sur tous les documents</p>
                                </div>

                                <!-- Ville -->
                                <div class="space-y-2">
                                    <label for="city" class="block text-sm font-semibold text-gray-800">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1115 0z" />
                                            </svg>
                                            Ville *
                                        </span>
                                    </label>
                                    <input type="text" name="city" id="city" value="{{ old('city', $store->city) }}" required 
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400"
                                           placeholder="Ex: Libreville">
                                </div>

                                <!-- Téléphone -->
                                <div class="space-y-2">
                                    <label for="contact_phone" class="block text-sm font-semibold text-gray-800">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 6.75c0 8.284 6.716 15 15 15h2.25a2.25 2.25 0 002.25-2.25v-1.372c0-.516-.351-.966-.852-1.091l-4.423-1.106c-.44-.11-.902.055-1.173.417l-.97 1.293c-.282.376-.769.542-1.21.38a12.035 12.035 0 01-7.143-7.143c-.162-.441.004-.928.38-1.21l1.293-.97c.363-.271.527-.734.417-1.173L6.963 3.102a1.125 1.125 0 00-1.091-.852H4.5A2.25 2.25 0 002.25 4.5v2.25z" />
                                            </svg>
                                            Téléphone *
                                        </span>
                                    </label>
                                    <input type="tel" name="contact_phone" id="contact_phone" value="{{ old('contact_phone', $store->contact_phone) }}" required 
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400"
                                           placeholder="Ex: +241 11 22 33 44">
                                </div>

                                <!-- Email -->
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-semibold text-gray-800">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                                            </svg>
                                            Email
                                        </span>
                                    </label>
                                    <input type="email" name="email" id="email" value="{{ old('email', $store->email) }}" 
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400"
                                           placeholder="contact@branche.com">
                                </div>
                            </div>

                            <!-- Adresse complète -->
                            <div class="space-y-2">
                                <label for="address" class="block text-sm font-semibold text-gray-800">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 21h19.5m-18-18v18m2.25-18v18m13.5-18v18m2.25-18v18M6.75 6.75h.75m-.75 3h.75m-.75 3h.75m3-6h.75m-.75 3h.75m-.75 3h.75M6.75 21v-3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21M3 3h12m-.75 4.5H21m-3.75 3.75h.75m-3.75 3.75h.75m-3.75 3.75h.75m-3.75 3.75H21m-5.25-18h2.25A2.25 2.25 0 0121 5.25v13.5A2.25 2.25 0 0118.75 21H5.25A2.25 2.25 0 013 18.75V5.25A2.25 2.25 0 015.25 3z" />
                                        </svg>
                                        Adresse complète *
                                    </span>
                                </label>
                                <textarea name="address" id="address" rows="4" required 
                                          class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400 resize-none"
                                          placeholder="Adresse complète de la branche...">{{ old('address', $store->address) }}</textarea>
                                <p class="text-xs text-gray-500">Cette adresse apparaîtra sur tous les documents officiels</p>
                            </div>

                            <!-- Statut -->
                            <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                            <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9.348 14.651a3.75 3.75 0 010-5.303m5.304 0a3.75 3.75 0 010 5.303m-7.425 2.122a6.75 6.75 0 010-9.546m9.546 0a6.75 6.75 0 010 9.546M5.106 18.894c3.808-3.808 9.98-3.808 13.788 0M12 12h.008v.008H12V12z" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-semibold text-gray-900">Statut de la branche</h3>
                                            <p class="text-sm text-gray-600">Contrôlez la disponibilité de cette branche</p>
                                        </div>
                                    </div>
                                    <label class="relative inline-flex items-center cursor-pointer">
                                        <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $store->is_active) ? 'checked' : '' }} class="sr-only peer">
                                        <div class="w-14 h-7 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-blue-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-0.5 after:left-[4px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-6 after:w-6 after:transition-all peer-checked:bg-green-500"></div>
                                        <span class="ml-3 text-sm font-medium text-gray-700">{{ old('is_active', $store->is_active) ? 'Active' : 'Inactive' }}</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Informations légales -->
                    <div x-show="activeTab === 'legal'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                        <div class="space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-purple-100 to-purple-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3-6h3.75m-3.75 3h3.75m-3.75 3h3.75M9 6h3.75M9 3h3.75M9 9h3.75m3-6h3.75m-3.75 3h3.75M9 21h10.5c.621 0 1.125-.504 1.125-1.125V2.25c0-.621-.504-1.125-1.125-1.125H9m0 18V3m0 18h-5.25c-.621 0-1.125-.504-1.125-1.125V2.25c0-.621.504-1.125 1.125-1.125H9" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Informations légales</h2>
                                    <p class="text-sm text-gray-600">Données réglementaires spécifiques au pays</p>
                                </div>
                            </div>

                            <!-- Pays -->
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label for="country_code" class="block text-sm font-semibold text-gray-800">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3s-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
                                            </svg>
                                            Code pays *
                                        </span>
                                    </label>
                                    <select name="country_code" id="country_code" required 
                                            class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400">
                                        <option value="">Sélectionner un pays</option>
                                        <option value="GAB" {{ old('country_code', $store->country_code) == 'GAB' ? 'selected' : '' }}>🇬🇦 Gabon</option>
                                        <option value="CMR" {{ old('country_code', $store->country_code) == 'CMR' ? 'selected' : '' }}>🇨🇲 Cameroun</option>
                                        <option value="CG" {{ old('country_code', $store->country_code) == 'CG' ? 'selected' : '' }}>🇨🇬 Congo Brazzaville</option>
                                        <option value="CD" {{ old('country_code', $store->country_code) == 'CD' ? 'selected' : '' }}>🇨🇩 Congo Kinshasa</option>
                                    </select>
                                </div>

                                <div class="space-y-2">
                                    <label for="country_name" class="block text-sm font-semibold text-gray-800">
                                        <span class="flex items-center gap-2">
                                            <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 008.716-6.747M12 21a9.004 9.004 0 01-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3s-4.5 4.03-4.5 9 2.015 9 4.5 9z" />
                                            </svg>
                                            Nom du pays *
                                        </span>
                                    </label>
                                    <input type="text" name="country_name" id="country_name" value="{{ old('country_name', $store->country_name) }}" required 
                                           class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-blue-600 sm:text-sm transition-all duration-200 hover:ring-gray-400"
                                           placeholder="Ex: Gabon">
                                </div>
                            </div>

                            <!-- Documents légaux -->
                            <div class="bg-gradient-to-r from-amber-50 to-yellow-50 rounded-xl p-6 border border-amber-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                    Documents d'identification légale
                                </h3>
                                
                                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                    <div class="space-y-2">
                                        <label for="nif" class="block text-sm font-semibold text-gray-800">NIF</label>
                                        <input type="text" name="nif" id="nif" value="{{ old('nif', $store->nif) }}" 
                                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm transition-all duration-200"
                                               placeholder="Numéro d'identification fiscale">
                                    </div>

                                    <div class="space-y-2">
                                        <label for="rccm" class="block text-sm font-semibold text-gray-800">RCCM</label>
                                        <input type="text" name="rccm" id="rccm" value="{{ old('rccm', $store->rccm) }}" 
                                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm transition-all duration-200"
                                               placeholder="Registre de commerce">
                                    </div>

                                    <div class="space-y-2">
                                        <label for="postal_box" class="block text-sm font-semibold text-gray-800">Boîte postale</label>
                                        <input type="text" name="postal_box" id="postal_box" value="{{ old('postal_box', $store->postal_box) }}" 
                                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm transition-all duration-200"
                                               placeholder="BP 1234">
                                    </div>

                                    <div class="space-y-2">
                                        <label for="business_permit" class="block text-sm font-semibold text-gray-800">Permis d'exploitation</label>
                                        <input type="text" name="business_permit" id="business_permit" value="{{ old('business_permit', $store->business_permit) }}" 
                                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm transition-all duration-200"
                                               placeholder="Numéro de permis">
                                    </div>

                                    <div class="space-y-2">
                                        <label for="website" class="block text-sm font-semibold text-gray-800">Site web</label>
                                        <input type="url" name="website" id="website" value="{{ old('website', $store->website) }}" 
                                               class="block w-full rounded-lg border-0 py-2.5 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-amber-500 sm:text-sm transition-all duration-200"
                                               placeholder="https://branche.com">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Onglet Templates facture -->
                    <div x-show="activeTab === 'templates'" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 translate-y-1" x-transition:enter-end="opacity-100 translate-y-0" x-cloak>
                        <div class="space-y-8">
                            <div class="flex items-center gap-4 mb-8">
                                <div class="h-12 w-12 rounded-xl bg-gradient-to-br from-indigo-100 to-indigo-200 flex items-center justify-center">
                                    <svg class="h-6 w-6 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M18.75 9.456v5.294M6.75 9.456v5.294c0 .615-.456 1.122-1.037 1.122H4.622c-.924 0-1.622-.759-1.622-1.684V9.456c0-.925.698-1.684 1.622-1.684h1.091c.581 0 1.037.507 1.037 1.122z" />
                                    </svg>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold text-gray-900">Templates de facture</h2>
                                    <p class="text-sm text-gray-600">Personnalisez l'apparence de vos factures</p>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                                <!-- Image d'en-tête -->
                                <div class="bg-gradient-to-br from-blue-50 to-indigo-50 rounded-2xl p-6 border border-blue-200">
                                    <div class="text-center mb-6">
                                        <div class="h-16 w-16 rounded-xl bg-blue-100 flex items-center justify-center mx-auto mb-3">
                                            <svg class="h-8 w-8 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5M16.5 12L12 16.5m0 0L7.5 12m4.5 4.5V3" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">En-tête de facture</h3>
                                        <p class="text-sm text-gray-600">Image qui apparaîtra en haut de vos factures</p>
                                    </div>

                                    @if($store->invoice_header_image)
                                        <div class="mb-4 text-center">
                                            <img src="{{ Storage::url($store->invoice_header_image) }}" alt="En-tête actuelle" class="h-24 mx-auto rounded-lg border-2 border-white shadow-lg">
                                            <div class="mt-2 inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Image actuelle
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-4 text-center">
                                            <div class="h-24 mx-auto rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50">
                                                <span class="text-gray-500 text-sm">Aucune image</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="space-y-2">
                                        <label for="header_image" class="block text-sm font-semibold text-gray-800">Choisir une nouvelle image</label>
                                        <input type="file" name="header_image" id="header_image" accept="image/*" 
                                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none file:me-4 file:py-3 file:px-4 file:rounded-s-lg file:border-0 file:text-sm file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-700 file:disabled:opacity-50 file:disabled:pointer-events-none">
                                        <p class="text-xs text-gray-500 mt-2">PNG, JPG jusqu'à 2MB. Laissez vide pour conserver l'image actuelle.</p>
                                    </div>
                                </div>

                                <!-- Image de pied de page -->
                                <div class="bg-gradient-to-br from-purple-50 to-pink-50 rounded-2xl p-6 border border-purple-200">
                                    <div class="text-center mb-6">
                                        <div class="h-16 w-16 rounded-xl bg-purple-100 flex items-center justify-center mx-auto mb-3">
                                            <svg class="h-8 w-8 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75V16.5m-13.5-9L12 3m0 0l4.5 4.5M12 3v13.5" />
                                            </svg>
                                        </div>
                                        <h3 class="text-lg font-semibold text-gray-900">Pied de page</h3>
                                        <p class="text-sm text-gray-600">Image qui apparaîtra en bas de vos factures</p>
                                    </div>

                                    @if($store->invoice_footer_image)
                                        <div class="mb-4 text-center">
                                            <img src="{{ Storage::url($store->invoice_footer_image) }}" alt="Pied de page actuel" class="h-24 mx-auto rounded-lg border-2 border-white shadow-lg">
                                            <div class="mt-2 inline-flex items-center gap-1 bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded-full">
                                                <svg class="h-3 w-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                                </svg>
                                                Image actuelle
                                            </div>
                                        </div>
                                    @else
                                        <div class="mb-4 text-center">
                                            <div class="h-24 mx-auto rounded-lg border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50">
                                                <span class="text-gray-500 text-sm">Aucune image</span>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="space-y-2">
                                        <label for="footer_image" class="block text-sm font-semibold text-gray-800">Choisir une nouvelle image</label>
                                        <input type="file" name="footer_image" id="footer_image" accept="image/*" 
                                               class="block w-full text-sm text-gray-900 border border-gray-300 rounded-lg cursor-pointer bg-white focus:outline-none file:me-4 file:py-3 file:px-4 file:rounded-s-lg file:border-0 file:text-sm file:font-semibold file:bg-purple-600 file:text-white hover:file:bg-purple-700 file:disabled:opacity-50 file:disabled:pointer-events-none">
                                        <p class="text-xs text-gray-500 mt-2">PNG, JPG jusqu'à 2MB. Laissez vide pour conserver l'image actuelle.</p>
                                    </div>
                                </div>
                            </div>

                            <!-- Aperçu de la facture -->
                            <div class="bg-gradient-to-r from-gray-50 to-gray-100 rounded-2xl p-6 border border-gray-200">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4 flex items-center gap-2">
                                    <svg class="h-5 w-5 text-gray-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    Aperçu du template
                                </h3>
                                <div class="bg-white rounded-lg border-2 border-dashed border-gray-300 p-4 min-h-[200px] flex flex-col">
                                    <div class="flex-shrink-0 h-16 bg-gray-100 rounded mb-4 flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">Zone d'en-tête</span>
                                    </div>
                                    <div class="flex-1 bg-gray-50 rounded mb-4 flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">Contenu de la facture</span>
                                    </div>
                                    <div class="flex-shrink-0 h-12 bg-gray-100 rounded flex items-center justify-center">
                                        <span class="text-gray-500 text-sm">Zone de pied de page</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Boutons d'action -->
                <div class="border-t border-gray-200 bg-gray-50 px-8 py-6 flex items-center justify-between">
                    <a href="{{ route('stores.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-white px-6 py-3 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Retour
                    </a>
                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-blue-600 to-indigo-600 px-8 py-3 text-sm font-semibold text-white shadow-lg hover:from-blue-700 hover:to-indigo-700 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-blue-600 transition-all duration-200 transform hover:scale-105">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Mettre à jour la branche
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection