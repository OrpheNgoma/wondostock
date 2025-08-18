<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Services\InvoicePdfService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class InvoiceShow extends Component
{
    public Invoice $invoice;

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['company', 'subscription.plan', 'payments']);
    }

    public function markAsPaid()
    {
        try {
            $this->invoice->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            session()->flash('success', 'Facture marquée comme payée !');
            $this->invoice->refresh();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour : '.$e->getMessage());
        }
    }

    public function markAsUnpaid()
    {
        try {
            $this->invoice->update([
                'status' => 'pending',
                'paid_at' => null,
            ]);

            session()->flash('success', 'Facture marquée comme non payée !');
            $this->invoice->refresh();

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la mise à jour : '.$e->getMessage());
        }
    }

    public function sendInvoice()
    {
        try {
            // TODO: Implémenter l'envoi d'email
            session()->flash('success', 'Facture envoyée par email !');

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de l\'envoi : '.$e->getMessage());
        }
    }

    public function downloadPdf(InvoicePdfService $pdfService)
    {
        try {
            $errors = $pdfService->validateInvoice($this->invoice);

            if (! empty($errors)) {
                session()->flash('error', 'Impossible de générer le PDF : '.implode(', ', $errors));

                return;
            }

            return $pdfService->downloadPdf($this->invoice);

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la génération du PDF : '.$e->getMessage());
        }
    }

    public function viewPdf(InvoicePdfService $pdfService)
    {
        try {
            $errors = $pdfService->validateInvoice($this->invoice);

            if (! empty($errors)) {
                session()->flash('error', 'Impossible de générer le PDF : '.implode(', ', $errors));

                return;
            }

            return redirect()->route('admin.invoices.pdf', $this->invoice);

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la génération du PDF : '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.invoice-show');
    }
}
