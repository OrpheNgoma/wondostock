<?php

namespace App\Console\Commands;

use App\Services\FeatureLockService;
use Illuminate\Console\Command;

class CleanupExpiredFeatureLocks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'feature-locks:cleanup
                            {--dry-run : Afficher les verrouillages qui seraient supprimés sans les supprimer}
                            {--force : Forcer le nettoyage sans confirmation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie automatiquement les verrouillages de fonctionnalités expirés';

    /**
     * Execute the console command.
     */
    public function handle(FeatureLockService $featureLockService): int
    {
        $this->info('🧹 Nettoyage des verrouillages de fonctionnalités expirés...');

        // Mode dry-run pour prévisualiser
        if ($this->option('dry-run')) {
            return $this->handleDryRun($featureLockService);
        }

        // Demander confirmation si pas de --force
        if (! $this->option('force')) {
            if (! $this->confirm('Êtes-vous sûr de vouloir nettoyer les verrouillages expirés ?')) {
                $this->info('❌ Opération annulée.');

                return Command::FAILURE;
            }
        }

        // Effectuer le nettoyage
        $cleanedCount = $featureLockService->cleanupExpiredLocks();

        if ($cleanedCount > 0) {
            $this->info("✅ {$cleanedCount} verrouillage(s) expiré(s) nettoyé(s) avec succès.");
        } else {
            $this->info('ℹ️  Aucun verrouillage expiré trouvé.');
        }

        // Vider le cache après nettoyage
        $featureLockService->clearAllCache();
        $this->info('🗑️  Cache des verrouillages vidé.');

        return Command::SUCCESS;
    }

    /**
     * Gère le mode dry-run.
     */
    private function handleDryRun(FeatureLockService $featureLockService): int
    {
        $this->info('🔍 Mode aperçu (dry-run) - Aucune modification ne sera effectuée');

        // Récupérer les verrouillages expirés sans les supprimer
        $expiredLocks = \App\Models\FeatureLock::expired()->with(['company', 'lockedBy'])->get();

        if ($expiredLocks->isEmpty()) {
            $this->info('ℹ️  Aucun verrouillage expiré trouvé.');

            return Command::SUCCESS;
        }

        $this->info("📋 {$expiredLocks->count()} verrouillage(s) expiré(s) trouvé(s) :");

        $headers = ['Entreprise', 'Fonctionnalité', 'Verrouillé par', 'Expiré le'];
        $rows = [];

        foreach ($expiredLocks as $lock) {
            $rows[] = [
                $lock->company->name,
                $lock->getFeatureDescription(),
                $lock->lockedBy?->name ?? 'Système',
                $lock->expires_at?->format('d/m/Y H:i') ?? 'N/A',
            ];
        }

        $this->table($headers, $rows);

        $this->warn('⚠️  Utilisez la commande sans --dry-run pour effectuer le nettoyage.');

        return Command::SUCCESS;
    }
}
