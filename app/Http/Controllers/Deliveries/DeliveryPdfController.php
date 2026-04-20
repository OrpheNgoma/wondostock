<?php

namespace App\Http\Controllers\Deliveries;

use App\Http\Controllers\Controller;
use App\Models\DeliveryTrip;
use App\Services\DeliveryPdfService;
use Illuminate\Http\Response;

class DeliveryPdfController extends Controller
{
    public function __construct(private readonly DeliveryPdfService $pdfService) {}

    public function report(DeliveryTrip $trip): Response
    {
        abort_unless($trip->status->value === 'closed', 403, 'La tournée doit être clôturée.');

        return $this->pdfService->generateReport($trip)->stream(
            "rapport-tournee-{$trip->driver->name}-{$trip->trip_date->format('Y-m-d')}.pdf"
        );
    }

    public function invoice(DeliveryTrip $trip): Response
    {
        abort_unless($trip->status->value === 'closed', 403, 'La tournée doit être clôturée.');

        return $this->pdfService->generateInvoice($trip)->stream(
            "facture-livraison-{$trip->driver->name}-{$trip->trip_date->format('Y-m-d')}.pdf"
        );
    }
}
