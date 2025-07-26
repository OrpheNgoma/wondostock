<?php

namespace App\Console\Commands;

use App\Services\InvoiceService;
use Illuminate\Console\Command;

class GenerateMonthlyInvoices extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoices:generate-monthly {--send : Envoyer automatiquement les factures par email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Génère les factures mensuelles pour tous les abonnements actifs';

    /**
     * Execute the console command.
     */
    public function handle(InvoiceService $invoiceService)
    {
        $this->info('Génération des factures mensuelles...');

        try {
            // Générer les factures
            $invoices = $invoiceService->generateMonthlyInvoices();
            
            if (empty($invoices)) {
                $this->info('Aucune nouvelle facture à générer.');
                return;
            }

            $this->info(count($invoices) . ' facture(s) générée(s) avec succès.');

            // Afficher les détails des factures générées
            $this->table(
                ['Numéro', 'Entreprise', 'Plan', 'Montant', 'Date d\'échéance'],
                collect($invoices)->map(function ($invoice) {
                    return [
                        $invoice->invoice_number,
                        $invoice->company->name,
                        $invoice->subscription->plan->name,
                        number_format($invoice->total_amount, 2) . ' €',
                        $invoice->due_date->format('d/m/Y'),
                    ];
                })
            );

            // Envoyer les factures par email si demandé
            if ($this->option('send')) {
                $this->info('Envoi des factures par email...');
                $sent = 0;
                
                foreach ($invoices as $invoice) {
                    if ($invoiceService->sendInvoice($invoice)) {
                        $sent++;
                    }
                }
                
                $this->info("{$sent} facture(s) envoyée(s) par email.");
            }

            // Vérifier les factures en retard
            $overdueInvoices = $invoiceService->checkOverdueInvoices();
            if (!empty($overdueInvoices)) {
                $this->warn(count($overdueInvoices) . ' facture(s) en retard détectée(s).');
            }

        } catch (\Exception $e) {
            $this->error('Erreur lors de la génération des factures: ' . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
