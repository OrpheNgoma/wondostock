@section('title', $product->exists ? 'Modifier le Produit' : 'Nouveau Produit')

<div class="space-y-8">
    <!-- En-tête moderne avec gradient -->
    <div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-emerald-500 via-emerald-600 to-teal-700 p-8 shadow-2xl">
        <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/20 to-teal-700/20 backdrop-blur-sm"></div>
        <div class="relative">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">
                        {{ $product->exists ? 'Modifier le Produit' : 'Nouveau Produit' }}
                    </h1>
                    <p class="text-emerald-100 text-lg">
                        {{ $product->exists ? 'Mettre à jour les informations du produit' : 'Ajouter un nouveau produit au catalogue' }}
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('products.index') }}" 
                       wire:navigate 
                       class="inline-flex items-center gap-2 rounded-xl bg-white/10 backdrop-blur-sm px-6 py-3 text-sm font-semibold text-white shadow-lg ring-1 ring-white/20 hover:bg-white/20 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
                        </svg>
                        Retour
                    </a>
                    <button type="submit" 
                            form="product-form"
                            class="inline-flex items-center gap-2 rounded-xl bg-white text-emerald-600 px-6 py-3 text-sm font-semibold shadow-lg hover:bg-gray-50 transition-all duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        {{ $product->exists ? 'Mettre à jour' : 'Créer le produit' }}
                    </button>
                </div>
            </div>
        </div>
        <div class="absolute -bottom-1 -right-1 h-32 w-32 rounded-full bg-white/10 blur-2xl"></div>
        <div class="absolute -top-1 -left-1 h-24 w-24 rounded-full bg-white/10 blur-xl"></div>
    </div>

<form wire:submit.prevent="save" id="product-form">

    <!-- Contenu du formulaire moderne -->
    <div class="grid grid-cols-1 gap-8 lg:grid-cols-12">
        <!-- Colonne principale : Informations du produit -->
        <div class="lg:col-span-8">
            <div class="space-y-8">
                <!-- Section : Informations de base -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-emerald-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 6h.008v.008H6V6z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Informations de base</h3>
                                <p class="text-sm text-gray-600">Détails principaux du produit</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                            <!-- Nom du produit -->
                            <div class="sm:col-span-2">
                                <label for="name" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.568 3H5.25A2.25 2.25 0 003 5.25v4.318c0 .597.237 1.17.659 1.591l9.581 9.581c.699.699 1.78.872 2.607.33a18.095 18.095 0 005.223-5.223c.542-.827.369-1.908-.33-2.607L11.16 3.66A2.25 2.25 0 009.568 3z" />
                                        </svg>
                                        Nom du produit *
                                    </span>
                                </label>
                                <input type="text" 
                                       wire:model="formData.name" 
                                       id="name" 
                                       placeholder="Entrez le nom du produit"
                                       class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('formData.name') 
                                    <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                            <!-- SKU -->
                            <div>
                                <label for="sku" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                        </svg>
                                        SKU (Référence) *
                                    </span>
                                </label>
                                <input type="text" 
                                       wire:model="formData.sku" 
                                       id="sku" 
                                       placeholder="Ex: PRD-001"
                                       class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('formData.sku') 
                                    <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <!-- Unité de mesure -->
                            <div>
                                <label for="unit_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v17.25m0 0c-1.472 0-2.882.265-4.185.75M12 20.25c1.472 0 2.882.265 4.185.75M18.75 4.97A48.254 48.254 0 0112 4.5c-2.291 0-4.545.16-6.75.47m13.5 0c1.01.143 2.01.317 3 .52m-3-.52l2.62 10.726c.122.499-.106 1.012-.568 1.314l-2.436 1.596c-.905.593-2.07.593-2.975 0l-2.436-1.596a1.125 1.125 0 01-.568-1.314L8.25 5.5" />
                                        </svg>
                                        Unité de mesure
                                    </span>
                                </label>
                                <select wire:model="formData.unit_id" 
                                        id="unit_id" 
                                        class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                    <option value="">Sélectionner une unité</option>
                                    @foreach($units as $unit)
                                        <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <!-- Type de produit -->
                            <div class="sm:col-span-2">
                                <label for="type" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Type de produit
                                    </span>
                                </label>
                                <select wire:model.live="formData.type" 
                                        id="type" 
                                        class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                    <option value="simple">Simple</option>
                                    <option value="variable">Variable (avec variantes)</option>
                                </select>
                            </div>

                            <!-- Prix -->
                            <div>
                                <label for="selling_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0l.879-.659M7.5 14.25l-2.489-1.867a.75.75 0 01.3-1.382l4.12 2.355M16.5 14.25l2.489-1.867a.75.75 0 00-.3-1.382l-4.12 2.355" />
                                        </svg>
                                        Prix de vente (XAF) *
                                    </span>
                                </label>
                                <input type="number" 
                                       wire:model="formData.selling_price" 
                                       id="selling_price" 
                                       placeholder="0"
                                       class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                                @error('formData.selling_price') 
                                    <div class="mt-2 flex items-center gap-2 text-red-600 text-sm">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                                        </svg>
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>

                            <div>
                                <label for="purchase_price" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v-.75A.75.75 0 013 4.5h.75zM21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        Prix d'achat (XAF)
                                    </span>
                                </label>
                                <input type="number" 
                                       wire:model="formData.purchase_price" 
                                       id="purchase_price" 
                                       placeholder="0"
                                       class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200">
                            </div>

                            <!-- Description -->
                            <div class="sm:col-span-2">
                                <label for="description" class="block text-sm font-semibold text-gray-700 mb-2">
                                    <span class="flex items-center gap-2">
                                        <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                        </svg>
                                        Description
                                    </span>
                                </label>
                                <textarea wire:model="formData.description" 
                                          id="description" 
                                          rows="4" 
                                          placeholder="Description détaillée du produit..."
                                          class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 placeholder:text-gray-400 focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition-all duration-200 resize-none"></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section : Variantes (conditionnelle) -->
                <div x-data="{ type: @entangle('formData.type') }" x-show="type === 'variable'" x-transition class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100"
