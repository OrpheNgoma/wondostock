<section class="bg-gradient-to-br from-slate-50 to-blue-50 min-h-screen" x-data="{
        showPassword: false,
        showConfirmPassword: false,
        step: 1,
        init() {
            this.$nextTick(() => {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && e.target.tagName === 'INPUT') {
                        e.preventDefault();
                        this.$wire.submit();
                    }
                });
            });
        }
    }">
    
    <!-- Header avec logo et branding -->
    <div class="relative bg-white border-b border-gray-200 shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex items-center justify-between h-20">
                <div class="flex items-center space-x-4">
                    <div class="relative">
                        <div class="absolute inset-0 bg-gradient-to-r from-blue-600 to-purple-600 rounded-full blur-xl opacity-30"></div>
                        <a class="relative inline-flex size-16 items-center justify-center rounded-full bg-white shadow-xl border-2 border-blue-100" href="{{ route('login') }}">
                            <img class="h-10 w-auto" src="images/LOGO WONDO_login.png" alt="WondoStock Logo">
                        </a>
                    </div>
                    <div>
                        <h1 class="text-2xl font-bold text-gray-900">WondoStock</h1>
                        <p class="text-sm text-gray-600">Votre solution de gestion d'inventaire</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center space-x-2 text-sm text-gray-600">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                        </svg>
                        <span>Gratuit pendant 30 jours</span>
                    </div>
                    <a href="{{ route('login') }}" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">
                        Déjà inscrit ? Se connecter
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
            
            <!-- Colonne gauche : Avantages et réassurance -->
            <div class="space-y-8">
                <div class="text-center lg:text-left">
                    <h2 class="text-4xl font-bold text-gray-900 leading-tight">
                        Créez votre compte
                        <span class="block text-transparent bg-clip-text bg-gradient-to-r from-blue-600 to-purple-600">
                            WondoStock
                        </span>
                    </h2>
                    <p class="mt-4 text-xl text-gray-600">
                        Démarrez votre gestion d'inventaire professionnelle en moins de 2 minutes
                    </p>
                </div>
                
                <!-- Avantages -->
                <div class="space-y-6">
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-emerald-500 to-emerald-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Configuration instantanée</h3>
                            <p class="text-gray-600">Votre entreprise sera prête à l'emploi en quelques clics</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-blue-500 to-blue-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Sécurisé et isolé</h3>
                            <p class="text-gray-600">Vos données sont protégées et isolées par entreprise</p>
                        </div>
                    </div>
                    
                    <div class="flex items-start space-x-4">
                        <div class="flex-shrink-0">
                            <div class="w-10 h-10 bg-gradient-to-r from-purple-500 to-purple-600 rounded-lg flex items-center justify-center">
                                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                                </svg>
                            </div>
                        </div>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Multi-utilisateurs</h3>
                            <p class="text-gray-600">Invitez votre équipe et gérez les permissions</p>
                        </div>
                    </div>
                </div>
                
                <!-- Témoignage/Stats -->
                <div class="bg-gradient-to-r from-blue-50 to-purple-50 rounded-2xl p-6 border border-blue-200">
                    <div class="flex items-center space-x-4">
                        <div class="flex-shrink-0">
                            <div class="flex -space-x-2">
                                <div class="w-8 h-8 bg-gradient-to-r from-emerald-400 to-emerald-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">A</span>
                                </div>
                                <div class="w-8 h-8 bg-gradient-to-r from-blue-400 to-blue-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">B</span>
                                </div>
                                <div class="w-8 h-8 bg-gradient-to-r from-purple-400 to-purple-500 rounded-full border-2 border-white flex items-center justify-center">
                                    <span class="text-xs font-bold text-white">C</span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <p class="text-sm text-gray-700 font-medium">Rejignez les entreprises qui nous font confiance</p>
                            <p class="text-xs text-gray-600">Configuration complète en moins de 5 minutes</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Colonne droite : Formulaire d'inscription -->
            <div class="bg-white rounded-2xl shadow-2xl border border-gray-200 overflow-hidden">
                <!-- Header du formulaire -->
                <div class="bg-gradient-to-r from-blue-600 to-purple-600 px-8 py-6">
                    <h3 class="text-2xl font-bold text-white">Créer mon compte</h3>
                    <p class="text-blue-100 mt-1">Remplissez les informations ci-dessous</p>
                </div>
                
                <div class="px-8 py-8">
                    <form wire:submit.prevent="submit" class="space-y-6">
                        <!-- Nom de l'entreprise -->
                        <div class="space-y-2">
                            <label for="companyName" class="block text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                                </svg>
                                Nom de votre entreprise
                            </label>
                            <div class="relative">
                                <input wire:model="companyName" 
                                       id="companyName" 
                                       type="text" 
                                       placeholder="Ex: Ma Société SARL"
                                       required 
                                       class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            @error('companyName') 
                            <div class="flex items-center space-x-1 text-red-600 text-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Nom de l'utilisateur -->
                        <div class="space-y-2">
                            <label for="userName" class="block text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                Votre nom complet
                            </label>
                            <div class="relative">
                                <input wire:model="userName" 
                                       id="userName" 
                                       type="text" 
                                       placeholder="Ex: Jean Dupont"
                                       required 
                                       class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            @error('userName') 
                            <div class="flex items-center space-x-1 text-red-600 text-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Email -->
                        <div class="space-y-2">
                            <label for="email" class="block text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                                </svg>
                                Adresse email professionnelle
                            </label>
                            <div class="relative">
                                <input wire:model="email" 
                                       id="email" 
                                       type="email" 
                                       placeholder="votre.email@entreprise.com"
                                       autocomplete="email" 
                                       required 
                                       class="w-full pl-4 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                            </div>
                            @error('email') 
                            <div class="flex items-center space-x-1 text-red-600 text-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Mot de passe -->
                        <div class="space-y-2">
                            <label for="password" class="block text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                Mot de passe sécurisé
                            </label>
                            <div class="relative">
                                <input wire:model="password" 
                                       id="password" 
                                       :type="showPassword ? 'text' : 'password'"
                                       placeholder="Minimum 8 caractères"
                                       autocomplete="new-password" 
                                       required 
                                       class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <button type="button" 
                                        @click="showPassword = !showPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg x-show="!showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L12 12m-1.122-2.122l3.536 3.536M21 12a9.969 9.969 0 01-1.563 3.029m-5.858-.908L12 12m0 0L9.464 9.464M21 3l-18 18"/>
                                    </svg>
                                </button>
                            </div>
                            @error('password') 
                            <div class="flex items-center space-x-1 text-red-600 text-sm">
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ $message }}</span>
                            </div>
                            @enderror
                        </div>

                        <!-- Confirmation mot de passe -->
                        <div class="space-y-2">
                            <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">
                                <svg class="w-4 h-4 inline mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                Confirmer le mot de passe
                            </label>
                            <div class="relative">
                                <input wire:model="password_confirmation" 
                                       id="password_confirmation" 
                                       :type="showConfirmPassword ? 'text' : 'password'"
                                       placeholder="Répétez votre mot de passe"
                                       autocomplete="new-password" 
                                       required 
                                       class="w-full pl-4 pr-10 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors">
                                <button type="button" 
                                        @click="showConfirmPassword = !showConfirmPassword"
                                        class="absolute inset-y-0 right-0 pr-3 flex items-center">
                                    <svg x-show="!showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/>
                                    </svg>
                                    <svg x-show="showConfirmPassword" class="h-5 w-5 text-gray-400 hover:text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.878 9.878L8.464 8.464m1.414 1.414L12 12m-1.122-2.122l3.536 3.536M21 12a9.969 9.969 0 01-1.563 3.029m-5.858-.908L12 12m0 0L9.464 9.464M21 3l-18 18"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Conditions d'utilisation -->
                        <div class="bg-gray-50 rounded-lg p-4 text-sm text-gray-600">
                            <div class="flex items-start space-x-2">
                                <svg class="w-4 h-4 text-blue-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>
                                    En créant votre compte, vous acceptez nos 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">conditions d'utilisation</a> 
                                    et notre 
                                    <a href="#" class="text-blue-600 hover:text-blue-800 font-medium">politique de confidentialité</a>.
                                </span>
                            </div>
                        </div>
                        
                        <!-- Bouton de soumission -->
                        <div class="pt-2">
                            <button type="submit" 
                                    class="w-full bg-gradient-to-r from-blue-600 to-purple-600 text-white font-bold py-4 px-6 rounded-lg hover:from-blue-700 hover:to-purple-700 focus:outline-none focus:ring-4 focus:ring-blue-300 transform transition-all duration-300 hover:scale-105 shadow-xl hover:shadow-2xl">
                                <span wire:loading.remove wire:target="submit" class="flex items-center justify-center space-x-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                                    </svg>
                                    <span>Créer mon compte WondoStock</span>
                                </span>
                                <span wire:loading wire:target="submit" class="flex items-center justify-center space-x-2">
                                    <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <span>Création de votre compte...</span>
                                </span>
                            </button>
                        </div>
                        
                        <!-- Garantie -->
                        <div class="text-center pt-4 border-t border-gray-200">
                            <div class="flex items-center justify-center space-x-4 text-sm text-gray-600">
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>30 jours gratuits</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Sans engagement</span>
                                </div>
                                <div class="flex items-center space-x-1">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                                    </svg>
                                    <span>Support inclus</span>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Copyright -->
    <div class="bg-white border-t border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <p class="text-center text-sm text-gray-500">
                Conçu et maintenu par: <span class="font-medium text-gray-700">Pixel Parfait</span>
            </p>
        </div>
    </div>
</section>

