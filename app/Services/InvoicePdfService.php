<?php

namespace App\Services;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

/**
 * Service pour gérer la génération de PDF pour les factures.
 */
class InvoicePdfService
{
    /**
     * Générer un PDF pour une facture.
     */
    public function generatePdf(Invoice $invoice): \Barryvdh\DomPDF\PDF
    {
        // Charger les relations nécessaires
        $invoice->load(['company', 'subscription', 'subscription.plan', 'paymentNotifications']);

        $pdf = PDF::loadView('pdfs.invoice', compact('invoice'));

        // Configuration du PDF
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'isFontSubsettingEnabled' => true,
            'dpi' => 96,
            'fontHeightRatio' => 1.1,
        ]);

        return $pdf;
    }

    /**
     * Télécharger le PDF d'une facture.
     */
    public function downloadPdf(Invoice $invoice): Response
    {
        $pdf = $this->generatePdf($invoice);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->output();
        }, $this->getFileName($invoice), [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$this->getFileName($invoice).'"',
        ]);
    }

    /**
     * Afficher le PDF dans le navigateur.
     */
    public function streamPdf(Invoice $invoice): Response
    {
        $pdf = $this->generatePdf($invoice);

        return response($pdf->output(), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="'.$this->getFileName($invoice).'"',
        ]);
    }

    /**
     * Sauvegarder le PDF sur le système de fichiers.
     */
    public function savePdf(Invoice $invoice, ?string $path = null): string
    {
        $pdf = $this->generatePdf($invoice);

        if (! $path) {
            $path = storage_path('app/invoices/'.$this->getFileName($invoice));
        }

        // Créer le dossier si nécessaire
        $directory = dirname($path);
        if (! is_dir($directory)) {
            mkdir($directory, 0755, true);
        }

        $pdf->save($path);

        return $path;
    }

    /**
     * Obtenir le nom de fichier pour la facture.
     */
    protected function getFileName(Invoice $invoice): string
    {
        return "facture-{$invoice->invoice_number}.pdf";
    }

    /**
     * Générer un aperçu HTML de la facture (pour débogage).
     */
    public function getHtmlPreview(Invoice $invoice): string
    {
        $invoice->load(['company', 'subscription', 'subscription.plan', 'paymentNotifications']);

        return view('pdfs.invoice', compact('invoice'))->render();
    }

    /**
     * Valider qu'une facture peut être convertie en PDF.
     */
    public function validateInvoice(Invoice $invoice): array
    {
        $errors = [];

        if (! $invoice->company) {
            $errors[] = 'La facture doit être associée à une entreprise';
        }

        if (! $invoice->invoice_number) {
            $errors[] = 'La facture doit avoir un numéro';
        }

        if (! $invoice->amount || $invoice->amount <= 0) {
            $errors[] = 'La facture doit avoir un montant valide';
        }

        if (! $invoice->issue_date) {
            $errors[] = 'La facture doit avoir une date d\'émission';
        }

        if (! $invoice->due_date) {
            $errors[] = 'La facture doit avoir une date d\'échéance';
        }

        return $errors;
    }

    /**
     * Générer un lot de PDF pour plusieurs factures.
     */
    public function generateBatchPdfs(array $invoices): array
    {
        $results = [];

        foreach ($invoices as $invoice) {
            try {
                $errors = $this->validateInvoice($invoice);

                if (empty($errors)) {
                    $path = $this->savePdf($invoice);
                    $results[] = [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'status' => 'success',
                        'path' => $path,
                    ];
                } else {
                    $results[] = [
                        'invoice_id' => $invoice->id,
                        'invoice_number' => $invoice->invoice_number,
                        'status' => 'error',
                        'errors' => $errors,
                    ];
                }
            } catch (\Exception $e) {
                $results[] = [
                    'invoice_id' => $invoice->id,
                    'invoice_number' => $invoice->invoice_number ?? 'N/A',
                    'status' => 'error',
                    'errors' => [$e->getMessage()],
                ];
            }
        }

        return $results;
    }
}