>
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-purple-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-purple-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.324.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.296 2.247a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.24-.438.613-.431.992a6.759 6.759 0 010 .255c-.007.378.138.75.43.99l1.005.828c.424.35.534.954.26 1.43l-1.298 2.247a1.125 1.125 0 01-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.28c-.09.543-.56.941-1.11.941h-2.594c-.55 0-1.02-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.297-2.247a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.992a6.932 6.932 0 010-.255c.007-.378-.138-.75-.43-.99l-1.004-.828a1.125 1.125 0 01-.26-1.43l1.297-2.247a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.087.22-.128.332-.183.582-.495.644-.869l.214-1.281z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Attributs & Variantes</h3>
                                <p class="text-sm text-gray-600">Configuration des variantes du produit</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <div class="space-y-4">
                            @foreach($product_attributes as $index => $attribute)
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 p-4 rounded-xl bg-gray-50 border border-gray-200" wire:key="attribute-{{$index}}">
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Nom de l'attribut</label>
                                    <input type="text" 
                                           wire:model="product_attributes.{{$index}}.name" 
                                           placeholder="Ex: Taille, Couleur"
                                           class="block w-full rounded-lg border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-purple-500 transition-all duration-200">
                                </div>
                                <div class="flex gap-2">
                                    <div class="flex-1">
                                        <label class="block text-sm font-semibold text-gray-700 mb-2">Valeurs (séparées par une virgule)</label>
                                        <input type="text" 
                                               wire:model="product_attributes.{{$index}}.values" 
                                               placeholder="S, M, L, XL"
                                               class="block w-full rounded-lg border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-purple-500 transition-all duration-200">
                                    </div>
                                    <div class="flex items-end">
                                        <button wire:click.prevent="removeAttribute({{$index}})" 
                                                type="button" 
                                                class="inline-flex items-center justify-center h-10 w-10 rounded-lg bg-red-100 text-red-600 hover:bg-red-200 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <div class="flex gap-3 pt-4">
                            <button wire:click.prevent="addAttribute" 
                                    type="button" 
                                    class="inline-flex items-center gap-2 rounded-xl bg-white px-4 py-2 text-sm font-semibold text-gray-700 shadow-sm ring-1 ring-gray-200 hover:bg-gray-50 transition-all duration-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                                </svg>
                                Ajouter un attribut
                            </button>
                            <button wire:click.prevent="generateVariants" 
                                    type="button" 
                                    class="inline-flex items-center gap-2 rounded-xl bg-purple-600 px-4 py-2 text-sm font-semibold text-white shadow-sm hover:bg-purple-500 transition-all duration-200">
                                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9.813 15.904L9 18.75l-.813-2.846a4.5 4.5 0 00-3.09-3.09L2.25 12l2.846-.813a4.5 4.5 0 003.09-3.09L9 5.25l.813 2.846a4.5 4.5 0 003.09 3.09L15.75 12l-2.846.813a4.5 4.5 0 00-3.09 3.09z" />
                                </svg>
                                Générer les variantes
                            </button>
                        </div>

                        @if(!empty($variants))
                        <div class="mt-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-4 flex items-center gap-2">
                                <svg class="h-4 w-4 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Variantes générées
                            </h4>
                            <div class="space-y-3">
                                @foreach($variants as $index => $variant)
                                <div wire:key="variant-{{$index}}" class="grid grid-cols-1 md:grid-cols-3 gap-4 p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Variante</label>
                                        <div class="text-sm font-semibold text-gray-900 bg-white rounded-lg px-3 py-2 border">{{ $variant['name'] }}</div>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">SKU</label>
                                        <input type="text" 
                                               wire:model="variants.{{$index}}.sku" 
                                               class="block w-full rounded-lg border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-green-500 transition-all duration-200">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-1">Prix de Vente (XAF)</label>
                                        <input type="number" 
                                               wire:model="variants.{{$index}}.selling_price" 
                                               class="block w-full rounded-lg border-0 py-2 px-3 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-green-500 transition-all duration-200">
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar : Organisation et Images -->
        <div class="lg:col-span-4">
            <div class="space-y-6">
                <!-- Section : Organisation -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-blue-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 7.125C2.25 6.504 2.754 6 3.375 6h.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125h-.75A1.125 1.125 0 012.25 8.875v-.75zM10.5 7.125C10.5 6.504 11.004 6 11.625 6h.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125h-.75A1.125 1.125 0 0110.5 8.875v-.75zM18.75 7.125C18.75 6.504 19.254 6 19.875 6h.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125h-.75A1.125 1.125 0 0118.75 8.875v-.75zM2.25 15.375C2.25 14.754 2.754 14.25 3.375 14.25h.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125h-.75a1.125 1.125 0 01-1.125-1.125v-.75zM10.5 15.375c0-.621.504-1.125 1.125-1.125h.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125h-.75a1.125 1.125 0 01-1.125-1.125v-.75zM18.75 15.375c0-.621.504-1.125 1.125-1.125h.75c.621 0 1.125.504 1.125 1.125v.75c0 .621-.504 1.125-1.125 1.125h-.75a1.125 1.125 0 01-1.125-1.125v-.75z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Organisation</h3>
                                <p class="text-sm text-gray-600">Classification et paramètres</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6 space-y-6">
                        <!-- Catégorie -->
                        <div>
                            <label for="category_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                                    </svg>
                                    Catégorie
                                </span>
                            </label>
                            <select wire:model="formData.category_id" 
                                    id="category_id" 
                                    class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Sélectionner une catégorie</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Taxe -->
                        <div>
                            <label for="tax_id" class="block text-sm font-semibold text-gray-700 mb-2">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 8.25H9m6 3H9m3 6l-3-3h1.5a3 3 0 100-6M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Taxe
                                </span>
                            </label>
                            <select wire:model="formData.tax_id" 
                                    id="tax_id" 
                                    class="block w-full rounded-xl border-0 py-3 px-4 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-200 focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-all duration-200">
                                <option value="">Aucune taxe</option>
                                @foreach($taxes as $tax)
                                    <option value="{{ $tax->id }}">{{ $tax->name }} ({{$tax->rate}}%)</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Statut actif -->
                        <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-green-100 flex items-center justify-center">
                                    <svg class="h-5 w-5 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                </div>
                                <div>
                                    <label for="is_active" class="text-sm font-semibold text-gray-900">Produit Actif</label>
                                    <p class="text-xs text-gray-600">Le produit sera visible dans le catalogue</p>
                                </div>
                            </div>
                            <div class="flex items-center">
                                <input id="is_active" 
                                       wire:model="formData.is_active" 
                                       type="checkbox" 
                                       class="h-5 w-5 rounded border-gray-300 text-green-600 focus:ring-green-500 focus:ring-offset-0">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Section : Images -->
                <div class="overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100">
                    <div class="border-b border-gray-100 bg-gradient-to-r from-gray-50 to-white px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-lg bg-indigo-100 flex items-center justify-center">
                                <svg class="h-4 w-4 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 015.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 00-1.134-.175 2.31 2.31 0 01-1.64-1.055l-.822-1.316a2.192 2.192 0 00-1.736-1.039 48.774 48.774 0 00-5.232 0 2.192 2.192 0 00-1.736 1.039l-.821 1.316z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 11-9 0 4.5 4.5 0 019 0zM18.75 10.5h.008v.008h-.008V10.5z" />
                                </svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900">Images</h3>
                                <p class="text-sm text-gray-600">Photos du produit</p>
                            </div>
                        </div>
                    </div>
                    <div class="p-6">
                        <!-- Images existantes -->
                        @if ($existingImages && count($existingImages) > 0)
                        <div class="mb-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Images actuelles</h4>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($existingImages as $image)
                                <div class="relative group">
                                    <img src="{{ $image->getUrl() }}" 
                                         class="w-full aspect-square rounded-xl object-cover shadow-sm ring-1 ring-gray-200">
                                    <div class="absolute inset-0 bg-black/60 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-200 rounded-xl">
                                        <button wire:click.prevent="removeImage({{ $image->id }})" 
                                                wire:confirm="Êtes-vous sûr de vouloir supprimer cette image ?"
                                                type="button" 
                                                class="inline-flex items-center justify-center h-10 w-10 rounded-full bg-red-600 text-white hover:bg-red-700 transition-colors duration-200">
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <!-- Zone d'upload -->
                        <div>
                            <label for="newImages" class="block text-sm font-semibold text-gray-700 mb-3">
                                <span class="flex items-center gap-2">
                                    <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 16.5V9.75m0 0l3 3m-3-3l-3 3M6.75 19.5a4.5 4.5 0 01-1.41-8.775 5.25 5.25 0 0110.233-2.33 3 3 0 013.758 3.848A3.752 3.752 0 0118 19.5H6.75z" />
                                    </svg>
                                    Ajouter de nouvelles images
                                </span>
                            </label>
                            <div class="relative">
                                <input type="file" 
                                       wire:model="newImages" 
                                       multiple 
                                       id="newImages" 
                                       accept="image/*"
                                       class="block w-full text-sm text-gray-500 
                                              file:mr-4 file:py-3 file:px-4 
                                              file:rounded-xl file:border-0 
                                              file:text-sm file:font-semibold 
                                              file:bg-indigo-50 file:text-indigo-700 
                                              hover:file:bg-indigo-100 
                                              file:transition-colors file:duration-200
                                              border border-gray-200 rounded-xl p-3
                                              focus:ring-2 focus:ring-indigo-500 focus:border-transparent">
                            </div>
                            
                            <!-- Indicateur de chargement -->
                            <div wire:loading wire:target="newImages" class="mt-3 flex items-center gap-2 text-indigo-600 text-sm">
                                <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Chargement des images...
                            </div>
                        </div>

                        <!-- Prévisualisation des nouvelles images -->
                        @if ($newImages)
                        <div class="mt-4">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Nouvelles images</h4>
                            <div class="grid grid-cols-2 gap-4">
                                @foreach ($newImages as $image)
                                    @if (str_starts_with($image->getMimeType(), 'image/'))
                                    <div class="relative">
                                        <img src="{{ $image->temporaryUrl() }}" 
                                             class="w-full aspect-square rounded-xl object-cover shadow-sm ring-1 ring-gray-200">
                                        <div class="absolute top-2 right-2">
                                            <span class="inline-flex items-center rounded-full bg-green-100 px-2 py-1 text-xs font-medium text-green-700">
                                                Nouveau
                                            </span>
                                        </div>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</div>
