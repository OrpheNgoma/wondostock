<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Tax;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // On récupère la première compagnie pour lui assigner les produits
        $company = Company::first();
        if (!$company) {
            $this->command->error('Aucune compagnie trouvée. Veuillez d\'abord lancer le CompanySeeder.');
            return;
        }

        // 1. Créer les Catégories et les Taxes
        $categories = Category::factory(5)->create(['company_id' => $company->id]);
        Tax::factory()->create(['company_id' => $company->id]);
        
        // 2. Créer des Produits et leur assigner un stock dans les magasins
        $stores = $company->stores;
        if($stores->isEmpty()) {
             $this->command->warn('Aucun magasin trouvé pour la compagnie. Les produits n\'auront pas de stock initial.');
             return;
        }

        Product::factory(50)->create([
            'company_id' => $company->id,
            'category_id' => $categories->random()->id,
        ])->each(function ($product) use ($stores) {
            // Attacher le produit à chaque magasin avec une quantité de stock aléatoire
            foreach($stores as $store) {
                 $product->stores()->attach($store->id, ['quantity' => rand(5, 100), 'low_stock_threshold' => 10]);
            }
        });
    }
}
