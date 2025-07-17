<div class="min-h-screen bg-gray-50">
    {{-- Header avec avatar et informations de base --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between py-6">
                <div class="flex items-center space-x-6">
                    {{-- Avatar Section --}}
                    <div class="relative">
                        <div class="h-20 w-20 rounded-full overflow-hidden bg-gray-100 ring-4 ring-white shadow-lg">
                            @if($current_avatar)
                                <img src="{{ Storage::url($current_avatar) }}" alt="Avatar" class="h-full w-full object-cover">
                            @else
                                <div class="h-full w-full flex items-center justify-center bg-gradient-to-br from-blue-500 to-purple-600">
                                    <span class="text-2xl font-bold text-white">{{ substr($name, 0, 1) }}</span>
                                </div>
                            @endif
                        </div>
                        
                        {{-- Change Avatar Button --}}
                        <button type="button" 
                                x-data="{ open: false }"
                                @click="open = true; $refs.avatarInput.click()"
                                class="absolute bottom-0 right-0 h-7 w-7 rounded-full bg-blue-600 flex items-center justify-center text-white shadow-lg hover:bg-blue-700 transition-colors">
                            <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>
                        
                        {{-- Hidden file input --}}
                        <input type="file" 
                               wire:model="avatar" 
                               x-ref="avatarInput"
                               accept="image/*" 
                               class="hidden">
                    </div>

                    {{-- User Info --}}
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">{{ $name }}</h1>
                        <p class="text-sm text-gray-500">{{ $email }}</p>
                        @if($position)
                            <p class="text-sm text-blue-600 font-medium">{{ $position }}</p>
                        @endif
                    </div>
                </div>

                {{-- Quick Actions --}}
                <div class="flex items-center space-x-3">
                    <button wire:click="togglePasswordFields"
                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                        </svg>
                        {{ $showPasswordFields ? 'Masquer' : 'Changer mot de passe' }}
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Navigation Tabs --}}
    <div class="bg-white border-b border-gray-200">
        <div class="px-4 sm:px-6 lg:px-8">
            <nav class="flex space-x-8" aria-label="Tabs">
                @php
                    $tabs = [
                        'profile' => ['label' => 'Informations personnelles', 'icon' => 'user'],
                        'security' => ['label' => 'Sécurité', 'icon' => 'shield-check'],
                        'sessions' => ['label' => 'Sessions actives', 'icon' => 'device-mobile']
                    ];
                @endphp

                @foreach($tabs as $key => $tab)
                    <button wire:click="switchTab('{{ $key }}')"
                            class="py-4 px-1 border-b-2 font-medium text-sm transition-colors {{ $activeTab === $key 
                                ? 'border-blue-500 text-blue-600' 
                                : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }}">
                        {{ $tab['label'] }}
                    </button>
                @endforeach
            </nav>
        </div>
    </div>

    {{-- Content Area --}}
    <div class="px-4 sm:px-6 lg:px-8 py-8">
        <div class="max-w-4xl mx-auto">
            @if($activeTab === 'profile')
                {{-- Profile Tab --}}
                <div class="space-y-8">
                    {{-- Personal Information Card --}}
                    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                            <div class="flex items-center justify-between">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-900">{{ __('Informations personnelles') }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">{{ __('Gérez vos informations de profil') }}</p>
                                </div>
                                <div class="flex items-center space-x-2">
                                    @if($isUpdatingProfile)
                                        <div class="flex items-center text-blue-600">
                                            <svg class="animate-spin h-4 w-4 mr-2" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('Mise à jour...') }}
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <form wire:submit.prevent="updateProfile" class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Name Field --}}
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-medium text-gray-700">
                                        {{ __('Nom complet') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               wire:model.blur="name" 
                                               id="name" 
                                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('name') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                               placeholder="Votre nom complet">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('name')
                                        <p class="text-sm text-red-600 flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Email Field --}}
                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-medium text-gray-700">
                                        {{ __('Adresse e-mail') }} <span class="text-red-500">*</span>
                                    </label>
                                    <div class="relative">
                                        <input type="email" 
                                               wire:model.blur="email" 
                                               id="email" 
                                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('email') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                               placeholder="votre@email.com">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    @error('email')
                                        <p class="text-sm text-red-600 flex items-center">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                            </svg>
                                            {{ $message }}
                                        </p>
                                    @enderror
                                </div>

                                {{-- Phone Field --}}
                                <div class="space-y-2">
                                    <label for="phone" class="block text-sm font-medium text-gray-700">
                                        {{ __('Téléphone') }}
                                    </label>
                                    <div class="relative">
                                        <input type="tel" 
                                               wire:model.blur="phone" 
                                               id="phone" 
                                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                               placeholder="+241 01 02 03 04 05">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>

                                {{-- Position Field --}}
                                <div class="space-y-2">
                                    <label for="position" class="block text-sm font-medium text-gray-700">
                                        {{ __('Poste') }}
                                    </label>
                                    <div class="relative">
                                        <input type="text" 
                                               wire:model.blur="position" 
                                               id="position" 
                                               class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                               placeholder="Gestionnaire, Caissier, etc.">
                                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2-2v2m8 0V6a2 2 0 012 2v6a2 2 0 01-2 2H6a2 2 0 01-2-2V8a2 2 0 012-2h8zM12 9h.01"></path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" 
                                        :disabled="$wire.isUpdatingProfile"
                                        class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                    @if($isUpdatingProfile)
                                        <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                        </svg>
                                        {{ __('Mise à jour...') }}
                                    @else
                                        <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                        </svg>
                                        {{ __('app.general.save') }}
                                    @endif
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Password Update Card (Conditional) --}}
                    @if($showPasswordFields)
                        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden" x-data x-show="true" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 transform scale-95" x-transition:enter-end="opacity-100 transform scale-100">
                            <div class="px-6 py-4 border-b border-gray-200 bg-red-50">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center">
                                        <div class="flex-shrink-0">
                                            <svg class="h-6 w-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                            </svg>
                                        </div>
                                        <div class="ml-3">
                                            <h3 class="text-lg font-semibold text-red-900">{{ __('Changer le mot de passe') }}</h3>
                                            <p class="text-sm text-red-700 mt-1">{{ __('Assurez-vous d\'utiliser un mot de passe fort et unique') }}</p>
                                        </div>
                                    </div>
                                    <button wire:click="togglePasswordFields"
                                            class="text-red-400 hover:text-red-600 transition-colors">
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>

                            <form wire:submit.prevent="updatePassword" class="p-6">
                                <div class="space-y-6">
                                    {{-- Current Password --}}
                                    <div class="space-y-2">
                                        <label for="current_password" class="block text-sm font-medium text-gray-700">
                                            {{ __('Mot de passe actuel') }} <span class="text-red-500">*</span>
                                        </label>
                                        <div class="relative">
                                            <input type="password" 
                                                   wire:model.blur="current_password" 
                                                   id="current_password" 
                                                   class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('current_password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                                   placeholder="Votre mot de passe actuel">
                                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                                                </svg>
                                            </div>
                                        </div>
                                        @error('current_password')
                                            <p class="text-sm text-red-600 flex items-center">
                                                <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                </svg>
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                        {{-- New Password --}}
                                        <div class="space-y-2">
                                            <label for="password" class="block text-sm font-medium text-gray-700">
                                                {{ __('Nouveau mot de passe') }} <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" 
                                                       wire:model.blur="password" 
                                                       id="password" 
                                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors @error('password') border-red-300 focus:border-red-500 focus:ring-red-500 @enderror"
                                                       placeholder="Nouveau mot de passe">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                            @error('password')
                                                <p class="text-sm text-red-600 flex items-center">
                                                    <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                                    </svg>
                                                    {{ $message }}
                                                </p>
                                            @enderror
                                        </div>

                                        {{-- Confirm Password --}}
                                        <div class="space-y-2">
                                            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">
                                                {{ __('Confirmer le mot de passe') }} <span class="text-red-500">*</span>
                                            </label>
                                            <div class="relative">
                                                <input type="password" 
                                                       wire:model.blur="password_confirmation" 
                                                       id="password_confirmation" 
                                                       class="block w-full rounded-lg border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 transition-colors"
                                                       placeholder="Confirmer le mot de passe">
                                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                                    <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="mt-8 flex justify-end space-x-3">
                                    <button type="button" 
                                            wire:click="togglePasswordFields"
                                            class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 transition-colors">
                                        {{ __('app.general.cancel') }}
                                    </button>
                                    <button type="submit" 
                                            :disabled="$wire.isUpdatingPassword"
                                            class="inline-flex items-center px-6 py-3 border border-transparent rounded-lg text-sm font-medium text-white bg-red-600 hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 disabled:opacity-50 disabled:cursor-not-allowed transition-colors">
                                        @if($isUpdatingPassword)
                                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                            {{ __('Mise à jour...') }}
                                        @else
                                            <svg class="h-4 w-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z"></path>
                                            </svg>
                                            {{ __('Changer le mot de passe') }}
                                        @endif
                                    </button>
                                </div>
                            </form>
                        </div>
                    @endif
                </div>

            @elseif($activeTab === 'security')
                {{-- Security Tab --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Paramètres de sécurité') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Gérez la sécurité de votre compte') }}</p>
                    </div>

                    <div class="p-6 space-y-6">
                        {{-- Two Factor Authentication --}}
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ __('Authentification à deux facteurs') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Ajoutez une couche de sécurité supplémentaire') }}</p>
                            </div>
                            <div class="flex items-center">
                                @if($twoFactorEnabled)
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                        </svg>
                                        Activé
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-red-100 text-red-800">
                                        <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                        </svg>
                                        Désactivé
                                    </span>
                                @endif
                                <button class="ml-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    {{ $twoFactorEnabled ? 'Désactiver' : 'Activer' }}
                                </button>
                            </div>
                        </div>

                        {{-- Password Strength --}}
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ __('Force du mot de passe') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Dernière modification il y a 30 jours') }}</p>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                    Moyenne
                                </span>
                                <button wire:click="togglePasswordFields" class="ml-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Changer
                                </button>
                            </div>
                        </div>

                        {{-- Login Notifications --}}
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h4 class="text-sm font-medium text-gray-900">{{ __('Notifications de connexion') }}</h4>
                                <p class="text-sm text-gray-600">{{ __('Recevez des alertes pour les nouvelles connexions') }}</p>
                            </div>
                            <div class="flex items-center">
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                    <svg class="h-3 w-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    Activé
                                </span>
                                <button class="ml-3 text-blue-600 hover:text-blue-700 text-sm font-medium">
                                    Configurer
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

            @elseif($activeTab === 'sessions')
                {{-- Sessions Tab --}}
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                    <div class="px-6 py-4 border-b border-gray-200 bg-gray-50">
                        <h3 class="text-lg font-semibold text-gray-900">{{ __('Sessions actives') }}</h3>
                        <p class="text-sm text-gray-600 mt-1">{{ __('Gérez vos connexions actives sur différents appareils') }}</p>
                    </div>

                    <div class="divide-y divide-gray-200">
                        @foreach($loginSessions as $index => $session)
                            <div class="p-6 flex items-center justify-between">
                                <div class="flex items-center space-x-4">
                                    <div class="flex-shrink-0">
                                        <div class="h-10 w-10 rounded-lg {{ $session['current'] ? 'bg-green-100' : 'bg-gray-100' }} flex items-center justify-center">
                                            @if(str_contains($session['device'], 'iPhone') || str_contains($session['device'], 'Mobile'))
                                                <svg class="h-6 w-6 {{ $session['current'] ? 'text-green-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                                </svg>
                                            @else
                                                <svg class="h-6 w-6 {{ $session['current'] ? 'text-green-600' : 'text-gray-600' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                                </svg>
                                            @endif
                                        </div>
                                    </div>
                                    <div>
                                        <div class="flex items-center">
                                            <h4 class="text-sm font-medium text-gray-900">{{ $session['device'] }}</h4>
                                            @if($session['current'])
                                                <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-green-100 text-green-800">
                                                    Session actuelle
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-sm text-gray-600">{{ $session['location'] }} • IP: {{ $session['ip'] }}</p>
                                        <p class="text-xs text-gray-500">{{ $session['last_active'] }}</p>
                                    </div>
                                </div>
                                <div>
                                    @if(!$session['current'])
                                        <button wire:click="terminateSession({{ $index }})"
                                                class="inline-flex items-center px-3 py-2 border border-red-300 rounded-md text-sm font-medium text-red-700 bg-white hover:bg-red-50 transition-colors">
                                            <svg class="h-4 w-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                            Terminer
                                        </button>
                                    @else
                                        <span class="inline-flex items-center px-3 py-2 border border-gray-300 rounded-md text-sm font-medium text-gray-500 bg-gray-50">
                                            <svg class="h-4 w-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            Active
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>