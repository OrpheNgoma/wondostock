<?php

namespace App\Livewire\GlobalAdmin;

use App\Models\Invoice;
use Barryvdh\DomPDF\Facade\Pdf;
use Livewire\Component;

class InvoicePDF extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['company', 'subscription', 'subscription.plan']);
    }

    public function downloadPDF()
    {
        $pdf = PDF::loadView('pdfs.invoice', [
            'invoice' => $this->invoice,
        ]);

        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, "facture-{$this->invoice->invoice_number}.pdf");
    }

    public function render()
    {
        return view('livewire.global-admin.invoice-p-d-f');
    }
}
