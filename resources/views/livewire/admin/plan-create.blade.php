<div>
    <div class="flex justify-between items-center mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Nouveau Plan</h1>
            <p class="text-gray-600">Créer un nouveau plan d'abonnement</p>
        </div>
        <a href="{{ route('admin.plans.index') }}" 
           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg inline-flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Retour
        </a>
    </div>

    @if (session()->has('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
            {{ session('success') }}
        </div>
    @endif

    @if (session()->has('error'))
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
            {{ session('error') }}
        </div>
    @endif

    <div class="bg-white rounded-lg shadow-sm">
        <form wire:submit.prevent="save" class="p-6">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Nom du plan -->
                <div>
                    <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                        Nom du plan *
                    </label>
                    <input type="text" 
                           id="name"
                           wire:model.live="name" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Ex: Plan Essentiel">
                    @error('name') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Slug -->
                <div>
                    <label for="slug" class="block text-sm font-medium text-gray-700 mb-2">
                        Slug *
                    </label>
                    <input type="text" 
                           id="slug"
                           wire:model="slug" 
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="plan-essentiel">
                    @error('slug') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Prix -->
                <div>
                    <label for="price" class="block text-sm font-medium text-gray-700 mb-2">
                        Prix (€) *
                    </label>
                    <input type="number" 
                           id="price"
                           wire:model="price" 
                           step="0.01"
                           min="0"
                           class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="0.00">
                    @error('price') 
                        <span class="text-red-500 text-sm">{{ $message }}</span> 
                    @enderror
                </div>

                <!-- Limite d'utilisateurs -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        Limite d'utilisateurs
                    </label>
                    <div class="space-y-3">
                        <label class="flex items-center">
                            <input type="checkbox" 
                                   wire:model.live="unlimited_users"
                                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-300 focus:ring focus:ring-blue-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-gray-600">Utilisateurs illimités</span>
                        </label>
                        
                        @if (!$unlimited_users)
                            <input type="number" 
                                   wire:model="user_limit" 
                                   min="1"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="Nombre maximum d'utilisateurs">
                            @error('user_limit') 
                                <span class="text-red-500 text-sm">{{ $message }}</span> 
                            @enderror
                        @endif
                    </div>
                </div>
            </div>

            <!-- Description -->
            <div class="mt-6">
                <label for="description" class="block text-sm font-medium text-gray-700 mb-2">
                    Description
                </label>
                <textarea id="description"
                          wire:model="description" 
                          rows="3"
                          class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                          placeholder="Description du plan..."></textarea>
                @error('description') 
                    <span class="text-red-500 text-sm">{{ $message }}</span> 
                @enderror
            </div>

            <!-- Fonctionnalités -->
            <div class="mt-6">
                <label class="block text-sm font-medium text-gray-700 mb-2">
                    Fonctionnalités
                </label>
                
                <!-- Ajouter une fonctionnalité -->
                <div class="flex gap-2 mb-4">
                    <input type="text" 
                           wire:model="newFeature"
                           wire:keydown.enter.prevent="addFeature"
                           class="flex-1 px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                           placeholder="Nouvelle fonctionnalité...">
                    <button type="button" 
                            wire:click="addFeature"
                            class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md">
                        Ajouter
                    </button>
                </div>

                <!-- Liste des fonctionnalités -->
                @if (!empty($features))
                    <div class="space-y-2">
                        @foreach ($features as $index => $feature)
                            <div class="flex items-center justify-between bg-gray-50 px-3 py-2 rounded-md">
                                <span class="text-sm">{{ $feature }}</span>
                                <button type="button" 
                                        wire:click="removeFeature({{ $index }})"
                                        class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Boutons d'action -->
            <div class="flex justify-end gap-3 mt-8 pt-6 border-t">
                <a href="{{ route('admin.plans.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-md">
                    Annuler
                </a>
                <button type="submit" 
                        class="bg-blue-500 hover:bg-blue-600 text-white px-6 py-2 rounded-md">
                    Créer le plan
                </button>
            </div>
        </form>
    </div>
</div>