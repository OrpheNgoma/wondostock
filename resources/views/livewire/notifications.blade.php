<div
    x-data="{
        init() {
            $wire.on('notification-shown', () => { 
                const timeout = $wire.type === 'error' ? 8000 : 5000;
                setTimeout(() => { 
                    $wire.show = false;
                }, timeout);
            });
        }
    }"
    :class="$wire.show ? 'pointer-events-auto fixed top-5 right-5 z-50 w-full max-w-sm' : 'pointer-events-none fixed top-5 right-5 z-50 w-full max-w-sm opacity-0'"
    x-transition:enter="transform ease-out duration-300 transition"
    x-transition:enter-start="translate-y-2 opacity-0 sm:translate-y-0 sm:translate-x-2"
    x-transition:enter-end="translate-y-0 opacity-100 sm:translate-x-0"
    x-transition:leave="transition ease-in duration-100"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
>
    <div class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-xl shadow-2xl ring-1 ring-black ring-opacity-5"
         :class="{
             'bg-gradient-to-r from-emerald-50 to-emerald-100 border border-emerald-200': $wire.type === 'success',
             'bg-gradient-to-r from-red-50 to-red-100 border border-red-200': $wire.type === 'error',
             'bg-gradient-to-r from-amber-50 to-amber-100 border border-amber-200': $wire.type === 'warning',
             'bg-gradient-to-r from-blue-50 to-blue-100 border border-blue-200': $wire.type === 'info',
             'bg-white border border-gray-200': !['success', 'error', 'warning', 'info'].includes($wire.type)
         }">
        <div class="p-4">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <!-- Icône Success -->
                    <template x-if="$wire.type === 'success'">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-emerald-500">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </template>
                    
                    <!-- Icône Error -->
                    <template x-if="$wire.type === 'error'">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-red-500">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </div>
                    </template>
                    
                    <!-- Icône Warning -->
                    <template x-if="$wire.type === 'warning'">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-amber-500">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                            </svg>
                        </div>
                    </template>
                    
                    <!-- Icône Info -->
                    <template x-if="$wire.type === 'info'">
                        <div class="flex h-8 w-8 items-center justify-center rounded-full bg-blue-500">
                            <svg class="h-5 w-5 text-white" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                            </svg>
                        </div>
                    </template>
                </div>
                
                <div class="ml-3 w-0 flex-1 pt-0.5">
                    <p class="text-sm font-semibold"
                       :class="{
                           'text-emerald-800': $wire.type === 'success',
                           'text-red-800': $wire.type === 'error',
                           'text-amber-800': $wire.type === 'warning',
                           'text-blue-800': $wire.type === 'info',
                           'text-gray-900': !['success', 'error', 'warning', 'info'].includes($wire.type)
                       }" 
                       x-text="$wire.message"></p>
                </div>
                
                <div class="ml-4 flex flex-shrink-0">
                    <button @click="$wire.show = false" type="button" 
                            class="inline-flex rounded-full p-1.5 transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2"
                            :class="{
                                'text-emerald-600 hover:text-emerald-800 hover:bg-emerald-200 focus:ring-emerald-500': $wire.type === 'success',
                                'text-red-600 hover:text-red-800 hover:bg-red-200 focus:ring-red-500': $wire.type === 'error',
                                'text-amber-600 hover:text-amber-800 hover:bg-amber-200 focus:ring-amber-500': $wire.type === 'warning',
                                'text-blue-600 hover:text-blue-800 hover:bg-blue-200 focus:ring-blue-500': $wire.type === 'info',
                                'text-gray-400 hover:text-gray-600 hover:bg-gray-200 focus:ring-gray-500': !['success', 'error', 'warning', 'info'].includes($wire.type)
                            }">
                        <span class="sr-only">Fermer</span>
                        <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M6.28 5.22a.75.75 0 00-1.06 1.06L8.94 10l-3.72 3.72a.75.75 0 101.06 1.06L10 11.06l3.72 3.72a.75.75 0 101.06-1.06L11.06 10l3.72-3.72a.75.75 0 00-1.06-1.06L10 8.94 6.28 5.22z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
