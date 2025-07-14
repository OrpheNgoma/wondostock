<div x-data="{
        showShortcuts: false,
        init() {
            this.$nextTick(() => {
                document.addEventListener('keydown', (e) => {
                    if (e.ctrlKey && e.key === 's') {
                        e.preventDefault();
                        this.$wire.save();
                        return;
                    }
                    if (e.key === 'Escape') {
                        this.showShortcuts = false;
                    }
                });
            });
        }
    }" 
    class="min-h-screen bg-gradient-to-br from-blue-50 via-white to-purple-50">
    
    <!-- Header moderne avec gradient -->
    <div class="sticky top-0 z-20 bg-white/95 backdrop-blur-lg border-b border-blue-200 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center space-x-6">
                    <div class="flex items-center space-x-3">
                        <div class="w-12 h-12 bg-gradient-to-r from-blue-500 to-purple-600 rounded-xl flex items-center justify-center shadow-lg">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <div>
                            <h1 class="text-2xl font-bold text-gray-900">Paramètres Entreprise</h1>
                            <p class="text-sm text-gray-600">Configuration et informations légales</p>
                        </div>
                    </div>
                    
                    <!-- Indicateurs live -->
                    <div class="hidden lg:flex items-center space-x-4">
                        <div class="flex items-center space-x-2 px-3 py-2 bg-blue-100 rounded-lg">
                            <div class="w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                            <span class="text-sm font-medium text-blue-700">{{ $company->name }}</span>
                        </div>
                        <div class="flex items-center space-x-2 px-3 py-2 bg-purple-100 rounded-lg">
                            <svg class="w-4 h-4 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="text-sm font-medium text-purple-700">{{ now()->format('H:i') }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="flex items-center space-x-3">
                    <!-- Raccourcis clavier -->
                    <button @click="showShortcuts = !showShortcuts" 
                            class="p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition-colors relative"
                            title="Raccourcis clavier">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 9l3 3-3 3m5 0h3M5 20h14a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        <div class="absolute -top-1 -right-1 w-3 h-3 bg-blue-500 rounded-full animate-pulse"></div>
                    </button>
                    
                    <a href="{{ route('dashboard') }}"
                       class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        ← Retour
                    </a>
                    
                    <button form="company-form" type="submit" 
                            class="px-6 py-2 text-sm font-semibold text-white bg-gradient-to-r from-blue-600 to-purple-600 rounded-lg hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg">
                        <span wire:loading.remove wire:target="save">💾 Sauvegarder</span>
                        <span wire:loading wire:target="save" class="flex items-center">
                            <svg class="animate-spin -ml-1 mr-2 h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Sauvegarde...
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Popup raccourcis clavier -->
    <div x-show="showShortcuts" x-transition 
         @click.outside="showShortcuts = false"
         class="fixed inset-0 z-50 flex items-center justify-center bg-black/50">
        <div class="bg-white rounded-xl p-6 mx-4 max-w-md shadow-2xl">
            <h3 class="text-lg font-semibold mb-4">Raccourcis clavier</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between"><span>Sauvegarder</span><kbd class="px-2 py-1 bg-gray-100 rounded">Ctrl + S</kbd></div>
                <div class="flex justify-between"><span>Fermer popup</span><kbd class="px-2 py-1 bg-gray-100 rounded">Esc</kbd></div>
            </div>
            <button @click="showShortcuts = false" class="mt-4 w-full px-4 py-2 bg-gray-100 rounded-lg hover:bg-gray-200">Fermer</button>
        </div>
    </div>

    <form id="company-form" wire:submit.prevent="save" class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-8">


        <!-- Section Informations Générales -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-blue-500 to-blue-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    Informations Générales
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Nom commercial <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="name" 
                                   placeholder="Nom affiché publiquement"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Raison sociale
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="legal_name" 
                                   placeholder="Raison sociale officielle"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Email de contact
                        </label>
                        <div class="relative">
                            <input type="email" wire:model="email" 
                                   placeholder="contact@monentreprise.com"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Téléphone
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="phone_number" 
                                   placeholder="+243 XXX XXX XXX"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            Adresse complète
                        </label>
                        <div class="relative">
                            <textarea wire:model="address" rows="3" 
                                      placeholder="Avenue de la Paix, Kinshasa, RDC"
                                      class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"></textarea>
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Informations Légales & Logo -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-8">
            <div class="bg-gradient-to-r from-purple-500 to-purple-600 px-6 py-4">
                <h2 class="text-lg font-semibold text-white flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"/>
                    </svg>
                    Informations Légales & Logo
                </h2>
            </div>
            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            N° RCCM
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="rccm" 
                                   placeholder="CD/KGA/RCCM/23-A-12345"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-gray-700">
                            NIF (Numéro Fiscal)
                        </label>
                        <div class="relative">
                            <input type="text" wire:model="nif" 
                                   placeholder="A23456789"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 transition-colors">
                            <svg class="absolute left-3 top-3.5 w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                        </div>
                    </div>
                    
                    <div class="md:col-span-2 space-y-4">
                        <label class="block text-sm font-medium text-gray-700">
                            Logo de l'entreprise
                        </label>
                        
                        <div class="bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl p-6 border-2 border-dashed border-gray-300">
                            <div class="flex items-center space-x-6">
                                <!-- Aperçu du logo -->
                                <div class="flex-shrink-0">
                                    @if ($logo && method_exists($logo, 'getMimeType') && Str::startsWith($logo->getMimeType(), 'image/'))
                                        <img src="{{ $logo->temporaryUrl() }}" class="h-20 w-20 rounded-xl object-cover border-2 border-purple-200 shadow-lg">
                                    @elseif ($company->hasMedia('logo'))
                                        <img src="{{ $company->getFirstMediaUrl('logo') }}" class="h-20 w-20 rounded-xl object-cover border-2 border-purple-200 shadow-lg">
                                    @else
                                        <div class="h-20 w-20 rounded-xl bg-gradient-to-br from-purple-100 to-blue-100 flex items-center justify-center border-2 border-purple-200">
                                            <svg class="h-10 w-10 text-purple-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                                            </svg>
                                        </div>
                                    @endif
                                </div>
                                
                                <!-- Zone de upload -->
                                <div class="flex-1">
                                    <input type="file" wire:model="logo" id="logo" class="hidden" accept="image/*">
                                    <label for="logo" class="cursor-pointer group">
                                        <div class="flex flex-col items-center justify-center space-y-3 p-6 border-2 border-dashed border-purple-300 rounded-lg hover:border-purple-400 hover:bg-purple-50 transition-all">
                                            <svg class="h-8 w-8 text-purple-400 group-hover:text-purple-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                                            </svg>
                                            <div class="text-center">
                                                <p class="text-sm font-medium text-gray-700 group-hover:text-purple-600">Cliquez pour télécharger</p>
                                                <p class="text-xs text-gray-500">PNG, JPG jusqu'à 1MB</p>
                                                <p class="text-xs text-purple-600 font-medium">Recommandé: 200x200px</p>
                                            </div>
                                        </div>
                                    </label>
                                </div>
                            </div>
                            
                            @error('logo')
                            <div class="mt-3 flex items-center space-x-2 text-red-600">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span class="text-sm">{{ $message }}</span>
                            </div>
                            @enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Actions et résumé -->
        <div class="bg-gradient-to-br from-emerald-50 to-blue-50 rounded-xl p-6 border border-emerald-200">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="font-semibold text-gray-900 mb-1">Prêt à sauvegarder ?</h3>
                    <p class="text-sm text-gray-600">Vérifiez vos informations avant de confirmer</p>
                </div>
                
                <div class="flex items-center space-x-3">
                    <button type="button" onclick="window.location.reload()" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition-colors">
                        🔄 Réinitialiser
                    </button>
                    
                    <button type="submit" 
                            class="px-6 py-3 text-sm font-semibold text-white bg-gradient-to-r from-emerald-600 to-blue-600 rounded-lg hover:from-emerald-700 hover:to-blue-700 transition-all shadow-lg transform hover:scale-105">
                        <span wire:loading.remove wire:target="save" class="flex items-center space-x-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                            <span>Sauvegarder les modifications</span>
                        </span>
                        <span wire:loading wire:target="save" class="flex items-center space-x-2">
                            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span>Sauvegarde en cours...</span>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>