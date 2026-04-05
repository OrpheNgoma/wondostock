<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\BillingService;
use App\Services\PaymentService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendPaymentReminders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'billing:send-reminders 
                            {--dry-run : Afficher ce qui sera fait sans l\'exécuter}
                            {--days=7 : Nombre de jours de retard pour envoyer une relance}
                            {--suspend-after=30 : Suspendre après X jours de retard}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoyer des relances pour les factures en retard et suspendre les abonnements si nécessaire';

    protected PaymentService $paymentService;

    protected BillingService $billingService;

    /**
     * Create a new command instance.
     */
    public function __construct(PaymentService $paymentService, BillingService $billingService)
    {
        parent::__construct();
        $this->paymentService = $paymentService;
        $this->billingService = $billingService;
    }

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('📬 Démarrage de l\'envoi des relances de paiement...');
        $this->info('📅 Date d\'exécution: '.now()->format('d/m/Y H:i'));

        $isDryRun = $this->option('dry-run');
        $reminderDays = (int) $this->option('days');
        $suspendAfterDays = (int) $this->option('suspend-after');

        if ($isDryRun) {
            $this->warn('⚠️  MODE TEST ACTIVÉ - Aucune action ne sera réellement effectuée');
        }

        $this->info('⏰ Configuration:');
        $this->line("   • Relances après {$reminderDays} jours de retard");
        $this->line("   • Suspension après {$suspendAfterDays} jours de retard");
        $this->line('');

        try {
            // Étape 1: Analyser la situation
            $analysis = $this->analyzeOverdueInvoices($reminderDays, $suspendAfterDays);
            $this->displayAnalysis($analysis);

            if (! $isDryRun && ($analysis['reminders_to_send'] > 0 || $analysis['subscriptions_to_suspend'] > 0)) {
                if (! $this->confirm('Voulez-vous continuer avec l\'envoi des relances et suspensions ?')) {
                    $this->info('Opération annulée par l\'utilisateur.');

                    return Command::SUCCESS;
                }
            }

            // Étape 2: Traitement
            $results = [
                'reminders' => ['sent' => 0, 'failed' => 0],
                'suspensions' => ['processed' => 0, 'failed' => 0],
                'errors' => [],
            ];

            // Envoyer les relances
            if ($analysis['reminders_to_send'] > 0) {
                $reminderResults = $isDryRun
                    ? $this->simulateReminders($analysis['overdue_invoices'])
                    : $this->sendReminders($analysis['overdue_invoices']);

                $results['reminders'] = $reminderResults;
            }

            // Traiter les suspensions
            if ($analysis['subscriptions_to_suspend'] > 0) {
                $suspensionResults = $isDryRun
                    ? $this->simulateSuspensions($analysis['critical_subscriptions'])
                    : $this->processSuspensions($analysis['critical_subscriptions']);

                $results['suspensions'] = $suspensionResults;
            }

            // Afficher les résultats
            $this->displayResults($results, $isDryRun);

            $this->info('✅ Traitement des relances terminé avec succès !');

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('❌ Erreur lors du traitement des relances : '.$e->getMessage());
            Log::error('Erreur commande billing:send-reminders', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return Command::FAILURE;
        }
    }

    /**
     * Analyser les factures en retard
     */
    private function analyzeOverdueInvoices(int $reminderDays, int $suspendAfterDays): array
    {
        $overdueInvoices = Invoice::with(['company', 'subscription'])
            ->whereIn('status', ['sent', 'overdue'])
            ->where('due_date', '<', now())
            ->get();

        $reminderThreshold = now()->subDays($reminderDays);
        $suspensionThreshold = now()->subDays($suspendAfterDays);

        $invoicesForReminder = $overdueInvoices->filter(function ($invoice) use ($reminderThreshold) {
            return $invoice->due_date <= $reminderThreshold;
        });

        $criticalSubscriptions = $overdueInvoices->filter(function ($invoice) use ($suspensionThreshold) {
            return $invoice->due_date <= $suspensionThreshold &&
                   $invoice->subscription &&
                   $invoice->subscription->status === 'active';
        })->pluck('subscription')->unique('id');

        return [
            'total_overdue' => $overdueInvoices->count(),
            'overdue_invoices' => $invoicesForReminder,
            'reminders_to_send' => $invoicesForReminder->count(),
            'critical_subscriptions' => $criticalSubscriptions,
            'subscriptions_to_suspend' => $criticalSubscriptions->count(),
            'total_overdue_amount' => $overdueInvoices->sum('total_amount') / 100,
        ];
    }

    /**
     * Afficher l'analyse de la situation
     */
    private function displayAnalysis(array $analysis): void
    {
        $this->info('📊 ANALYSE DES FACTURES EN RETARD');
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');
        $this->line("📋 Total factures en retard: {$analysis['total_overdue']}");
        $this->line("📬 Relances à envoyer: {$analysis['reminders_to_send']}");
        $this->line("⛔ Abonnements à suspendre: {$analysis['subscriptions_to_suspend']}");
        $this->line('💰 Montant total en retard: '.number_format($analysis['total_overdue_amount'], 0, ',', ' ').' FCFA');
        $this->line('');

        if ($analysis['reminders_to_send'] > 0) {
            $this->warn('📮 FACTURES NÉCESSITANT UNE RELANCE:');
            foreach ($analysis['overdue_invoices']->take(10) as $invoice) {
                $daysPastDue = now()->diffInDays($invoice->due_date);
                $this->line("   • {$invoice->invoice_number} - {$invoice->company->name} - ".
                          number_format($invoice->total_amount / 100, 0, ',', ' ').' FCFA '.
                          "({$daysPastDue} jours de retard)");
            }
            if ($analysis['overdue_invoices']->count() > 10) {
                $remaining = $analysis['overdue_invoices']->count() - 10;
                $this->line("   ... et {$remaining} autres factures");
            }
            $this->line('');
        }

        if ($analysis['subscriptions_to_suspend'] > 0) {
            $this->error('🚨 ABONNEMENTS À SUSPENDRE:');
            foreach ($analysis['critical_subscriptions']->take(10) as $subscription) {
                $this->line("   • {$subscription->company->name} - Plan {$subscription->plan->name}");
            }
            if ($analysis['critical_subscriptions']->count() > 10) {
                $remaining = $analysis['critical_subscriptions']->count() - 10;
                $this->line("   ... et {$remaining} autres abonnements");
            }
            $this->line('');
        }
    }

    /**
     * Simuler l'envoi des relances
     */
    private function simulateReminders($overdueInvoices): array
    {
        $this->info('📬 Simulation de l\'envoi des relances...');

        $results = ['sent' => 0, 'failed' => 0];

        foreach ($overdueInvoices as $invoice) {
            $this->line("   ✓ Relance simulée pour {$invoice->invoice_number} - {$invoice->company->name}");
            $results['sent']++;
        }

        return $results;
    }

    /**
     * Envoyer les relances réelles
     */
    private function sendReminders($overdueInvoices): array
    {
        $this->info('📬 Envoi des relances en cours...');

        $results = $this->paymentService->sendPaymentReminders();

        return $results;
    }

    /**
     * Simuler les suspensions
     */
    private function simulateSuspensions($criticalSubscriptions): array
    {
        $this->info('⛔ Simulation des suspensions...');

        $results = ['processed' => 0, 'failed' => 0];

        foreach ($criticalSubscriptions as $subscription) {
            $this->line("   ⚠️  Suspension simulée pour {$subscription->company->name}");
            $results['processed']++;
        }

        return $results;
    }

    /**
     * Traiter les suspensions réelles
     */
    private function processSuspensions($criticalSubscriptions): array
    {
        $this->info('⛔ Traitement des suspensions...');

        $results = ['processed' => 0, 'failed' => 0];

        foreach ($criticalSubscriptions as $subscription) {
            try {
                if ($this->billingService->suspendSubscriptionForNonPayment($subscription)) {
                    $this->line("   ✓ Abonnement suspendu: {$subscription->company->name}");
                    $results['processed']++;
                } else {
                    $this->line("   ⚠️  Aucune action nécessaire pour: {$subscription->company->name}");
                }
            } catch (\Exception $e) {
                $this->error("   ❌ Erreur suspension {$subscription->company->name}: {$e->getMessage()}");
                $results['failed']++;
            }
        }

        return $results;
    }

    /**
     * Afficher les résultats du traitement
     */
    private function displayResults(array $results, bool $isDryRun): void
    {
        $mode = $isDryRun ? ' (SIMULATION)' : '';

        $this->info("📊 RÉSULTATS DU TRAITEMENT{$mode}");
        $this->info('━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━');

        // Résultats des relances
        $this->line('📬 RELANCES:');
        $this->line("   ✅ Envoyées: {$results['reminders']['sent']}");
        $this->line("   ❌ Échecs: {$results['reminders']['failed']}");

        // Résultats des suspensions
        $this->line('⛔ SUSPENSIONS:');
        $this->line("   ✅ Traitées: {$results['suspensions']['processed']}");
        $this->line("   ❌ Échecs: {$results['suspensions']['failed']}");

        if (! empty($results['errors'])) {
            $this->warn("\n⚠️  ERREURS DÉTAILLÉES:");
            foreach ($results['errors'] as $error) {
                $this->error("   • {$error}");
            }
        }

        $this->line('');
    }
}
