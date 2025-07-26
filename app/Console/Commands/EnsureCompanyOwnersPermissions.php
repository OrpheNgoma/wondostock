<?php

namespace App\Console\Commands;

use App\Models\Company;
use App\Models\User;
use Illuminate\Console\Command;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class EnsureCompanyOwnersPermissions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'company:ensure-owners-permissions {--dry-run : Run without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Ensure all company owners have proper roles and permissions';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        
        if ($isDryRun) {
            $this->info('🔍 Mode DRY-RUN: Aucune modification ne sera apportée');
        }
        
        $this->info('📋 Vérification des permissions des propriétaires d\'entreprise...');
        $this->newLine();

        $companies = Company::whereNotNull('owner_id')->with('owner')->get();
        $allPermissions = Permission::all();
        
        $this->info("Entreprises trouvées: {$companies->count()}");
        $this->info("Permissions disponibles: {$allPermissions->count()}");
        $this->newLine();

        $processed = 0;
        $created = 0;
        $updated = 0;

        foreach ($companies as $company) {
            $this->info("🏢 Traitement de: {$company->name} (ID: {$company->id})");
            
            if (!$company->owner) {
                $this->warn("  ⚠️  Propriétaire introuvable (owner_id: {$company->owner_id})");
                continue;
            }

            $owner = $company->owner;
            $this->info("  👤 Propriétaire: {$owner->name} ({$owner->email})");

            // Définir le contexte d'équipe pour cette entreprise
            if (!$isDryRun) {
                setPermissionsTeamId($company->id);
            }

            // Générer le nom du rôle professionnel
            $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9\s]/', '', $company->name);
            $sanitizedCompanyName = preg_replace('/\s+/', '-', trim($sanitizedCompanyName));
            $roleName = 'Propriétaire-' . $sanitizedCompanyName;

            // Vérifier si le rôle existe déjà
            $ownerRole = Role::where('name', $roleName)
                ->where('company_id', $company->id)
                ->first();

            if (!$ownerRole) {
                $this->info("  ➕ Création du rôle: {$roleName}");
                
                if (!$isDryRun) {
                    $ownerRole = Role::create([
                        'name' => $roleName,
                        'guard_name' => 'web',
                        'company_id' => $company->id,
                    ]);
                    
                    // Assigner toutes les permissions
                    $ownerRole->syncPermissions($allPermissions);
                    $this->info("    ✅ Rôle créé avec {$allPermissions->count()} permissions");
                }
                $created++;
            } else {
                $this->info("  ✅ Rôle existant: {$roleName}");
                
                // Vérifier si toutes les permissions sont assignées
                $currentPermissions = $ownerRole->permissions->count();
                if ($currentPermissions < $allPermissions->count()) {
                    $this->info("    🔄 Mise à jour des permissions ({$currentPermissions} → {$allPermissions->count()})");
                    if (!$isDryRun) {
                        $ownerRole->syncPermissions($allPermissions);
                    }
                    $updated++;
                }
            }

            // Vérifier si le propriétaire a le rôle
            if (!$isDryRun) {
                $hasRole = $owner->hasRole($roleName);
                if (!$hasRole) {
                    $this->info("  🔗 Attribution du rôle au propriétaire");
                    $owner->assignRole($ownerRole);
                } else {
                    $this->info("  ✅ Propriétaire a déjà le rôle");
                }
            }

            $processed++;
            $this->newLine();
        }

        // Résumé
        $this->info('📊 RÉSUMÉ:');
        $this->info("  • Entreprises traitées: {$processed}");
        $this->info("  • Rôles créés: {$created}");
        $this->info("  • Rôles mis à jour: {$updated}");
        
        if ($isDryRun) {
            $this->newLine();
            $this->warn('⚠️  Mode DRY-RUN: Aucune modification n\'a été apportée');
            $this->info('Pour appliquer les changements, exécutez: php artisan company:ensure-owners-permissions');
        } else {
            $this->newLine();
            $this->info('✅ Traitement terminé avec succès!');
        }

        return 0;
    }
}
