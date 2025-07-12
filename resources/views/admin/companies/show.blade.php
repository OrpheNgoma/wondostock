<x-layouts.app>
<div class="px-4 sm:px-6 lg:px-8 py-6">
    <!-- Header -->
    <div class="mb-8 p-6 rounded-xl bg-gradient-to-r from-blue-600 via-purple-600 to-indigo-700 text-white shadow-xl">
        <div class="md:flex md:items-center md:justify-between">
            <div class="flex-1 min-w-0">
                <div class="flex items-center space-x-4">
                    <div class="h-12 w-12 rounded-xl bg-white/20 backdrop-blur-sm flex items-center justify-center text-white font-bold text-xl">
                        {{ substr($company->name, 0, 1) }}
                    </div>
                    <div>
                        <h2 class="text-3xl font-bold">
                            {{ $company->name }}
                        </h2>
                        <div class="mt-2 flex flex-col sm:flex-row sm:flex-wrap sm:space-x-6 text-blue-100">
                            <div class="flex items-center">
                                <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 4.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                </svg>
                                {{ $company->email }}
                            </div>
                            <div class="flex items-center mt-1 sm:mt-0">
                                <svg class="flex-shrink-0 mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                </svg>
                                Inscrite le {{ $company->created_at->format('d/m/Y') }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="mt-5 flex lg:mt-0 lg:ml-4 space-x-3">
                <form method="POST" action="{{ route('admin.companies.toggle-status', $company) }}" class="inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold {{ $company->is_active ? 'text-red-700 bg-red-100/20 border border-red-300/30 hover:bg-red-200/30' : 'text-green-700 bg-green-100/20 border border-green-300/30 hover:bg-green-200/30' }} backdrop-blur-sm transition-all duration-200">
                        <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $company->is_active ? 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z' : 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z' }}"/>
                        </svg>
                        {{ $company->is_active ? 'Désactiver' : 'Activer' }}
                    </button>
                </form>
                <a href="{{ route('admin.companies.index') }}" class="inline-flex items-center px-4 py-2 rounded-lg text-sm font-semibold text-white bg-white/20 border border-white/30 hover:bg-white/30 backdrop-blur-sm transition-all duration-200">
                    <svg class="mr-2 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 17l-5-5m0 0l5-5m-5 5h12"/>
                    </svg>
                    Retour à la liste
                </a>
            </div>
        </div>
    </div>

    <!-- Status Banner -->
    <div class="mt-6">
        @if($company->is_active)
            <div class="rounded-md bg-green-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-green-800">
                            Cette entreprise est active et peut utiliser la plateforme.
                        </p>
                    </div>
                </div>
            </div>
        @else
            <div class="rounded-md bg-red-50 p-4">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm font-medium text-red-800">
                            Cette entreprise est désactivée et ne peut pas utiliser la plateforme.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>

    <!-- Metrics Grid -->
    <div class="mb-8 grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-4">
        <div class="bg-white overflow-hidden shadow-lg rounded-xl border border-gray-100 hover:shadow-xl transition-all duration-200">
            <div class="p-6">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <div class="p-3 rounded-xl bg-gradient-to-br from-blue-500 to-blue-600 text-white shadow-md">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-600 truncate">Utilisateurs</dt>
                            <dd class="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">{{ $metrics['users_count'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Produits</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $metrics['products_count'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Revenus (30j)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ number_format($metrics['revenue'], 2) }}€</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
            <div class="p-5">
                <div class="flex items-center">
                    <div class="flex-shrink-0">
                        <svg class="h-6 w-6 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 7v10c0 2.21 3.582 4 8 4s8-1.79 8-4V7M4 7c0 2.21 3.582 4 8 4s8-1.79 8-4M4 7c0-2.21 3.582-4 8-4s8 1.79 8 4"/>
                        </svg>
                    </div>
                    <div class="ml-5 w-0 flex-1">
                        <dl>
                            <dt class="text-sm font-medium text-gray-500 truncate">Connexions (30j)</dt>
                            <dd class="text-lg font-medium text-gray-900">{{ $metrics['logins_count'] }}</dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Company Details and Users -->
    <div class="mt-8 grid grid-cols-1 gap-6 lg:grid-cols-2">
        <!-- Company Information -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Informations de l'entreprise</h3>
            </div>
            <div class="px-6 py-4">
                <dl class="space-y-4">
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Nom légal</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->legal_name ?: 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Adresse</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->address ?: 'Non renseignée' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">Téléphone</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->phone_number ?: 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">RCCM</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->rccm ?: 'Non renseigné' }}</dd>
                    </div>
                    <div>
                        <dt class="text-sm font-medium text-gray-500">NIF</dt>
                        <dd class="mt-1 text-sm text-gray-900">{{ $company->nif ?: 'Non renseigné' }}</dd>
                    </div>
                </dl>
            </div>
        </div>

        <!-- Users List -->
        <div class="bg-white shadow rounded-lg">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg leading-6 font-medium text-gray-900">Utilisateurs ({{ $company->users->count() }})</h3>
            </div>
            <div class="px-6 py-4">
                @if($company->users->count() > 0)
                    <div class="space-y-3">
                        @foreach($company->users as $user)
                            <div class="flex items-center justify-between p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <div class="h-8 w-8 rounded-full bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-medium text-sm">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm font-medium text-gray-900">{{ $user->name }}</p>
                                        <p class="text-sm text-gray-500">{{ $user->email }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center">
                                    @if($user->id === $company->owner_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            Propriétaire
                                        </span>
                                    @endif
                                    @if($user->is_global_admin)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800 ml-2">
                                            Admin Global
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <p class="text-gray-500 text-sm">Aucun utilisateur dans cette entreprise.</p>
                @endif
            </div>
        </div>
    </div>

    <!-- Activity Chart -->
    <div class="mt-8 bg-white shadow rounded-lg">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg leading-6 font-medium text-gray-900">Activité récente (30 derniers jours)</h3>
        </div>
        <div class="px-6 py-4">
            <div class="grid grid-cols-1 gap-6 sm:grid-cols-3">
                <div class="text-center">
                    <p class="text-2xl font-semibold text-blue-600">{{ $metrics['logins_count'] }}</p>
                    <p class="text-sm text-gray-500">Connexions</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-semibold text-green-600">{{ $metrics['products_created'] }}</p>
                    <p class="text-sm text-gray-500">Produits créés</p>
                </div>
                <div class="text-center">
                    <p class="text-2xl font-semibold text-purple-600">{{ $metrics['documents_created'] }}</p>
                    <p class="text-sm text-gray-500">Documents créés</p>
                </div>
            </div>
        </div>
    </div>
</div>
</x-layouts.app>