<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Subscription;
use App\Models\BillingPayment;
use Carbon\Carbon;

/**
 * Service pour la gestion automatique des factures SaaS.
 */
class InvoiceService
{
    /**
     * Génère une facture pour un abonnement.
     */
    public function generateInvoiceForSubscription(Subscription $subscription): Invoice
    {
        $plan = $subscription->plan;
        $company = $subscription->company;

        // Calculer les montants
        $amount = $plan->price;
        $taxRate = 0.18; // 18% TVA par défaut
        $taxAmount = $amount * $taxRate;
        $totalAmount = $amount + $taxAmount;

        // Créer la facture
        $invoice = Invoice::create([
            'invoice_number' => Invoice::generateInvoiceNumber(),
            'company_id' => $company->id,
            'subscription_id' => $subscription->id,
            'amount' => $amount,
            'tax_amount' => $taxAmount,
            'total_amount' => $totalAmount,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => now()->addDays(30), // 30 jours pour payer
            'billing_address' => [
                'company_name' => $company->name,
                'legal_name' => $company->legal_name,
                'address' => $company->address,
                'email' => $company->email,
                'phone' => $company->phone_number,
                'rccm' => $company->rccm,
                'nif' => $company->nif,
            ],
            'notes' => "Abonnement {$plan->name} - Période de facturation",
        ]);

        return $invoice;
    }

    /**
     * Génère des factures pour tous les abonnements actifs qui doivent être facturés.
     */
    public function generateMonthlyInvoices(): array
    {
        $generatedInvoices = [];
        
        // Récupérer tous les abonnements actifs
        $subscriptions = Subscription::where('status', 'active')
            ->with(['company', 'plan'])
            ->get();

        foreach ($subscriptions as $subscription) {
            // Vérifier si une facture a déjà été générée ce mois
            $existingInvoice = Invoice::where('subscription_id', $subscription->id)
                ->whereYear('issue_date', now()->year)
                ->whereMonth('issue_date', now()->month)
                ->first();

            if (!$existingInvoice) {
                $invoice = $this->generateInvoiceForSubscription($subscription);
                $generatedInvoices[] = $invoice;
            }
        }

        return $generatedInvoices;
    }

    /**
     * Marque une facture comme payée.
     */
    public function markInvoiceAsPaid(Invoice $invoice, array $paymentData): BillingPayment
    {
        // Créer l'enregistrement de paiement
        $payment = BillingPayment::create([
            'invoice_id' => $invoice->id,
            'amount' => $paymentData['amount'] ?? $invoice->total_amount,
            'payment_method' => $paymentData['payment_method'] ?? 'manual',
            'transaction_id' => $paymentData['transaction_id'] ?? null,
            'status' => 'completed',
            'payment_date' => $paymentData['payment_date'] ?? now(),
            'payment_details' => $paymentData['details'] ?? null,
            'notes' => $paymentData['notes'] ?? null,
        ]);

        // Mettre à jour le statut de la facture
        $invoice->update([
            'status' => 'paid',
            'paid_at' => $payment->payment_date,
        ]);

        return $payment;
    }

    /**
     * Envoie une facture par email.
     */
    public function sendInvoice(Invoice $invoice): bool
    {
        try {
            // Mettre à jour le statut
            $invoice->update(['status' => 'sent']);
            
            // TODO: Implémenter l'envoi d'email avec PDF
            // Mail::to($invoice->company->email)->send(new InvoiceMail($invoice));
            
            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'envoi de la facture: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Vérifie et marque les factures en retard.
     */
    public function checkOverdueInvoices(): array
    {
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            if ($invoice->status !== 'overdue') {
                $invoice->update(['status' => 'overdue']);
            }
        }

        return $overdueInvoices->toArray();
    }

    /**
     * Calcule les statistiques de facturation.
     */
    public function getBillingStats(): array
    {
        return [
            'total_invoices' => Invoice::count(),
            'paid_invoices' => Invoice::where('status', 'paid')->count(),
            'overdue_invoices' => Invoice::where('status', 'overdue')->count(),
            'pending_invoices' => Invoice::whereIn('status', ['draft', 'sent'])->count(),
            'total_revenue' => Invoice::where('status', 'paid')->sum('total_amount'),
            'pending_revenue' => Invoice::whereIn('status', ['draft', 'sent', 'overdue'])->sum('total_amount'),
            'monthly_revenue' => Invoice::where('status', 'paid')
                ->whereYear('paid_at', now()->year)
                ->whereMonth('paid_at', now()->month)
                ->sum('total_amount'),
        ];
    }

    /**
     * Annule une facture.
     */
    public function cancelInvoice(Invoice $invoice, string $reason = null): bool
    {
        try {
            $invoice->update([
                'status' => 'cancelled',
                'notes' => ($invoice->notes ? $invoice->notes . "\n\n" : '') . 
                          "Facture annulée le " . now()->format('d/m/Y') . 
                          ($reason ? " - Raison: {$reason}" : ''),
            ]);

            return true;
        } catch (\Exception $e) {
            \Log::error('Erreur lors de l\'annulation de la facture: ' . $e->getMessage());
            return false;
        }
    }
}