<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Subscription;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service de gestion de la facturation SaaS
 *
 * Gère la création automatique de factures, calcul des montants,
 * application des remises et gestion des cycles de facturation
 */
class BillingService
{
    /**
     * Générer une facture pour un abonnement
     */
    public function generateInvoiceForSubscription(Subscription $subscription): Invoice
    {
        DB::beginTransaction();

        try {
            // Vérifier que l'abonnement est actif
            if ($subscription->status !== 'active') {
                throw new Exception('L\'abonnement doit être actif pour générer une facture');
            }

            // Vérifier qu'il n'y a pas déjà de facture pour cette période
            $existingInvoice = Invoice::where('company_id', $subscription->company_id)
                ->where('subscription_id', $subscription->id)
                ->where('billing_period_start', now()->startOfMonth())
                ->where('billing_period_end', now()->endOfMonth())
                ->first();

            if ($existingInvoice) {
                throw new Exception('Une facture existe déjà pour cette période');
            }

            // Calculer les montants
            $plan = $subscription->plan;
            $baseAmount = $plan->price;

            // Calculer les utilisateurs supplémentaires si applicable
            $additionalUsersAmount = 0;
            if (! $plan->unlimited_users) {
                $activeUsers = $subscription->company->users()->where('is_active', true)->count();
                if ($activeUsers > $plan->user_limit) {
                    $additionalUsers = $activeUsers - $plan->user_limit;
                    $additionalUsersAmount = $additionalUsers * ($baseAmount * 0.1); // 10% du prix de base par utilisateur supplémentaire
                }
            }

            // Calculer les taxes (18% TVA pour l'Afrique de l'Ouest)
            $subtotal = $baseAmount + $additionalUsersAmount;
            $taxRate = 0.18;
            $taxAmount = $subtotal * $taxRate;
            $totalAmount = $subtotal + $taxAmount;

            // Créer la facture
            $invoice = Invoice::create([
                'company_id' => $subscription->company_id,
                'subscription_id' => $subscription->id,
                'invoice_number' => $this->generateInvoiceNumber(),
                'status' => 'draft',
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'total_amount' => $totalAmount,
                'currency' => 'XOF',
                'billing_period_start' => now()->startOfMonth(),
                'billing_period_end' => now()->endOfMonth(),
                'due_date' => now()->addDays(15),
                'notes' => $this->generateInvoiceNotes($subscription, $additionalUsersAmount > 0 ? ($additionalUsersAmount / ($baseAmount * 0.1)) : 0),
            ]);

            DB::commit();

            Log::info('Facture générée avec succès', [
                'invoice_id' => $invoice->id,
                'company_id' => $subscription->company_id,
                'amount' => $totalAmount,
            ]);

            return $invoice;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la génération de facture', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Traiter les factures mensuelles automatiquement
     */
    public function processMonthlyBilling(): array
    {
        $results = [
            'generated' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        // Récupérer tous les abonnements actifs
        $activeSubscriptions = Subscription::with(['company', 'plan'])
            ->where('status', 'active')
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>', now());
            })
            ->get();

        foreach ($activeSubscriptions as $subscription) {
            try {
                // Vérifier si une facture n'existe pas déjà pour ce mois
                $existingInvoice = Invoice::where('company_id', $subscription->company_id)
                    ->where('subscription_id', $subscription->id)
                    ->where('billing_period_start', now()->startOfMonth())
                    ->exists();

                if (! $existingInvoice) {
                    $this->generateInvoiceForSubscription($subscription);
                    $results['generated']++;
                }

            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'subscription_id' => $subscription->id,
                    'company_name' => $subscription->company->name,
                    'error' => $e->getMessage(),
                ];
            }
        }

        Log::info('Facturation mensuelle terminée', $results);

        return $results;
    }

