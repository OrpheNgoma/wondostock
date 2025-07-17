<?php

namespace App\Livewire\Products;

use App\Models\Category;
use App\Models\Product;
use App\Models\Tax;
use App\Models\Unit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
class ProductForm extends Component
{
    use WithFileUploads;

    public Product $product;

    // Form properties - Optimisées pour éviter le re-rendu complet
    public array $formData = [
        'name' => '',
        'sku' => '',
        'description' => '',
        'selling_price' => '',
        'purchase_price' => '',
        'category_id' => null,
        'tax_id' => null,
        'unit_id' => null,
        'attributes_json' => '',
        'low_stock_threshold' => 10,
        'is_active' => true,
        'type' => 'simple',
    ];

    // Propriétés d'interface - uniquement ce qui doit être réactif
    public $newImages = [];

    public $existingImages = [];

    public array $product_attributes = [];

    public array $variants = [];

    // Méthode appelée quand de nouvelles images sont uploadées
    public function updatedNewImages()
    {
        $this->validate([
            'newImages.*' => 'image|max:1024', // 1MB max
        ]);
    }

    // On écoute un événement pour savoir quel produit charger
    // protected $listeners = ['loadProduct' => 'loadProduct'];

    public function mount(?Product $product = null)
    {
        try {
            $this->product = $product ?? new Product;

            // Remplir formData avec les données du produit
            if ($this->product->exists) {
                $this->formData = array_merge($this->formData, [
                    'name' => $this->product->name ?? '',
                    'sku' => $this->product->sku ?? '',
                    'description' => $this->product->description ?? '',
                    'selling_price' => $this->product->selling_price ?? '',
                    'purchase_price' => $this->product->purchase_price ?? '',
                    'category_id' => $this->product->category_id,
                    'tax_id' => $this->product->tax_id,
                    'unit_id' => $this->product->unit_id,
                    'is_active' => $this->product->is_active ?? true,
                    'type' => $this->product->type?->value ?? 'simple',
                ]);

                if ($this->product->type?->value === 'variable' && $this->product->attributes) {
                    $this->product_attributes = array_map(function ($key, $value) {
                        return ['name' => $key, 'values' => implode(', ', $value)];
                    }, array_keys($this->product->attributes), $this->product->attributes);
                }

                $this->variants = $this->product->variants->map(fn ($variant) => $variant->toArray())->toArray();
                $this->existingImages = $this->product->getMedia('images');
            }

            // Définir une unité par défaut si nécessaire
            if (! $this->formData['unit_id']) {
                $this->formData['unit_id'] = Unit::where('symbol', 'pce')->first()?->id;
            }
        } catch (\Exception $e) {
            Log::error('Erreur lors du chargement du produit: '.$e->getMessage());
            $this->dispatch('notify', message: 'Erreur lors du chargement du produit.', type: 'error');
        }
    }

    // --- Logique des Variantes ---
    public function addAttribute()
    {
        $this->product_attributes[] = ['name' => '', 'values' => ''];
    }

    public function removeAttribute($index)
    {
        unset($this->product_attributes[$index]);
        $this->product_attributes = array_values($this->product_attributes);
    }

    public function generateVariants()
    {
        $attributes = collect($this->product_attributes)
            ->filter(fn ($attr) => ! empty($attr['name']) && ! empty($attr['values']))
            ->mapWithKeys(fn ($attr) => [$attr['name'] => array_map('trim', explode(',', $attr['values']))]);

        if ($attributes->isEmpty()) {
            $this->dispatch('notify', message: 'Veuillez définir au moins un attribut et ses valeurs.', type: 'error');

            return;
        }

        $combinations = $this->getCombinations($attributes->toArray());

        $this->variants = [];
        foreach ($combinations as $combination) {
            $variantName = $this->formData['name'].' - '.implode(' / ', $combination);
            $variantSku = $this->formData['sku'].'-'.implode('-', array_map(fn ($val) => strtoupper(substr($val, 0, 3)), $combination));

            $this->variants[] = [
                'name' => $variantName,
                'sku' => $variantSku,
                'selling_price' => $this->formData['selling_price'],
                'purchase_price' => $this->formData['purchase_price'],
                'attributes' => $combination,
            ];
        }
    }

    private function getCombinations(array $arrays): array
    {
        $result = [[]];
        foreach ($arrays as $property => $property_values) {
            $tmp = [];
            foreach ($result as $result_item) {
                foreach ($property_values as $property_value) {
                    $tmp[] = array_merge($result_item, [$property => $property_value]);
                }
            }
            $result = $tmp;
        }

        return $result;
    }

