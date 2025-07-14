<section class="bg-gradient-to-br from-slate-50 to-blue-50 h-screen overflow-hidden" x-data="{
        showPassword: false,
        init() {
            this.$nextTick(() => {
                document.addEventListener('keydown', (e) => {
                    if (e.key === 'Enter' && (e.target.type === 'email' || e.target.type === 'password')) {
                        e.preventDefault();
                        this.$wire.login();
                    }
                });
            });
        }
    }">
  <div class="lg:grid lg:h-screen lg:grid-cols-12">
    <section class="relative flex h-32 items-end lg:col-span-5 lg:h-full xl:col-span-6 overflow-hidden">
      <!-- Image sans overlay pour préserver le slogan original -->
      <img
        alt="WondoStock Background"
        src="images/wondo-cover-02.jpg"
        class="absolute inset-0 h-full w-full object-cover"
      />
    </section>

    <main class="flex flex-col justify-center px-4 py-4 sm:px-8 lg:col-span-7 lg:px-12 xl:col-span-6 relative h-full">
      <!-- Effets de fond subtils -->
      <div class="absolute inset-0 overflow-hidden pointer-events-none">
        <div class="absolute top-20 -right-20 w-40 h-40 bg-gradient-to-br from-blue-200/20 to-purple-200/20 rounded-full blur-2xl"></div>
        <div class="absolute bottom-20 -left-20 w-40 h-40 bg-gradient-to-br from-emerald-200/20 to-blue-200/20 rounded-full blur-2xl"></div>
      </div>
      
      <div class="max-w-md lg:max-w-lg relative z-10 w-full mx-auto">
        <!-- Header avec logo optimisé -->
        <div class="text-center mb-6">
          <div class="inline-flex items-center justify-center mb-4">
            <a class="inline-flex size-16 items-center justify-center rounded-full bg-white text-blue-600 shadow-lg border-2 border-blue-100 hover:scale-105 transition-transform duration-300" href="#">
              <img class="h-auto w-auto" src="images/LOGO WONDO_login.png" alt="WondoStock Logo">
            </a>
          </div>
          
          <div class="space-y-1">
            <h1 class="text-2xl font-bold text-gray-900 leading-tight">
              Connexion WondoStock
            </h1>
            <p class="text-gray-600 text-sm">
              Connectez-vous à votre espace de gestion
            </p>
          </div>
        </div>

        <!-- Formulaire optimisé -->
        <div class="bg-white rounded-2xl shadow-xl border border-gray-200 p-5">
          <form wire:submit.prevent="login" class="space-y-4">
            
            <!-- Champ Email -->
            <div class="space-y-2">
              <label for="email" class="block text-sm font-medium text-gray-700">
                Adresse email
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"/>
                  </svg>
                </div>
                <input
                  type="email"
                  id="email"
                  wire:model="email"
                  placeholder="exemple@votreentreprise.com"
                  autocomplete="email"
                  class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  required
                />
              </div>
              @error('email') 
              <div class="flex items-center space-x-1 text-red-600 text-sm mt-1">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <span>{{ $message }}</span>
              </div>
              @enderror
            </div>

            <!-- Champ Mot de passe -->
            <div class="space-y-2">
              <label for="password" class="block text-sm font-medium text-gray-700">
                Mot de passe
              </label>
              <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                  <svg class="h-5 w-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                  </svg>
                </div>
                <input
                  :type="showPassword ? 'text' : 'password'"
                  id="password"
                  wire:model="password"
                  placeholder="Entrez votre mot de passe"
                  autocomplete="current-password"
                  class="w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-gray-900 placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors"
                  required
                />
                <button 
                  type="button"
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
            </div>
            <!-- Options de connexion -->
            <div class="flex items-center justify-between">
              <label for="remember" class="flex items-center space-x-2 cursor-pointer">
                <input
                  type="checkbox"
                  id="remember"
                  wire:model="remember"
                  class="w-4 h-4 text-blue-600 border-gray-300 rounded focus:ring-blue-500"
                />
                <span class="text-sm text-gray-700">Se souvenir de moi</span>
              </label>
              
              <a href="#" class="text-sm text-blue-600 hover:text-blue-800 transition-colors">
                Mot de passe oublié ?
              </a>
            </div>

            <!-- Bouton de connexion -->
            <div class="pt-2">
              <button 
                type="submit" 
                class="w-full bg-gradient-to-r from-blue-600 to-blue-700 text-white font-semibold py-3 px-4 rounded-lg hover:from-blue-700 hover:to-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors shadow-lg">
                <span wire:loading.remove wire:target="login">Se connecter</span>
                <span wire:loading wire:target="login" class="flex items-center justify-center space-x-2">
                  <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                  </svg>
                  <span>Connexion...</span>
                </span>
              </button>
            </div>
            
            <!-- Lien d'inscription -->
            <div class="text-center pt-3 border-t border-gray-200">
              <p class="text-sm text-gray-600">
                Pas de compte ?
                <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-800 font-medium transition-colors">
                  Inscrivez-vous
                </a>
              </p>
            </div>
          </form>
        </div>
        
        <!-- Copyright -->
        <div class="text-center mt-4">
          <p class="text-xs text-gray-500">
            Conçu et maintenu par: <span class="font-medium text-gray-600">Pixel Parfait</span>
          </p>
        </div>
      </div>
    </main>
  </div>
  
  <!-- Styles pour les animations -->
  <style>
    @keyframes fade-in-up {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    @keyframes fade-in-down {
      from {
        opacity: 0;
        transform: translateY(-30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }
    
    .animate-fade-in-up {
      animation: fade-in-up 1s ease-out;
    }
    
    .animate-fade-in-down {
      animation: fade-in-down 1s ease-out;
    }
  </style>
</section>
