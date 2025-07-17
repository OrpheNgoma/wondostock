<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ __('Invitation expirée') }} - WondoStock</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gray-50">
    <div class="min-h-screen flex flex-col justify-center py-12 sm:px-6 lg:px-8">
        <div class="sm:mx-auto sm:w-full sm:max-w-md">
            <div class="flex justify-center">
                <div class="w-12 h-12 bg-red-600 rounded-lg flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            <h2 class="mt-6 text-center text-3xl font-bold text-gray-900">
                {{ __('Invitation expirée') }}
            </h2>
            <p class="mt-2 text-center text-sm text-gray-600">
                {{ __('Cette invitation n\'est plus valide') }}
            </p>
        </div>

        <div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
            <div class="bg-white py-8 px-4 shadow sm:rounded-lg sm:px-10">
                {{-- Message d'erreur --}}
                <div class="mb-6 p-4 bg-red-50 rounded-lg border border-red-200">
                    <div class="flex items-start">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-red-600 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800">
                                {{ __('Invitation expirée') }}
                            </h3>
                            <div class="mt-1 text-sm text-red-700">
                                <p>{{ __('Cette invitation pour rejoindre') }} <strong>{{ $invitation->company->name }}</strong> {{ __('a expiré le') }} {{ $invitation->expires_at->format('d/m/Y à H:i') }}.</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Informations sur l'invitation --}}
                <div class="mb-6 p-4 bg-gray-50 rounded-lg border border-gray-200">
                    <h4 class="text-sm font-medium text-gray-900 mb-2">{{ __('Détails de l\'invitation') }}</h4>
                    <div class="text-sm text-gray-700 space-y-1">
                        <p><strong>{{ __('Entreprise') }}:</strong> {{ $invitation->company->name }}</p>
                        <p><strong>{{ __('Invité par') }}:</strong> {{ $invitation->inviter->name }}</p>
                        <p><strong>{{ __('Email') }}:</strong> {{ $invitation->email }}</p>
                        @if($invitation->role)
                            <p><strong>{{ __('Rôle proposé') }}:</strong> {{ $invitation->role->name }}</p>
                        @endif
                        @if($invitation->store)
                            <p><strong>{{ __('Magasin assigné') }}:</strong> {{ $invitation->store->name }}</p>
                        @endif
                    </div>
                </div>

                {{-- Actions --}}
                <div class="space-y-4">
                    <div class="text-center">
                        <p class="text-sm text-gray-600 mb-4">
                            {{ __('Pour obtenir une nouvelle invitation, contactez') }} <strong>{{ $invitation->inviter->name }}</strong> {{ __('ou un administrateur de') }} <strong>{{ $invitation->company->name }}</strong>.
                        </p>
                    </div>

                    <div class="flex flex-col space-y-3">
                        <a href="{{ route('login') }}"
                           class="w-full flex justify-center py-2 px-4 border border-transparent rounded-md shadow-sm text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            {{ __('Se connecter') }}
                        </a>
                        
                        <a href="{{ route('register') }}"
                           class="w-full flex justify-center py-2 px-4 border border-gray-300 rounded-md shadow-sm text-sm font-medium text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                            {{ __('Créer un nouveau compte') }}
                        </a>
                    </div>
                </div>

                <div class="mt-6">
                    <div class="relative">
                        <div class="absolute inset-0 flex items-center">
                            <div class="w-full border-t border-gray-300" />
                        </div>
                    </div>
                </div>

                <div class="mt-6 text-center">
                    <p class="text-xs text-gray-500">
                        {{ __('Besoin d\'aide ? Contactez le support WondoStock.') }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</body>
</html>