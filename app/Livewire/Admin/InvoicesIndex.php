<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Models\Company;
use App\Models\BillingPayment;
use App\Services\InvoiceService;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Gestion des Factures - WondoStock Admin')]
class InvoicesIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $statusFilter = '';
    public string $companyFilter = '';
    public string $sortBy = 'created_at';
    public string $sortDirection = 'desc';
    
    // Modal de paiement manuel
    public bool $showPaymentModal = false;
    public ?int $invoiceToPayId = null;
    public float $paymentAmount = 0.00;
    public string $paymentMethod = 'manual';
    public string $transactionId = '';
    public string $paymentNotes = '';
    public ?string $paymentDate = null;

    public function mount()
    {
        if (!Auth::user()->is_global_admin) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedCompanyFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function sendInvoice(Invoice $invoice, InvoiceService $invoiceService)
    {
        if ($invoiceService->sendInvoice($invoice)) {
            $this->dispatch('notify', [
                'message' => 'Facture envoyée avec succès.',
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de l\'envoi de la facture.',
                'type' => 'error'
            ]);
        }
    }

    public function showPaymentModal(Invoice $invoice)
    {
        $this->invoiceToPayId = $invoice->id;
        $this->paymentAmount = (float) $invoice->total_amount;
        $this->paymentDate = now()->format('Y-m-d');
        $this->paymentMethod = 'manual';
        $this->transactionId = '';
        $this->paymentNotes = '';
        $this->showPaymentModal = true;
    }

    public function recordPayment(InvoiceService $invoiceService)
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|string',
            'paymentDate' => 'required|date',
            'transactionId' => 'nullable|string|max:255',
            'paymentNotes' => 'nullable|string|max:500',
        ]);

        try {
            $invoice = Invoice::findOrFail($this->invoiceToPayId);
            
            $paymentData = [
                'amount' => $this->paymentAmount,
                'payment_method' => $this->paymentMethod,
                'payment_date' => $this->paymentDate,
                'transaction_id' => $this->transactionId ?: null,
                'notes' => $this->paymentNotes ?: null,
            ];

            $invoiceService->markInvoiceAsPaid($invoice, $paymentData);

            $this->dispatch('notify', [
                'message' => 'Paiement enregistré avec succès.',
                'type' => 'success'
            ]);

            $this->closePaymentModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de l\'enregistrement du paiement: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function cancelInvoice(Invoice $invoice, InvoiceService $invoiceService)
    {
        if ($invoiceService->cancelInvoice($invoice, 'Annulée par l\'administrateur')) {
            $this->dispatch('notify', [
                'message' => 'Facture annulée avec succès.',
                'type' => 'success'
            ]);
        } else {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de l\'annulation de la facture.',
                'type' => 'error'
            ]);
        }
    }

    public function generateMonthlyInvoices(InvoiceService $invoiceService)
    {
        try {
            $invoices = $invoiceService->generateMonthlyInvoices();
            
            $message = count($invoices) > 0 
                ? count($invoices) . ' facture(s) générée(s) avec succès.'
                : 'Aucune nouvelle facture à générer.';

            $this->dispatch('notify', [
                'message' => $message,
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de la génération des factures: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function closePaymentModal()
    {
        $this->showPaymentModal = false;
        $this->invoiceToPayId = null;
        $this->paymentAmount = 0.00;
        $this->paymentMethod = 'manual';
        $this->transactionId = '';
        $this->paymentNotes = '';
        $this->paymentDate = null;
        $this->resetErrorBag();
    }

    public function getStatsProperty()
    {
        $invoiceService = app(InvoiceService::class);
        return $invoiceService->getBillingStats();
    }

    public function render()
    {
        $invoices = Invoice::with(['company', 'subscription.plan', 'payments'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('invoice_number', 'like', '%' . $this->search . '%')
                      ->orWhereHas('company', function($companyQuery) {
                          $companyQuery->where('name', 'like', '%' . $this->search . '%')
                                       ->orWhere('email', 'like', '%' . $this->search . '%');
                      });
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->companyFilter, function ($query) {
                $query->where('company_id', $this->companyFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $companies = Company::orderBy('name')->get();

        return view('livewire.admin.invoices-index', [
            'invoices' => $invoices,
            'companies' => $companies,
        ]);
    }
}
