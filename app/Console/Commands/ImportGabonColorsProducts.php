<?php

namespace App\Console\Commands;

use App\Enums\ProductType;
use App\Models\Category;
use App\Models\Company;
use App\Models\Product;
use App\Scopes\CompanyScope;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

// Exception interne utilisée uniquement pour forcer le rollback du dry-run.
final class DryRunAbort extends \RuntimeException {}

class ImportGabonColorsProducts extends Command
{
    protected $signature = 'gabon-colors:import-products
                            {--email= : Email du propriétaire du tenant Gabon Colors}
                            {--company= : ID de l\'entreprise Gabon Colors}
                            {--dry-run : Affiche ce qui serait créé sans rien enregistrer}';

    protected $description = 'Importe le catalogue produits de Gabon Colors (peintures, résines, rouleaux, moules)';

    /**
     * Catalogue complet des produits.
     * Structure : ['name' => string, 'category' => string, 'selling_price' => int]
     *
     * @var array<int, array{name: string, category: string, selling_price: int}>
     */
    private array $products = [
        // ─── Moules & Accessoires Epoxy ───────────────────────────────────────
        ['name' => 'Moule cœur petit',           'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Moule cœur moyen',            'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Moule cœur grand',            'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 10000],
        ['name' => 'Moule rond',                  'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 10000],
        ['name' => 'Moule alphabétique petit',    'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Moule alphabétique grand',    'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 10000],
        ['name' => 'Moule Chiffre',               'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Moule dessin moyen',          'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Moule dessin grand',          'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 10000],
        ['name' => 'Moule échantillon',           'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Paillettes grands',           'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 10000],
        ['name' => 'Paillettes petits',           'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Paillettes en sachet',        'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 5000],
        ['name' => 'Vernis marin',                'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 25000],
        ['name' => 'Résine epoxy',                'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 20000],
        ['name' => 'Durcisseurs',                 'category' => 'Moules & Accessoires Epoxy', 'selling_price' => 30000],

        // ─── Rouleaux & Outillage Epoxy ───────────────────────────────────────
        ['name' => 'Rouleau 2203TS N°8',          'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Rouleau EG4110TH',            'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Rouleau rouge EG710',         'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 12000],
        ['name' => 'Rouleau rouge EG711',         'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 12000],
        ['name' => 'Rouleau EG722',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Rouleau EG724',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Rouleau EG729',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Rouleau EG730T',              'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'MS18A',                       'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 10000],
        ['name' => 'Papier collant dorée',        'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 10000],
        ['name' => 'Paquet de feuilles dorées (100)', 'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 5000],
        ['name' => 'Feuille dorée +18 tube colle', 'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 5000],
        ['name' => 'Feuille dorée',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 5000],
        ['name' => 'Masque respiratoire',         'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Trousse d\'outillage Epoxy',  'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Paquet de baguettes Epoxy',   'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 5000],
        ['name' => 'Porte-clefs moyen doré',      'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Porte-clefs petit doré',      'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 10000],
        ['name' => 'Porte-clefs petit argenté',   'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 10000],
        ['name' => 'Fer à souder',                'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 15000],
        ['name' => 'Paquet de vices porte-clef',  'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 10000],
        ['name' => 'Papier à poncer mural',       'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 10000],
        ['name' => 'Pochoir 50×50',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 12000],
        ['name' => 'Pochoir 60×60',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 12000],
        ['name' => 'Pochoir 40×60',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 12000],
        ['name' => 'Pochoir 80×80',               'category' => 'Rouleaux & Outillage Epoxy', 'selling_price' => 20000],

        // ─── Peintures Décoratives ─────────────────────────────────────────────
        ['name' => 'Pitura Difondo Deco 1L',      'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Pitura Difondo Deco 2L',      'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Pitura Difondo Deco 2.5L',    'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Peinture Murale Deco 1.5L',   'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Style Metal 1.5L',            'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Perla Efecto 1.5L',           'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Vento Di Sabia 1.5L',         'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Vento Di Sabia 3L',           'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Decko Lab 750ml',             'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Relooking 0.5L',              'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Bombe Locky',                 'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Peinture Carrosserie',        'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Peinture Deco Trix',          'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => "Or' Silv 900ml",              'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => "Or' Silv 100ml",              'category' => 'Peintures Décoratives', 'selling_price' => 0],
        ['name' => 'Moquettes',                   'category' => 'Peintures Décoratives', 'selling_price' => 0],

        // ─── Peintures & Matériaux ─────────────────────────────────────────────
        ['name' => 'Peinture routière rouge 20KG',          'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Antirouille rouge 4KG',                 'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Antirouille gris 4KG',                  'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Diluant synthétique 4L',                'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel jaune Cater 200ml',  'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel noir 200ml',         'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel orange 200ml',       'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel rouge oxyde 200ml',  'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel vert 200ml',         'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel bleu 200ml',         'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel jaune citron 200ml', 'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colorant universel jaune oxyde 200ml',  'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Colle à bois 4KG',                      'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Papier collant',                        'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Papier à poncer',                       'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Coude PVC 100/87',                      'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Coude PVC 32/87-40/87-45/03',           'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Coude PVC 45/100-75/100',               'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Réducteurs toutes mesures',             'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Coude PVC 75/45-03/45',                 'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Peinture paillette 22ml paquet',        'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Particule brillant cristal Tollens',    'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Pinceaux',                              'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Papier peint grand 16/1.5',             'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Papier peint petit 10/0.5',             'category' => 'Peintures & Matériaux', 'selling_price' => 0],
        ['name' => 'Optalin colle papiers peints',          'category' => 'Peintures & Matériaux', 'selling_price' => 0],

        // ─── Gamme Gabon Colors ────────────────────────────────────────────────
        ['name' => 'Royale Glycéro Brillant 4KG',       'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Glycéro Brillant 20KG',      'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Glycéro Base C 20KG',        'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Glycéro Mate 25KG',          'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Satinée 25KG',               'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Prestige Satiné 20KG',              'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Vinylique Extérieur 30KG',   'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Colonyl 600 Extra Blanc 30KG',      'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Vinylique Intérieur 30KG',   'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Royale Vinylique Base C 25KG',      'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Champion Intérieur 30KG',           'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Champion Extérieur 30KG',           'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Champion Extérieur 5KG',            'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Decolux Blanc 30KG',                'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Fixoprim 25KG',                     'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Enduit Star 30KG',                  'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Enduit Pate P77 30KG',              'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Enduit Pate Blanc 25KG',            'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Enduflex 5KG',                      'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Isoplast Blanc 25KG',               'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Coloflex Blanc 20KG',               'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Coloflex Vert 25KG',                'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Isoplast Base C 25KG',              'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Granugrès 30KG',                    'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Granito N°5 25KG',                  'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
        ['name' => 'Peinture routière jaune 20KG',      'category' => 'Gamme Gabon Colors', 'selling_price' => 0],
    ];

    public function handle(): int
    {
        $company = $this->resolveCompany();
        if (! $company) {
            return self::FAILURE;
        }

        $isDryRun = (bool) $this->option('dry-run');

        $this->info("Tenant : {$company->name} (ID: {$company->id})");
        $totalProducts = count($this->products);
        $this->info("Produits à importer : {$totalProducts}");

        if ($isDryRun) {
            $this->warn('Mode dry-run — aucune donnée ne sera enregistrée.');
        }

        $this->newLine();

        try {
            DB::transaction(function () use ($company, $isDryRun): void {
                $this->importCategories($company, $isDryRun);
                $this->importProducts($company, $isDryRun);

                if ($isDryRun) {
                    throw new DryRunAbort;
                }
            });
        } catch (DryRunAbort) {
            $this->newLine();
            $this->warn('Dry-run terminé — aucune donnée enregistrée.');

            return self::SUCCESS;
        }

        $this->newLine();
        $this->info('✓ Catalogue Gabon Colors importé avec succès.');

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

        // Recherche automatique par nom si aucune option fournie
        $company = Company::where('name', 'LIKE', '%Gabon%Colors%')
            ->orWhere('name', 'LIKE', '%GabonColors%')
            ->first();

        if (! $company) {
            $this->error('Entreprise Gabon Colors introuvable. Utilisez --email=xxx ou --company=ID.');

            return null;
        }

        $this->line("Entreprise trouvée automatiquement : {$company->name} (ID: {$company->id})");

        return $company;
    }

    private function importCategories(Company $company, bool $isDryRun): void
    {
        $categoryNames = collect($this->products)->pluck('category')->unique()->sort()->values();

        $this->line('<fg=cyan>Catégories :</>');

        foreach ($categoryNames as $name) {
            if ($isDryRun) {
                $exists = Category::withoutGlobalScopes()->where('company_id', $company->id)->where('name', $name)->exists();
                $status = $exists ? '(existe déjà)' : '(nouvelle)';
                $this->line("  · {$name} {$status}");

                continue;
            }

            $slug = Str::slug($name).'-'.$company->id;

            Category::withoutGlobalScopes()->firstOrCreate(
                ['company_id' => $company->id, 'name' => $name],
                ['slug' => $slug, 'description' => null]
            );

            $this->line("  ✓ {$name}");
        }
    }

    private function importProducts(Company $company, bool $isDryRun): void
    {
        $this->line('<fg=cyan>Produits :</>');

        // Charger catégories et produits existants en deux requêtes seulement (évite le N+1)
        $categoryNames = collect($this->products)->pluck('category')->unique()->toArray();

        $categories = Category::withoutGlobalScopes()
            ->where('company_id', $company->id)
            ->whereIn('name', $categoryNames)
            ->get()
            ->keyBy('name');

        // withoutGlobalScope(CompanyScope::class) — préserve SoftDeletingScope intentionnellement :
        // un produit soft-deleted ne doit pas bloquer la recréation.
        $existingNames = Product::withoutGlobalScope(CompanyScope::class)
            ->where('company_id', $company->id)
            ->pluck('name')
            ->flip();

        $created = 0;
        $skipped = 0;

        foreach ($this->products as $data) {
            $categoryId = $categories->get($data['category'])?->id;
            $priceLabel = $data['selling_price'] > 0
                ? number_format($data['selling_price'], 0, '.', ' ').' FCFA'
                : 'prix à définir';

            $alreadyExists = $existingNames->has($data['name']);

            if ($isDryRun) {
                $status = $alreadyExists ? '<fg=yellow>(existe déjà)</>' : '<fg=green>(nouveau)</>';
                $this->line("  · {$data['name']} — {$priceLabel} {$status}");

                continue;
            }

            if ($alreadyExists) {
                $this->line("  ~ {$data['name']} — ignoré (existe déjà)");
                $skipped++;

                continue;
            }

            Product::withoutGlobalScope(CompanyScope::class)->create([
                'company_id' => $company->id,
                'name' => $data['name'],
                'type' => ProductType::Simple,
                'category_id' => $categoryId,
                'selling_price' => $data['selling_price'],
                'purchase_price' => 0,
                'is_active' => true,
            ]);

            $this->line("  ✓ {$data['name']} — {$priceLabel}");
            $created++;
        }

        if (! $isDryRun) {
            $this->newLine();
            $this->info("Résumé : {$created} produit(s) créé(s), {$skipped} ignoré(s) (déjà existant(s)).");
        }
    }
}
