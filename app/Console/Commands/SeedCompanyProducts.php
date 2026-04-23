<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Models\Zone;
use App\Models\ZoneProductPrice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

// Exception interne utilisée uniquement pour forcer le rollback du dry-run.
// L'utilisation d'un type dédié évite de masquer accidentellement une vraie RuntimeException.
final class DryRunRollback extends \RuntimeException {}

class SeedCompanyProducts extends Command
{
    protected $signature = 'wondo:seed-products
                            {--email= : Email du propriétaire du tenant}
                            {--company= : ID de l\'entreprise}
                            {--dry-run : Affiche ce qui serait créé sans rien enregistrer}';

    protected $description = 'Enregistre le catalogue produits pour un tenant dépôt de boissons';

    /** @var array<int, array<string, mixed>> */
    private array $products = [
        ['sku' => 'REG',      'name' => 'Regab 60Cl × 12',       'col' => 'C12', 'purchase_price' => 5241, 'category' => 'Bières'],
        ['sku' => 'PT-REG',   'name' => 'Regab 33Cl × 33Cl',     'col' => 'C24', 'purchase_price' => 0,    'category' => 'Bières'],
        ['sku' => 'PT-CAST',  'name' => 'Castel 33Cl × 24',      'col' => 'C24', 'purchase_price' => 6198, 'category' => 'Bières'],
        ['sku' => 'PT-BFT',   'name' => 'Beaufort 33Cl × 24',    'col' => 'C24', 'purchase_price' => 6198, 'category' => 'Bières'],
        ['sku' => 'PT-SOMB',  'name' => 'Sombreros 33Cl × 24',   'col' => 'C24', 'purchase_price' => 6198, 'category' => 'Bières'],
        ['sku' => 'PT-XX',    'name' => 'Xxl 33Cl × 24',         'col' => 'C24', 'purchase_price' => 7854, 'category' => 'Bières'],
        ['sku' => 'NJIN60',   'name' => 'Ndjino 33Cl × 24',      'col' => 'C24', 'purchase_price' => 4341, 'category' => 'Soft Drinks'],
        ['sku' => 'PT-33EXP', 'name' => '"33" Export 33Cl × 24', 'col' => 'C24', 'purchase_price' => 6198, 'category' => 'Bières'],
        ['sku' => 'WD-COL',   'name' => 'World Cola 60Cl × 12',  'col' => 'C12', 'purchase_price' => 3927, 'category' => 'Soft Drinks'],
        ['sku' => 'BOOST',    'name' => 'Booster 33Cl × 24',     'col' => 'C24', 'purchase_price' => 9425, 'category' => 'Énergie'],
        ['sku' => 'PT-CHIL',  'name' => 'Chill 33Cl × 24',       'col' => 'C24', 'purchase_price' => 6198, 'category' => 'Bières'],
        ['sku' => 'PT-DOP',   'name' => 'Doppel 33Cl × 24',      'col' => 'C24', 'purchase_price' => 6198, 'category' => 'Bières'],
    ];

    /**
     * Prix de vente par zone. Marge = selling_price − purchase_price (calculée auto).
     *
     * @var array<string, array<string, int>>
     */
    private array $zonePrices = [
        'Moanda' => [
            'REG' => 6000, 'PT-REG' => 0, 'PT-CAST' => 7200, 'PT-BFT' => 7200,
            'PT-SOMB' => 7200, 'PT-XX' => 9500, 'NJIN60' => 5000, 'PT-33EXP' => 7200,
            'WD-COL' => 5000, 'BOOST' => 11000, 'PT-CHIL' => 7200, 'PT-DOP' => 7200,
        ],
        'Bakoumba' => [
            'REG' => 6300, 'PT-REG' => 0, 'PT-CAST' => 7500, 'PT-BFT' => 7500,
            'PT-SOMB' => 7500, 'PT-XX' => 11000, 'NJIN60' => 5500, 'PT-33EXP' => 7500,
            'WD-COL' => 5500, 'BOOST' => 11500, 'PT-CHIL' => 7500, 'PT-DOP' => 7500,
        ],
        'Dienga' => [
            'REG' => 6300, 'PT-REG' => 0, 'PT-CAST' => 7500, 'PT-BFT' => 7500,
            'PT-SOMB' => 7500, 'PT-XX' => 11000, 'NJIN60' => 5500, 'PT-33EXP' => 7500,
            'WD-COL' => 5500, 'BOOST' => 11500, 'PT-CHIL' => 7500, 'PT-DOP' => 7500,
        ],
    ];

    public function handle(): int
    {
        $company = $this->resolveCompany();
        if (! $company) {
            return self::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');

        $this->info("Tenant : {$company->name} (ID: {$company->id})");
        if ($isDryRun) {
            $this->warn('Mode dry-run — rien ne sera enregistré.');
        }
        $this->line('');

        try {
            DB::transaction(function () use ($company, $isDryRun): void {
                $this->seedCategories($company, $isDryRun);
                $this->seedProducts($company, $isDryRun);
                $this->seedZones($company, $isDryRun);
                $this->seedZonePrices($company, $isDryRun);

                if ($isDryRun) {
                    throw new DryRunRollback;
                }
            });
        } catch (DryRunRollback) {
            $this->line('');
            $this->warn('Dry-run terminé — aucune donnée enregistrée.');

            return self::SUCCESS;
        }

        $this->line('');
        $this->info('✓ Catalogue produits enregistré avec succès.');

        return self::SUCCESS;
    }

    private function resolveCompany(): ?Company
    {
        if ($email = $this->option('email')) {
            $user = \App\Models\User::where('email', $email)->first();
            if (! $user?->company_id) {
                $this->error("Aucun tenant trouvé pour l'email : {$email}");

                return null;
            }

            return Company::find($user->company_id);
        }

        if ($id = $this->option('company')) {
            $company = Company::find((int) $id);
            if (! $company) {
                $this->error("Aucune entreprise trouvée avec l'ID : {$id}");

                return null;
            }

            return $company;
        }

        $this->error('Spécifiez --email=xxx ou --company=ID');

        return null;
    }

    private function seedCategories(Company $company, bool $isDryRun): void
    {
        $this->line('<fg=cyan>Catégories :</>');

        foreach (['Bières', 'Soft Drinks', 'Énergie'] as $name) {
            if ($isDryRun) {
                $this->line("  + {$name}");

                continue;
            }

            // Le slug est scopé à l'entreprise pour éviter les conflits multi-tenant.
            $slug = \Illuminate\Support\Str::slug($name).'-'.$company->id;

            Category::firstOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                ['slug' => $slug, 'description' => null]
            );
            $this->line("  ✓ {$name}");
        }
    }

    private function seedProducts(Company $company, bool $isDryRun): void
    {
        $this->line('<fg=cyan>Produits :</>');

        $categories = Category::where('company_id', $company->id)
            ->whereIn('name', ['Bières', 'Soft Drinks', 'Énergie'])
            ->get()
            ->keyBy('name');

        foreach ($this->products as $data) {
            $categoryId = $categories->get($data['category'])?->id;
            $defaultSellingPrice = $this->zonePrices['Moanda'][$data['sku']] ?? 0;

            if ($isDryRun) {
                $this->line("  + [{$data['sku']}] {$data['name']} — achat: {$data['purchase_price']} FCFA / vente: {$defaultSellingPrice} FCFA");

                continue;
            }

            // SKU scopé à l'entreprise pour éviter les conflits multi-tenant.
            $sku = $data['sku'].'-'.$company->id;

            Product::updateOrCreate(
                ['company_id' => $company->id, 'sku' => $sku],
                [
                    'name' => $data['name'],
                    'type' => ProductType::Simple,
                    'purchase_price' => $data['purchase_price'],
                    'selling_price' => $defaultSellingPrice,
                    'category_id' => $categoryId,
                    'is_active' => true,
                    'description' => "Colisage : {$data['col']}",
                ]
            );
            $this->line("  ✓ [{$data['sku']}] {$data['name']}");
        }
    }

    private function seedZones(Company $company, bool $isDryRun): void
    {
        $this->line('<fg=cyan>Zones de livraison :</>');

        foreach (array_keys($this->zonePrices) as $zoneName) {
            if ($isDryRun) {
                $this->line("  + Zone : {$zoneName}");

                continue;
            }

            Zone::firstOrCreate(
                ['company_id' => $company->id, 'name' => $zoneName],
                ['city' => $zoneName, 'mission_allowance' => 5000]
            );
            $this->line("  ✓ {$zoneName}");
        }
    }

    private function seedZonePrices(Company $company, bool $isDryRun): void
    {
        $this->line('<fg=cyan>Prix par zone :</>');

        $zones = Zone::where('company_id', $company->id)
            ->whereIn('name', array_keys($this->zonePrices))
            ->get()
            ->keyBy('name');

        // Les SKU sont scopés au tenant (ex: REG-1), on reconstruit la liste.
        $scopedSkus = array_map(fn (array $p): string => $p['sku'].'-'.$company->id, $this->products);

        // Indexer par SKU original (sans le suffixe -companyId) pour correspondre aux clés de $zonePrices.
        $products = Product::where('company_id', $company->id)
            ->whereIn('sku', $scopedSkus)
            ->get()
            ->keyBy(fn ($p): string => str_replace('-'.$company->id, '', $p->sku));

        foreach ($this->zonePrices as $zoneName => $prices) {
            $zone = $zones->get($zoneName);
            if (! $zone) {
                continue;
            }

            $count = 0;
            foreach ($prices as $sku => $sellingPrice) {
                $product = $products->get($sku);
                if (! $product || $sellingPrice === 0) {
                    continue;
                }

                if ($isDryRun) {
                    $margin = $sellingPrice - ($product->purchase_price ?? 0);
                    $this->line("  + {$zoneName} / {$sku} → vente: {$sellingPrice} FCFA / marge auto: {$margin} FCFA");

                    continue;
                }

                ZoneProductPrice::updateOrCreate(
                    ['zone_id' => $zone->id, 'product_id' => $product->id],
                    [
                        'company_id' => $company->id,
                        'selling_price' => $sellingPrice,
                        'margin_override' => null,
                        'is_active' => true,
                    ]
                );
                $count++;
            }

            if (! $isDryRun) {
                $this->line("  ✓ {$zoneName} ({$count} produits configurés)");
            }
        }
    }
}
