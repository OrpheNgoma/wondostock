@section('title', $product->exists ? 'Modifier le Produit' : 'Nouveau Produit')

<form wire:submit.prevent="save">
    <!-- En-tête -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl sm:tracking-tight">
                {{ $product->exists ? 'Modifier le Produit' : 'Nouveau Produit' }}
            </h2>
        </div>
        <div class="mt-5 flex sm:mt-0 sm:ml-4">
            <a href="{{ route('products.index') }}" wire:navigate class="text-sm font-semibold leading-6 text-gray-900">Annuler</a>
            <button type="submit" class="ml-3 inline-flex items-center rounded-md bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Sauvegarder</button>
        </div>
    </div>

    <!-- Contenu du formulaire en 2 colonnes -->
    <div class="mt-10 grid grid-cols-1 gap-x-8 gap-y-8 md:grid-cols-3">
        <!-- Colonne de Gauche : Informations principales -->
        <div class="md:col-span-2">
            <div class="grid grid-cols-1 gap-x-6 gap-y-8 sm:grid-cols-6 bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <div class="sm:col-span-full">
                    <label for="name" class="block text-sm font-medium leading-6 text-gray-900">Nom du produit</label>
                    <input type="text" wire:model="formData.name" id="name" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @error('formData.name') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="sku" class="block text-sm font-medium leading-6 text-gray-900">SKU (Référence)</label>
                    <input type="text" wire:model="formData.sku" id="sku" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @error('formData.sku') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                 <div class="sm:col-span-3">
                    <label for="unit_id" class="block text-sm font-medium leading-6 text-gray-900">Unité de mesure</label>
                    <select wire:model="formData.unit_id" id="unit_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}">{{ $unit->name }} ({{ $unit->symbol }})</option>
                        @endforeach
                    </select>
                </div>
                <!-- ... champ description ... -->
                <!-- Section pour les variantes -->
                <div class="sm:col-span-full" x-data="{ type: @entangle('formData.type') }">
                     <label for="type" class="block text-sm font-medium leading-6 text-gray-900">Type de produit</label>
                     <select wire:model.live="formData.type" id="type" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 ...">
                        <option value="simple">Simple</option>
                        <option value="variable">Variable (avec variantes)</option>
                    </select>

                    <!-- Section pour les variantes (conditionnelle) -->
                    <div x-data="{ type: @entangle('formData.type') }" x-show="type === 'variable'" x-transition class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                        <h3 class="text-base font-semibold leading-6 text-gray-900">Attributs & Variantes</h3>
                        <div class="mt-6 space-y-4">
                            @foreach($product_attributes as $index => $attribute)
                            <div class="flex items-end gap-x-3" wire:key="attribute-{{$index}}">
                                <div class="flex-grow"><label class="block text-sm font-medium">Nom de l'attribut (ex: Taille)</label><input type="text" wire:model="product_attributes.{{$index}}.name" class="mt-1 block w-full ..."></div>
                                <div class="flex-grow"><label class="block text-sm font-medium">Valeurs (séparées par une virgule)</label><input type="text" wire:model="product_attributes.{{$index}}.values" placeholder="S, M, L, XL" class="mt-1 block w-full ..."></div>
                                <button wire:click.prevent="removeAttribute({{$index}})" type="button" class="rounded-md bg-red-50 p-2 text-red-500 hover:bg-red-100">&times;</button>
                            </div>
                            @endforeach
                        </div>
                        <div class="mt-4 flex gap-x-3">
                            <button wire:click.prevent="addAttribute" type="button" class="rounded-md bg-white px-2.5 py-1.5 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">Ajouter un attribut</button>
                            <button wire:click.prevent="generateVariants" type="button" class="rounded-md bg-indigo-600 px-2.5 py-1.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500">Générer les variantes</button>
                        </div>

                        @if(!empty($variants))
                        <div class="mt-8 flow-root">
                            <h4 class="text-sm font-medium text-gray-700">Variantes générées :</h4>
                            <table class="min-w-full divide-y divide-gray-300 mt-2">
                                <thead><tr><th class="py-2 text-left ...">Variante</th><th class="py-2 text-left ...">SKU</th><th class="py-2 text-left ...">Prix de Vente</th></tr></thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($variants as $index => $variant)
                                    <tr wire:key="variant-{{$index}}">
                                        <td class="py-2 text-sm">{{ $variant['name'] }}</td>
                                        <td class="py-2"><input type="text" wire:model="variants.{{$index}}.sku" class="block w-full ..."></td>
                                        <td class="py-2"><input type="number" wire:model="variants.{{$index}}.selling_price" class="block w-full ..."></td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        @endif
                    </div>
                    <!-- Fin de la section pour les variantes (conditionnelle) -->
                </div>
                <div class="sm:col-span-full">
                    <label for="description" class="block text-sm font-medium leading-6 text-gray-900">Description</label>
                    <textarea wire:model="formData.description" id="description" rows="4" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600"></textarea>
                </div>
                <div class="sm:col-span-3">
                    <label for="selling_price" class="block text-sm font-medium leading-6 text-gray-900">Prix de vente (XAF)</label>
                    <input type="number" wire:model="formData.selling_price" id="selling_price" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                    @error('formData.selling_price') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                </div>
                <div class="sm:col-span-3">
                    <label for="purchase_price" class="block text-sm font-medium leading-6 text-gray-900">Prix d'achat (XAF)</label>
                    <input type="number" wire:model="formData.purchase_price" id="purchase_price" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                </div>
            </div>
        </div>

        <!-- Colonne de Droite : Organisation et Images -->
        <div class="grid grid-cols-1 gap-y-8">
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                <h3 class="text-base font-semibold leading-6 text-gray-900">Organisation</h3>
                 <div class="mt-6">
                    <label for="category_id" class="block text-sm font-medium leading-6 text-gray-900">Catégorie</label>
                    <select wire:model="formData.category_id" id="category_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        <option value="">Aucune</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mt-6">
                    <label for="tax_id" class="block text-sm font-medium leading-6 text-gray-900">Taxe</label>
                    <select wire:model="formData.tax_id" id="tax_id" class="mt-2 block w-full rounded-md border-0 py-1.5 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-indigo-600">
                        <option value="">Aucune</option>
                        @foreach($taxes as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name }} ({{$tax->rate}}%)</option>
                        @endforeach
                    </select>
                </div>
                 <div class="mt-6 relative flex items-start">
                    <div class="flex h-6 items-center"><input id="is_active" wire:model="formData.is_active" type="checkbox" class="h-4 w-4 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500"></div>
                    <div class="ml-3 text-sm leading-6"><label for="is_active" class="font-medium text-gray-900">Produit Actif</label></div>
                </div>
            </div>
            <!-- Carte des Images avec affichage des images existantes -->
            <div class="bg-white p-6 shadow-sm ring-1 ring-gray-900/5 sm:rounded-xl">
                 <h3 class="text-base font-semibold leading-6 text-gray-900">Images</h3>
                 
                 <!-- Affichage des images existantes -->
                 @if ($existingImages && count($existingImages) > 0)
                    <div class="mt-4">
                        <p class="text-sm font-medium text-gray-500 mb-2">Images actuelles :</p>
                        <div class="grid grid-cols-3 gap-4">
                            @foreach ($existingImages as $image)
                                <div class="relative group">
                                    <img src="{{ $image->getUrl() }}" class="rounded-md object-cover h-24 w-24">
                                    <div class="absolute inset-0 bg-black bg-opacity-50 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button 
                                            wire:click.prevent="removeImage({{ $image->id }})" 
                                            wire:confirm="Êtes-vous sûr de vouloir supprimer cette image ?"
                                            type="button" 
                                            class="p-1.5 bg-red-600 text-white rounded-full hover:bg-red-700"
                                        >
                                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" /></svg>
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                 @endif

                 <div class="mt-4">
                     <label for="newImages" class="block text-sm font-medium leading-6 text-gray-900">Ajouter de nouvelles images</label>
                     <input type="file" wire:model="newImages" multiple id="newImages" class="mt-2 block w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-violet-50 file:text-violet-700 hover:file:bg-violet-100"/>
                 </div>
                 <div wire:loading wire:target="newImages" class="mt-2 text-sm text-gray-500">Chargement...</div>
                 @if ($newImages)
                    <div class="mt-4 grid grid-cols-3 gap-4">
                        @foreach ($newImages as $image)
                            @if (str_starts_with($image->getMimeType(), 'image/'))
                                <img src="{{ $image->temporaryUrl() }}" class="rounded-md object-cover h-24 w-24">
                            @endif
                        @endforeach
                    </div>
                 @endif
            </div>
        </div>
    </div>
</form>
