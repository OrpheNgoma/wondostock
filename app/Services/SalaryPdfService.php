<?php

namespace App\Services;

use App\Models\SalarySlip;
use Barryvdh\DomPDF\Facade\Pdf;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalaryPdfService
{
    public function generateBulletin(SalarySlip $slip): \Barryvdh\DomPDF\PDF
    {
        $slip->load([
            'driver',
            'user',
            'period.company',
            'deductions',
        ]);

        $pdf = Pdf::loadView('pdf.salary-bulletin', compact('slip'));
        $pdf->setPaper('A4', 'portrait');
        $pdf->setOptions([
            'defaultFont' => 'DejaVu Sans',
            'isHtml5ParserEnabled' => true,
            'isRemoteEnabled' => false,
            'dpi' => 96,
        ]);

        return $pdf;
    }

    public function downloadBulletin(SalarySlip $slip): StreamedResponse
    {
        $pdf = $this->generateBulletin($slip);

        $employeeName = str_replace(' ', '-', strtolower($slip->employee_name));
        $period = $slip->period;
        $filename = "bulletin-{$employeeName}-{$period->year}-{$period->month}.pdf";

        return response()->streamDownload(fn () => print ($pdf->output()), $filename, [
            'Content-Type' => 'application/pdf',
        ]);
    }
}
