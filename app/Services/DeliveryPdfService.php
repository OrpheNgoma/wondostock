<?php

namespace App\Services;

use App\Models\DeliveryTrip;
use Barryvdh\DomPDF\Facade\Pdf;

class DeliveryPdfService
{
    /**
     * Génère le rapport de tournée complet (Rapport de livraison-ventes).
     * Correspond au document Excel "RAPPORT DE LIVRAISON-VENTES".
     */
    public function generateReport(DeliveryTrip $trip): \Barryvdh\DomPDF\PDF
    {
        $trip->load([
            'driver', 'vehicle', 'zone',
            'items.customer', 'items.product',
            'expenses.category',
            'closedBy',
            'company',
        ]);

        $pdf = Pdf::loadView('pdf.delivery-report', compact('trip'));
        $pdf->setPaper('A4', 'landscape');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 96,
        ]);

        return $pdf;
    }

    /**
     * Génère la facture client (bon de livraison signable).
     * Correspond au document Excel "FACT XX/XX/RAM/XXXX".
     */
    public function generateInvoice(DeliveryTrip $trip): \Barryvdh\DomPDF\PDF
    {
        $trip->load([
            'driver', 'zone',
            'items.customer', 'items.product',
            'company',
        ]);

        $pdf = Pdf::loadView('pdf.delivery-invoice', compact('trip'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 96,
        ]);

        return $pdf;
    }

    public function downloadReport(DeliveryTrip $trip): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->generateReport($trip);
        $filename = "rapport-tournee-{$trip->driver->name}-{$trip->trip_date->format('Y-m-d')}.pdf";

        return response()->streamDownload(fn () => print ($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }

    public function downloadInvoice(DeliveryTrip $trip): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $pdf = $this->generateInvoice($trip);
        $filename = "facture-livraison-{$trip->driver->name}-{$trip->trip_date->format('Y-m-d')}.pdf";

        return response()->streamDownload(fn () => print ($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
