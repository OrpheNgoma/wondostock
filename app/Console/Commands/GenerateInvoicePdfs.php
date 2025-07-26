<?php

namespace App\Console\Commands;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Illuminate\Console\Command;

class GenerateInvoicePdfs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'invoice:generate-pdfs
                            {--company= : ID de l\'entreprise pour filtrer les factures}
                            {--month= : Mois pour filtrer les factures (YYYY-MM)}
                            {--all : Générer pour toutes les factures}
                            {--overwrite : Écraser les PDF existants}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Générer les PDF des factures en lot';

    /**
     * Execute the console command.
     */
    public function handle(InvoicePdfService $pdfService)
    {
        $this->info('Génération des PDF de factures...');

        // Construction de la requête
        $query = Invoice::with(['company', 'subscription', 'subscription.plan']);

        if ($this->option('company')) {
            $query->where('company_id', $this->option('company'));
        }

        if ($this->option('month')) {
            $month = $this->option('month');
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                $this->error('Format de mois invalide. Utilisez YYYY-MM');
                return 1;
            }
            
            $query->whereYear('issue_date', substr($month, 0, 4))
                  ->whereMonth('issue_date', substr($month, 5, 2));
        }

        if (!$this->option('all') && !$this->option('company') && !$this->option('month')) {
            // Par défaut, traiter les factures du mois en cours
            $query->whereYear('issue_date', now()->year)
                  ->whereMonth('issue_date', now()->month);
        }

        $invoices = $query->get();

        if ($invoices->isEmpty()) {
            $this->warn('Aucune facture trouvée avec les critères spécifiés.');
            return 0;
        }

        $this->info("Traitement de {$invoices->count()} facture(s)...");

        // Créer la barre de progression
        $bar = $this->output->createProgressBar($invoices->count());
        $bar->start();

        $results = [];
        $storageDir = storage_path('app/invoices');

        // Créer le dossier si nécessaire
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        foreach ($invoices as $invoice) {
            $fileName = "facture-{$invoice->invoice_number}.pdf";
            $filePath = $storageDir . '/' . $fileName;

            // Vérifier si le fichier existe déjà
            if (file_exists($filePath) && !$this->option('overwrite')) {
                $results[] = [
                    'invoice' => $invoice->invoice_number,
                    'status' => 'skipped',
                    'message' => 'Fichier existant'
                ];
                $bar->advance();
                continue;
            }

            try {
                // Valider la facture
                $errors = $pdfService->validateInvoice($invoice);
                
                if (!empty($errors)) {
                    $results[] = [
                        'invoice' => $invoice->invoice_number,
                        'status' => 'error',
                        'message' => implode(', ', $errors)
                    ];
                    $bar->advance();
                    continue;
                }

                // Générer le PDF
                $pdfService->savePdf($invoice, $filePath);
                
                $results[] = [
                    'invoice' => $invoice->invoice_number,
                    'status' => 'success',
                    'message' => $fileName
                ];

            } catch (\Exception $e) {
                $results[] = [
                    'invoice' => $invoice->invoice_number ?? 'N/A',
                    'status' => 'error',
                    'message' => $e->getMessage()
                ];
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        // Affichage des résultats
        $successCount = collect($results)->where('status', 'success')->count();
        $errorCount = collect($results)->where('status', 'error')->count();
        $skippedCount = collect($results)->where('status', 'skipped')->count();

        $this->table(
            ['Facture', 'Statut', 'Message'],
            collect($results)->map(function ($result) {
                return [
                    $result['invoice'],
                    $result['status'],
                    $result['message']
                ];
            })->toArray()
        );

        $this->info("✅ {$successCount} PDF(s) généré(s) avec succès");
        
        if ($skippedCount > 0) {
            $this->warn("⚠️  {$skippedCount} fichier(s) ignoré(s) (déjà existant)");
        }
        
        if ($errorCount > 0) {
            $this->error("❌ {$errorCount} erreur(s) rencontrée(s)");
        }

        $this->info("📁 Dossier de stockage : {$storageDir}");

        return $errorCount > 0 ? 1 : 0;
    }
}
