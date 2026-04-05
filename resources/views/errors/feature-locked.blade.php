@extends('errors::minimal')

@section('title', 'Fonctionnalité Indisponible')
@section('code', '403')
@section('message')
    <div class="max-w-md mx-auto text-center">
        <div class="mb-6">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-amber-100">
                <svg class="h-8 w-8 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 10.5V6.75a4.5 4.5 0 10-9 0v3.75m-.75 11.25h10.5a2.25 2.25 0 002.25-2.25v-7.5a2.25 2.25 0 00-2.25-2.25H6.75a2.25 2.25 0 00-2.25 2.25v7.5a2.25 2.25 0 002.25 2.25z" />
                </svg>
            </div>
        </div>

        <h1 class="text-xl font-semibold text-gray-900 mb-4">
            Fonctionnalité Temporairement Indisponible
        </h1>

        <div class="text-gray-600 mb-6 space-y-3">
            <p>
                <strong>{{ $feature_description }}</strong>
            </p>
            
            <p class="text-sm">
                Cette fonctionnalité est actuellement verrouillée pour votre entreprise 
                <strong>{{ $company->name }}</strong>.
            </p>

            @if($lock_info && $lock_info['reason'])
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-4 text-left">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-amber-400" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-amber-800">
                                Motif du verrouillage
                            </h3>
                            <div class="mt-1 text-sm text-amber-700">
                                {{ $lock_info['reason'] }}
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            @if($lock_info && $lock_info['expires_at'])
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 text-sm">
                    <strong>Déverrouillage automatique :</strong>
                    {{ $lock_info['expires_at']->format('d/m/Y à H:i') }}
                </div>
            @endif
        </div>

        <div class="space-y-3">
            <a href="{{ route('dashboard') }}" 
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md text-white bg-gray-900 hover:bg-gray-800 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-gray-500 transition-colors">
                <svg class="mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                </svg>
                Retour au tableau de bord
            </a>

            <div class="text-xs text-gray-500">
                Pour toute question, contactez l'assistance technique.
            </div>
        </div>
    </div>
@endsection

@push('styles')
<style>
    body {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    }
</style>
@endpush