    /**
     * Calculer les métriques de revenus
     */
    public function calculateRevenueMetrics(): array
    {
        // MRR (Monthly Recurring Revenue)
        $mrr = Subscription::where('subscriptions.status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price') / 100;

        // ARR (Annual Recurring Revenue)
        $arr = $mrr * 12;

        // Revenus réalisés ce mois
        $currentMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('paid_at', now()->month)
            ->whereYear('paid_at', now()->year)
            ->sum('amount') / 100;

        // Revenus réalisés le mois précédent
        $lastMonthRevenue = Payment::where('status', 'completed')
            ->whereMonth('paid_at', now()->subMonth()->month)
            ->whereYear('paid_at', now()->subMonth()->year)
            ->sum('amount') / 100;

        // Croissance mensuelle
        $monthlyGrowth = $lastMonthRevenue > 0
            ? (($currentMonthRevenue - $lastMonthRevenue) / $lastMonthRevenue) * 100
            : 0;

        // Taux de conversion (factures payées)
        $totalInvoices = Invoice::whereMonth('created_at', now()->month)->count();
        $paidInvoices = Invoice::where('status', 'paid')
            ->whereMonth('created_at', now()->month)
            ->count();

        $conversionRate = $totalInvoices > 0 ? ($paidInvoices / $totalInvoices) * 100 : 0;

        return [
            'mrr' => $mrr,
            'arr' => $arr,
            'current_month_revenue' => $currentMonthRevenue,
            'last_month_revenue' => $lastMonthRevenue,
            'monthly_growth' => $monthlyGrowth,
            'conversion_rate' => $conversionRate,
            'total_invoices' => $totalInvoices,
            'paid_invoices' => $paidInvoices,
        ];
    }

    /**
     * Appliquer une remise à une facture
     */
    public function applyDiscount(Invoice $invoice, float $discountPercentage, string $reason = ''): Invoice
    {
        DB::beginTransaction();

        try {
            if ($invoice->status === 'paid') {
                throw new Exception('Impossible d\'appliquer une remise sur une facture déjà payée');
            }

            $discountAmount = $invoice->subtotal * ($discountPercentage / 100);
            $newSubtotal = $invoice->subtotal - $discountAmount;
            $newTaxAmount = $newSubtotal * 0.18; // Recalculer les taxes
            $newTotalAmount = $newSubtotal + $newTaxAmount;

            $invoice->update([
                'subtotal' => $newSubtotal,
                'tax_amount' => $newTaxAmount,
                'total_amount' => $newTotalAmount,
                'notes' => $invoice->notes."\n\nRemise appliquée: {$discountPercentage}% ({$reason})",
            ]);

            DB::commit();

            Log::info('Remise appliquée sur facture', [
                'invoice_id' => $invoice->id,
                'discount_percentage' => $discountPercentage,
                'discount_amount' => $discountAmount,
                'new_total' => $newTotalAmount,
            ]);

            return $invoice->fresh();

        } catch (Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Suspendre un abonnement pour non-paiement
     */
    public function suspendSubscriptionForNonPayment(Subscription $subscription): bool
    {
        try {
            // Vérifier les factures en retard
            $overdueInvoices = Invoice::where('company_id', $subscription->company_id)
                ->where('subscription_id', $subscription->id)
                ->whereIn('status', ['sent', 'overdue'])
                ->where('due_date', '<', now()->subDays(7)) // 7 jours de grâce
                ->count();

            if ($overdueInvoices > 0) {
                $subscription->update(['status' => 'suspended']);

                Log::warning('Abonnement suspendu pour non-paiement', [
                    'subscription_id' => $subscription->id,
                    'company_id' => $subscription->company_id,
                    'overdue_invoices' => $overdueInvoices,
                ]);

                return true;
            }

            return false;

        } catch (Exception $e) {
            Log::error('Erreur lors de la suspension d\'abonnement', [
                'subscription_id' => $subscription->id,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Générer un numéro de facture unique
     */
    private function generateInvoiceNumber(): string
    {
        $year = now()->format('Y');
        $month = now()->format('m');

        $lastInvoice = Invoice::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastInvoice ? (int) substr($lastInvoice->invoice_number, -4) + 1 : 1;

        return "INV-{$year}{$month}-".str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Générer les notes de facture
     */
    private function generateInvoiceNotes(Subscription $subscription, int $additionalUsers = 0): string
    {
        $plan = $subscription->plan;
        $notes = "Facturation mensuelle - Plan {$plan->name}\n";
        $notes .= 'Période: '.now()->startOfMonth()->format('d/m/Y').' au '.now()->endOfMonth()->format('d/m/Y')."\n";

        if ($additionalUsers > 0) {
            $notes .= "\nUtilisateurs supplémentaires: {$additionalUsers}\n";
            $notes .= "Tarif par utilisateur supplémentaire: 10% du prix du plan\n";
        }

        $notes .= "\nConditions de paiement: 15 jours";
        $notes .= "\nTVA incluse au taux de 18%";

        return $notes;
    }
}
