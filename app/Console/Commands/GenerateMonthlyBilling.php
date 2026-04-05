<?php

namespace App\Console\Commands;

use App\Services\BillingService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class GenerateMonthlyBilling extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:generate-monthly 
                            {--dry-run : Afficher ce qui sera fait sans l\'exécuter}
                            {--force : Forcer la génération même si des factures existent déjà}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer automatiquement les factures mensuelles pour tous les abonnements actifs';

    protected BillingService $billingService;

    /**
     * Create a new command instance.
     */
    public function __construct(BillingService $billingService)
    {
        parent::__construct();
        $this->billingService = $billingService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('🏗️  Démarrage de la génération des factures mensuelles...');
        $this->info('📅 Période: '.now()->format('F Y'));

        $isDryRun = $this->option('dry-run');
        $isForced = $this->option('force');

        if ($isDryRun) {
            $this->warn('⚠️  MODE TEST ACTIVÉ - Aucune facture ne sera réellement créée');
        }

        if ($isForced) {
            $this->warn('⚠️  MODE FORCÉ - Les factures existantes seront ignorées');
        }

        try {
            // Afficher un résumé avant l'exécution
            $this->showBillingSummary();

            // Traiter la facturation
            if ($isDryRun) {
                $results = $this->simulateBilling();
            } else {
                $results = $this->billingService->processMonthlyBilling();
            }

            // Afficher les résultats
            $this->displayResults($results);

            // Calculer et afficher les métriques
            $this->displayRevenueMetrics();

            $this->info('✅ Facturation mensuelle terminée avec succès !');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors de la facturation : '.$e->getMessage());
            Log::error('Erreur commande billing:generate-monthly', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Afficher un résumé avant l'exécution
     */
    private function showBillingSummary(): void
    {
        $subscriptions = \App\Models\Subscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->get();

        $totalSubscriptions = $subscriptions->count();
        $totalCompanies = $subscriptions->unique('company_id')->count();
        $estimatedRevenue = $subscriptions->sum(function ($sub) {
            return $sub->plan->price;
        }) / 100;

        $this->info('📊 RÉSUMÉ DE LA FACTURATION');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("📈 Abonnements actifs: {$totalSubscriptions}");
        $this->line("🏢 Entreprises concernées: {$totalCompanies}");
        $this->line('💰 Revenus estimés: '.number_format($estimatedRevenue, 0, ',', ' ').' FCFA');
        $this->line('');

        // Afficher le détail par plan
        $planBreakdown = $subscriptions->groupBy('plan.name')->map(function ($group) {
            return [
                'count' => $group->count(),
                'revenue' => $group->sum(function ($sub) {
                    return $sub->plan->price;
                }) / 100,
            ];
        });

        $this->info('📋 RÉPARTITION PAR PLAN');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━');
        foreach ($planBreakdown as $planName => $data) {
            $this->line("• {$planName}: {$data['count']} abonnements - ".
                      number_format($data['revenue'], 0, ',', ' ').' FCFA');
        }
        $this->line('');
    }

    /**
     * Simuler la facturation (mode dry-run)
     */
    private function simulateBilling(): array
    {
        $subscriptions = \App\Models\Subscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->get();

        $results = [
            'generated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        $this->info('🔍 Simulation de la facturation en cours...');

        $progressBar = $this->output->createProgressBar($subscriptions->count());
        $progressBar->start();

        foreach ($subscriptions as $subscription) {
            // Vérifier si une facture existe déjà
            $existingInvoice = \App\Models\Invoice::where('company_id', $subscription->company_id)
                ->where('subscription_id', $subscription->id)
                ->where('billing_period_start', now()->startOfMonth())
                ->exists();

            if (! $existingInvoice) {
                $results['generated']++;
                $this->line("  ✓ Facture créée pour {$subscription->company->name} - Plan {$subscription->plan->name}");
            } else {
                $this->line("  ⚠️  Facture existe déjà pour {$subscription->company->name}");
            }

            $progressBar->advance();
        }

        $progressBar->finish();
        $this->line('');

        return $results;
    }

    /**
     * Afficher les résultats de la facturation
     */
    private function displayResults(array $results): void
    {
        $this->info('📊 RÉSULTATS DE LA FACTURATION');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("✅ Factures générées: {$results['generated']}");
        $this->line("❌ Échecs: {$results['failed']}");

        if ($results['failed'] > 0 && ! empty($results['errors'])) {
            $this->warn("\n⚠️  ERREURS DÉTAILLÉES:");
            foreach ($results['errors'] as $error) {
                $this->error("• {$error['company_name']} (ID: {$error['subscription_id']}): {$error['error']}");
            }
        }

        $this->line('');
    }

    /**
     * Afficher les métriques de revenus
     */
    private function displayRevenueMetrics(): void
    {
        $metrics = $this->billingService->calculateRevenueMetrics();

        $this->info('💰 MÉTRIQUES DE REVENUS');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line('📈 MRR (Revenus Mensuels Récurrents): '.number_format($metrics['mrr'], 0, ',', ' ').' FCFA');
        $this->line('📊 ARR (Revenus Annuels Récurrents): '.number_format($metrics['arr'], 0, ',', ' ').' FCFA');
        $this->line('💵 Revenus ce mois: '.number_format($metrics['current_month_revenue'], 0, ',', ' ').' FCFA');
        $this->line('📉 Croissance mensuelle: '.number_format($metrics['monthly_growth'], 1).'%');
        $this->line('📋 Taux de conversion: '.number_format($metrics['conversion_rate'], 1).'%');
        $this->line("🧾 Factures: {$metrics['paid_invoices']}/{$metrics['total_invoices']} payées");
    }
}
