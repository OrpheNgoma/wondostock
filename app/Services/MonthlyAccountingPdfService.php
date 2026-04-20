<?php

namespace App\Services;

use App\Models\Store;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonthlyAccountingPdfService
{
    public function __construct(public readonly MonthlyAccountingService $accountingService) {}

    public function generate(int $companyId, int $storeId, Carbon $from, Carbon $to): \Barryvdh\DomPDF\PDF
    {
        $report = $this->accountingService->getReport($companyId, $storeId, $from, $to);

        $pdf = Pdf::loadView('pdf.monthly-accounting', compact('report'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 96,
        ]);

        return $pdf;
    }

    public function download(int $companyId, int $storeId, Carbon $from, Carbon $to): StreamedResponse
    {
        $store = Store::where('company_id', $companyId)->findOrFail($storeId);
        $pdf = $this->generate($companyId, $storeId, $from, $to);
        $filename = 'point-comptabilite-'
            .str_replace(' ', '-', strtolower($store->name)).'-'
            .$from->format('d-m-Y').'-'
            .$to->format('d-m-Y').'.pdf';

        return response()->streamDownload(fn () => print ($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