    public function updatedFormDataSku($value)
    {
        // Nettoyer le SKU automatiquement : supprimer espaces, convertir en majuscules, remplacer caractères spéciaux
        $cleanSku = strtoupper(trim($value));
        $cleanSku = preg_replace('/[^A-Z0-9\-_]/', '', $cleanSku);

        if ($cleanSku !== $value) {
            $this->formData['sku'] = $cleanSku;
            $this->dispatch('notify', message: 'SKU nettoyé automatiquement (caractères spéciaux supprimés)', type: 'info');
        }
    }

    protected function rules()
    {
        return [
            'formData.name' => 'required|string|max:255',
            'formData.sku' => 'required|string|max:100|regex:/^[A-Z0-9\-_]+$/|unique:products,sku,'.$this->product->id,
            'formData.selling_price' => 'required|numeric|min:0',
            'formData.purchase_price' => 'nullable|numeric|min:0',
            'formData.category_id' => 'nullable|exists:categories,id',
            'formData.tax_id' => 'nullable|exists:taxes,id',
            'formData.unit_id' => 'required|exists:units,id',
            'newImages.*' => 'nullable|image|max:1024', // 1MB Max par image
        ];
    }

    public function save()
    {
        $this->validate();
        DB::transaction(function () {
            $this->product->fill([
                'name' => $this->formData['name'],
                'sku' => $this->formData['sku'],
                'description' => $this->formData['description'],
                'selling_price' => $this->formData['selling_price'],
                'purchase_price' => $this->formData['purchase_price'],
                'category_id' => $this->formData['category_id'],
                'tax_id' => $this->formData['tax_id'],
                'unit_id' => $this->formData['unit_id'],
                'is_active' => $this->formData['is_active'],
                'type' => $this->formData['type'],
                'low_stock_threshold' => $this->formData['low_stock_threshold'] ?? 10,
                'company_id' => Auth::user()->company_id,
            ]);

            if ($this->formData['type'] === 'variable') {
                $this->product->attributes = collect($this->product_attributes)
                    ->filter(fn ($attr) => ! empty($attr['name']) && ! empty($attr['values']))
                    ->mapWithKeys(fn ($attr) => [$attr['name'] => array_map('trim', explode(',', $attr['values']))])
                    ->toArray();
            }

            $this->product->save();

            // Sauvegarde des variantes
            if ($this->formData['type'] === 'variable') {
                // Supprimer les anciennes variantes pour recréer les nouvelles
                $this->product->variants()->delete();
                foreach ($this->variants as $variantData) {
                    $this->product->variants()->create([
                        'company_id' => Auth::user()->company_id,
                        'name' => $variantData['name'],
                        'sku' => $variantData['sku'],
                        'selling_price' => $variantData['selling_price'],
                        'purchase_price' => $variantData['purchase_price'],
                        'attributes' => $variantData['attributes'],
                        'type' => 'variant',
                        'unit_id' => $this->formData['unit_id'],
                    ]);
                }
            }

            // Gestion des nouvelles images
            if ($this->newImages) {
                foreach ($this->newImages as $image) {
                    try {
                        // Copier le fichier temporaire dans un endroit accessible
                        $tempPath = $image->getRealPath();
                        $fileName = time().'_'.$image->getClientOriginalName();
                        $destinationPath = storage_path('app/public/temp/'.$fileName);

                        if (file_exists($tempPath) && copy($tempPath, $destinationPath)) {
                            $this->product->addMedia($destinationPath)
                                ->usingName($image->getClientOriginalName())
                                ->toMediaCollection('images');

                            // Nettoyer le fichier temporaire
                            if (file_exists($destinationPath)) {
                                unlink($destinationPath);
                            }
                        }
                    } catch (\Exception $e) {
                        Log::error('Erreur lors de l\'ajout d\'image: '.$e->getMessage());
                        $this->dispatch('notify', message: 'Erreur lors de l\'ajout d\'une image.', type: 'error');
                    }
                }

                // Reset des nouvelles images et rafraîchir les existantes
                $this->newImages = [];
                $this->existingImages = $this->product->fresh()->getMedia('images');
            }
            $this->dispatch('notify', message: 'Produit sauvegardé.');
            $this->redirectRoute('products.index', navigate: true);
        });
    }

    // Méthode pour supprimer une image existante
    public function removeImage($mediaId)
    {
        $media = $this->product->getMedia('images')->find($mediaId);
        if ($media) {
            $media->delete();
        }
        // On rafraîchit la collection d'images
        $this->existingImages = $this->product->fresh()->getMedia('images');
        $this->dispatch('notify', message: 'Image supprimée.');
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;
        $categories = Category::where('company_id', $companyId)->get();
        $taxes = Tax::where('company_id', $companyId)->get();
        $units = Unit::all(); // Les unités sont globales

        return view('livewire.products.product-form', [
            'categories' => $categories, 'taxes' => $taxes, 'units' => $units,
        ]);
    }
}
