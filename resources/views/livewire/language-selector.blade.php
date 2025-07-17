<div class="relative inline-block text-left" x-data="{ open: false }">
    <div>
        <button @click="open = !open" 
                type="button" 
                class="inline-flex items-center justify-center w-full px-3 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500" 
                id="language-menu-button" 
                aria-expanded="true" 
                aria-haspopup="true">
            
            {{-- Drapeau et langue actuelle --}}
            <div class="flex items-center space-x-2">
                @if($currentLocale === 'fr')
                    <span class="text-sm">🇫🇷</span>
                    <span>Français</span>
                @else
                    <span class="text-sm">🇺🇸</span>
                    <span>English</span>
                @endif
            </div>
            
            {{-- Icône dropdown --}}
            <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
    </div>

    {{-- Menu dropdown --}}
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-100"
         x-transition:enter-start="transform opacity-0 scale-95"
         x-transition:enter-end="transform opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-75"
         x-transition:leave-start="transform opacity-100 scale-100"
         x-transition:leave-end="transform opacity-0 scale-95"
         @click.away="open = false"
         class="absolute right-0 z-50 w-40 mt-2 origin-top-right bg-white border border-gray-200 rounded-md shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none" 
         role="menu" 
         aria-orientation="vertical" 
         aria-labelledby="language-menu-button" 
         tabindex="-1">
        
        <div class="py-1" role="none">
            @foreach($availableLocales as $locale => $name)
                <a href="{{ route('language.switch', $locale) }}" 
                   class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 hover:text-gray-900 {{ $currentLocale === $locale ? 'bg-gray-50 font-medium' : '' }}" 
                   role="menuitem" 
                   tabindex="-1">
                    
                    {{-- Drapeau --}}
                    <span class="mr-3 text-sm">
                        @if($locale === 'fr')
                            🇫🇷
                        @else
                            🇺🇸
                        @endif
                    </span>
                    
                    {{-- Nom de la langue --}}
                    <span>{{ $name }}</span>
                    
                    {{-- Indicateur langue active --}}
                    @if($currentLocale === $locale)
                        <svg class="w-4 h-4 ml-auto text-blue-600" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                        </svg>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</div>