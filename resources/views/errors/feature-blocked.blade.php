@extends('components.layouts.app')

@section('title', 'Fonctionnalité Indisponible')

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8">
        <div class="text-center">
            <!-- Icône de verrouillage -->
            <div class="mx-auto h-24 w-24 text-red-500 mb-6">
                <svg class="w-full h-full" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                </svg>
            </div>
            
            <h2 class="text-3xl font-extrabold text-gray-900 mb-4">
                Fonctionnalité Temporairement Indisponible
            </h2>
            
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">
                            Accès Restreint
                        </h3>
                        <div class="mt-2 text-sm text-red-700">
                            {{ $message ?? 'Cette fonctionnalité est actuellement indisponible pour votre entreprise.' }}
                        </div>
                        @if(isset($feature))
                            <div class="mt-2 text-xs text-red-600">
                                Fonctionnalité: {{ $feature }}
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            
            <div class="space-y-4">
                <p class="text-gray-600">
                    Cette restriction a été mise en place par nos administrateurs. 
                    Si vous pensez qu'il s'agit d'une erreur, veuillez contacter le support.
                </p>
                
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ route('dashboard') }}" 
                       class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                        </svg>
                        Retour au Tableau de Bord
                    </a>
                    
                    <a href="mailto:support@wondostock.com" 
                       class="inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                        <svg class="mr-2 -ml-1 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                        </svg>
                        Contacter le Support
                    </a>
                </div>
            </div>
            
            <!-- Informations supplémentaires -->
            <div class="mt-8 text-xs text-gray-500">
                <p>Code d'erreur: FEATURE_BLOCKED_403</p>
                <p>Horodatage: {{ now()->format('d/m/Y H:i:s') }}</p>
            </div>
        </div>
    </div>
</div>
@endsection