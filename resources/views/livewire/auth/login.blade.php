<section class="bg-white">
  <div class="lg:grid lg:min-h-screen lg:grid-cols-12">
    <section class="relative flex h-32 items-end lg:col-span-5 lg:h-full xl:col-span-6">
      <img
        alt=""
        src="images/wondo-cover-02.jpg"
        class="absolute inset-0 h-auto w-auto object-cover"
      />
      <div class="hidden lg:relative lg:block lg:p-12">
        {{-- <a class="block text-white" href="/">
           <img class="h-25 w-auto" src="images/LOGO WONDO STOCK-01.jpg" alt="KaziFlow Logo">
        </a> --}}
        {{-- <h2 class="mt-6 text-2xl font-bold text-white sm:text-3xl md:text-4xl">
          Bienvenue
        </h2> --}}
        {{-- <p class="mt-4 leading-relaxed text-white/90">
          Votre entreprise optimée, en quelques clics.
        </p> --}}
      </div>
    </section>

    <main class="flex items-center justify-center px-8 py-8 sm:px-12 lg:col-span-7 lg:px-16 lg:py-12 xl:col-span-6">
      <div class="max-w-xl lg:max-w-3xl">
        <div class="relative -mt-16 block">
            <a class="inline-flex size-16 items-center justify-center rounded-full bg-white text-blue-600 sm:size-20" href="#">
                 <img class="h-auto w-auto" src="images/LOGO WONDO_login.png" alt="KaziFlow Logo">
            </a>
            <h1 class="mt-2 text-2xl font-bold text-gray-900 sm:text-3xl md:text-4xl">
                 Connexion sur WondoStock
            </h1>
        </div>

        <form wire:submit.prevent="login" class="mt-4 grid grid-cols-8 gap-8">
            <div class="col-span-8">
                <label for="email" class="block text-sm/6 font-medium text-gray-900"> Email </label>
                    <input
                        type="email"
                        id="email"
                        name="email"
                        wire:model="email"
                        placeholder="exemple@votreentreprise.com"
                        autocomplete="email"
                        class="mt-2 block w-full rounded-md bg-white px-4 py-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-800 sm:text-sm/6"
                        required
                    />
                 @error('email') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
            </div>

            <div class="col-span-8">
                <label for="password" class="block text-sm/6 font-medium text-gray-900"> Mot de passe </label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        wire:model="password"
                        placeholder="Entrez votre mot de passe"
                        autocomplete="current-password"
                        class="mt-2 block w-full rounded-md bg-white px-4 py-3 text-base text-gray-900 outline-1 -outline-offset-1 outline-gray-300 placeholder:text-gray-400 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-900 sm:text-sm/6"
                        required
                    />
            </div>
            
            <div class="col-span-8 flex items-center justify-between">
                <label for="remember" class="flex gap-4">
                    <input
                        type="checkbox"
                        id="remember"
                        name="remember"
                        wire:model="remember"
                        class="size-5 rounded-md border-gray-200 bg-white shadow-sm"
                    />
                    <span class="text-sm text-gray-700"> Se souvenir de moi </span>
                </label>
                 <a href="#" class="text-sm text-indigo-600 hover:underline">Mot de passe oublié?</a>
            </div>

            <div class="col-span-8">
                <button type="submit" class="mt-4 flex w-full justify-center rounded-md bg-indigo-600 px-4 py-3.5 text-sm/6 font-semibold text-white shadow-xs hover:bg-indigo-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600">
                    Se connecter
                </button>
            </div>
            
             <div class="col-span-6 sm:flex sm:items-center sm:gap-4">
                <p class="mt-4 text-sm text-gray-500 sm:mt-0">
                    Vous n'avez pas de compte?
                    <a href="{{ route('register') }}" class="text-gray-700 underline" wire:navigate>Inscrivez-vous</a>.
                </p>
            </div>
        </form>
      </div>
    </main>
  </div>
</section>
