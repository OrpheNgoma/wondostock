<?php

namespace App\Livewire\Admin;

use App\Models\Invoice;
use App\Models\Company;
use App\Models\Subscription;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class InvoiceEdit extends Component
{
    public Invoice $invoice;
    
    public $invoice_number = '';
    public $company_id = '';
    public $subscription_id = '';
    public $amount = '';
    public $tax_amount = '';
    public $total_amount = '';
    public $status = '';
    public $issue_date = '';
    public $due_date = '';
    public $billing_address = '';
    public $notes = '';

    public $companies = [];
    public $subscriptions = [];

    protected $rules = [
        'invoice_number' => 'required|string|max:255',
        'company_id' => 'required|exists:companies,id',
        'subscription_id' => 'nullable|exists:subscriptions,id',
        'amount' => 'required|numeric|min:0',
        'tax_amount' => 'required|numeric|min:0',
        'total_amount' => 'required|numeric|min:0',
        'status' => 'required|in:pending,paid,cancelled',
        'issue_date' => 'required|date',
        'due_date' => 'required|date|after_or_equal:issue_date',
        'billing_address' => 'nullable|string',
        'notes' => 'nullable|string',
    ];

    protected $messages = [
        'invoice_number.required' => 'Le numéro de facture est requis.',
        'company_id.required' => 'Veuillez sélectionner une entreprise.',
        'amount.required' => 'Le montant HT est requis.',
        'amount.numeric' => 'Le montant HT doit être un nombre.',
        'tax_amount.required' => 'Le montant de TVA est requis.',
        'tax_amount.numeric' => 'Le montant de TVA doit être un nombre.',
        'total_amount.required' => 'Le montant total est requis.',
        'total_amount.numeric' => 'Le montant total doit être un nombre.',
        'issue_date.required' => 'La date d\'émission est requise.',
        'due_date.required' => 'La date d\'échéance est requise.',
        'due_date.after_or_equal' => 'La date d\'échéance ne peut pas être antérieure à la date d\'émission.',
    ];

    public function mount(Invoice $invoice)
    {
        $this->invoice = $invoice->load(['company', 'subscription']);
        
        $this->companies = Company::orderBy('name')->get();
        $this->subscriptions = Subscription::with(['company', 'plan'])->orderBy('created_at', 'desc')->get();
        
        // Charger les valeurs actuelles
        $this->invoice_number = $invoice->invoice_number;
        $this->company_id = $invoice->company_id;
        $this->subscription_id = $invoice->subscription_id;
        $this->amount = $invoice->amount;
        $this->tax_amount = $invoice->tax_amount;
        $this->total_amount = $invoice->total_amount;
        $this->status = $invoice->status;
        $this->issue_date = $invoice->issue_date->format('Y-m-d');
        $this->due_date = $invoice->due_date->format('Y-m-d');
        $this->billing_address = is_array($invoice->billing_address) 
            ? implode("\n", $invoice->billing_address) 
            : ($invoice->billing_address ?? '');
        $this->notes = $invoice->notes;
    }

    public function updatedAmount()
    {
        $this->calculateTotal();
    }

    public function updatedTaxAmount()
    {
        $this->calculateTotal();
    }

    public function calculateTotal()
    {
        if (is_numeric($this->amount) && is_numeric($this->tax_amount)) {
            $this->total_amount = number_format((float)$this->amount + (float)$this->tax_amount, 2, '.', '');
        }
    }

    public function save()
    {
        // Validation unique pour invoice_number (exclure la facture actuelle)
        $rules = $this->rules;
        $rules['invoice_number'] .= '|unique:invoices,invoice_number,' . $this->invoice->id;

        $this->validate($rules);

        try {
            // Préparer l'adresse de facturation
            $billingAddress = !empty($this->billing_address) 
                ? array_filter(explode("\n", $this->billing_address))
                : null;

            $this->invoice->update([
                'invoice_number' => $this->invoice_number,
                'company_id' => $this->company_id,
                'subscription_id' => $this->subscription_id ?: null,
                'amount' => $this->amount,
                'tax_amount' => $this->tax_amount,
                'total_amount' => $this->total_amount,
                'status' => $this->status,
                'issue_date' => $this->issue_date,
                'due_date' => $this->due_date,
                'billing_address' => $billingAddress,
                'notes' => $this->notes,
                // Mettre à jour paid_at si le statut change
                'paid_at' => $this->status === 'paid' ? ($this->invoice->paid_at ?? now()) : null,
            ]);

            session()->flash('success', 'Facture modifiée avec succès !');
            return $this->redirect(route('admin.invoices.show', $this->invoice));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification de la facture : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.invoice-edit');
    }
}