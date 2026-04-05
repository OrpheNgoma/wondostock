<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Service de gestion des paiements
 *
 * Gère le traitement des paiements, intégrations avec les gateways,
 * réconciliation et gestion des remboursements
 */
class PaymentService
{
    /**
     * Créer un nouveau paiement
     */
    public function createPayment(array $data): Payment
    {
        DB::beginTransaction();

        try {
            // Générer l'ID de transaction si pas fourni
            if (empty($data['transaction_id'])) {
                $data['transaction_id'] = $this->generateTransactionId();
            }

            // Valider les données
            $this->validatePaymentData($data);

            $payment = Payment::create($data);

            // Si le paiement est marqué comme terminé, traiter immédiatement
            if ($data['status'] === 'completed') {
                $this->processCompletedPayment($payment);
            }

            DB::commit();

            Log::info('Paiement créé avec succès', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'amount' => $payment->amount,
            ]);

            return $payment;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors de la création du paiement', [
                'data' => $data,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Traiter un paiement terminé
     */
    public function processCompletedPayment(Payment $payment): bool
    {
        try {
            if ($payment->status !== 'completed') {
                throw new Exception('Le paiement doit être marqué comme terminé');
            }

            // Mettre à jour la date de traitement
            if (! $payment->processed_at) {
                $payment->update(['processed_at' => now()]);
            }

            // Traiter la facture associée si applicable
            if ($payment->invoice) {
                $this->updateInvoiceStatus($payment->invoice);
            }

            // Réactiver l'abonnement si suspendu pour non-paiement
            $this->reactivateSubscriptionIfNeeded($payment);

            Log::info('Paiement traité avec succès', [
                'payment_id' => $payment->id,
                'invoice_id' => $payment->invoice_id,
            ]);

            return true;

        } catch (Exception $e) {
            Log::error('Erreur lors du traitement du paiement', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Rembourser un paiement
     */
    public function refundPayment(Payment $payment, ?float $refundAmount = null, string $reason = ''): Payment
    {
        DB::beginTransaction();

        try {
            if ($payment->status !== 'completed') {
                throw new Exception('Seuls les paiements terminés peuvent être remboursés');
            }

            $refundAmount = $refundAmount ?? $payment->amount;

            if ($refundAmount > $payment->amount) {
                throw new Exception('Le montant du remboursement ne peut pas être supérieur au montant du paiement');
            }

            // Créer une entrée de remboursement
            $refundPayment = Payment::create([
                'company_id' => $payment->company_id,
                'invoice_id' => $payment->invoice_id,
                'amount' => -$refundAmount, // Montant négatif pour le remboursement
                'payment_method' => $payment->payment_method,
                'status' => 'completed',
                'transaction_id' => 'REFUND_'.$payment->transaction_id,
                'paid_at' => now(),
                'processed_at' => now(),
                'notes' => "Remboursement de {$payment->transaction_id}. Raison: {$reason}",
            ]);

            // Marquer le paiement original comme remboursé si remboursement total
            if ($refundAmount == $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }

            // Mettre à jour le statut de la facture si nécessaire
            if ($payment->invoice) {
                $this->updateInvoiceStatus($payment->invoice);
            }

            DB::commit();

            Log::info('Remboursement traité', [
                'original_payment_id' => $payment->id,
                'refund_payment_id' => $refundPayment->id,
                'refund_amount' => $refundAmount,
            ]);

            return $refundPayment;

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('Erreur lors du remboursement', [
                'payment_id' => $payment->id,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Traiter les paiements en attente via webhook
     */
    public function processWebhookPayment(array $webhookData): ?Payment
    {
        try {
            // Vérifier la signature du webhook (sécurité)
            $this->verifyWebhookSignature($webhookData);

            $transactionId = $webhookData['transaction_id'] ?? null;
            if (! $transactionId) {
                throw new Exception('ID de transaction manquant dans le webhook');
            }

            // Rechercher le paiement correspondant
            $payment = Payment::where('transaction_id', $transactionId)->first();
            if (! $payment) {
                Log::warning('Paiement non trouvé pour webhook', ['transaction_id' => $transactionId]);

                return null;
            }

            // Mettre à jour le statut selon la réponse du gateway
            $newStatus = $this->mapWebhookStatusToPaymentStatus($webhookData['status']);

            $payment->update([
                'status' => $newStatus,
                'processed_at' => $newStatus === 'completed' ? now() : null,
                'gateway_response' => json_encode($webhookData),
            ]);

            // Traiter si paiement terminé
            if ($newStatus === 'completed') {
                $this->processCompletedPayment($payment);
            }

            Log::info('Webhook traité avec succès', [
                'transaction_id' => $transactionId,
                'new_status' => $newStatus,
            ]);

            return $payment;

        } catch (Exception $e) {
            Log::error('Erreur lors du traitement du webhook', [
                'webhook_data' => $webhookData,
                'error' => $e->getMessage(),
            ]);
            throw $e;
        }
    }

    /**
     * Calculer les statistiques de paiements
     */
    public function getPaymentStatistics(?Carbon $startDate = null, ?Carbon $endDate = null): array
    {
        $startDate = $startDate ?? now()->startOfMonth();
        $endDate = $endDate ?? now()->endOfMonth();

        $query = Payment::whereBetween('created_at', [$startDate, $endDate]);

        $totalPayments = $query->count();
        $completedPayments = $query->clone()->where('status', 'completed')->count();
        $failedPayments = $query->clone()->where('status', 'failed')->count();
        $pendingPayments = $query->clone()->where('status', 'pending')->count();

        $totalAmount = $query->clone()->where('status', 'completed')->sum('amount') / 100;
        $averageAmount = $completedPayments > 0 ? $totalAmount / $completedPayments : 0;

        // Répartition par méthode de paiement
        $paymentMethods = $query->clone()
            ->where('status', 'completed')
            ->select('payment_method', DB::raw('count(*) as count'), DB::raw('sum(amount) as amount'))
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->payment_method => [
                    'count' => $item->count,
                    'amount' => $item->amount / 100,
                ]];
            });

        return [
            'period' => [
                'start' => $startDate->format('d/m/Y'),
                'end' => $endDate->format('d/m/Y'),
            ],
            'total_payments' => $totalPayments,
            'completed_payments' => $completedPayments,
            'failed_payments' => $failedPayments,
            'pending_payments' => $pendingPayments,
            'success_rate' => $totalPayments > 0 ? ($completedPayments / $totalPayments) * 100 : 0,
            'total_amount' => $totalAmount,
            'average_amount' => $averageAmount,
            'payment_methods' => $paymentMethods,
        ];
    }

    /**
     * Envoyer des relances pour paiements en retard
     */
    public function sendPaymentReminders(): array
    {
        $results = [
            'sent' => 0,
            'failed' => 0,
            'errors' => [],
        ];

        // Récupérer les factures en retard
        $overdueInvoices = Invoice::with(['company', 'payments'])
            ->whereIn('status', ['sent', 'overdue'])
            ->where('due_date', '<', now())
            ->get();

        foreach ($overdueInvoices as $invoice) {
            try {
                // Vérifier si une relance n'a pas déjà été envoyée récemment
                $lastReminder = $invoice->updated_at;
                if ($lastReminder && $lastReminder->diffInDays(now()) < 3) {
                    continue; // Pas de relance si moins de 3 jours
                }

                // Marquer la facture comme en retard
                $invoice->update(['status' => 'overdue']);

                // Ici, on pourrait intégrer l'envoi d'email/SMS
                // $this->sendReminderNotification($invoice);

                $results['sent']++;

                Log::info('Relance envoyée', [
                    'invoice_id' => $invoice->id,
                    'company_id' => $invoice->company_id,
                ]);

            } catch (Exception $e) {
                $results['failed']++;
                $results['errors'][] = [
                    'invoice_id' => $invoice->id,
                    'error' => $e->getMessage(),
                ];
            }
        }

        return $results;
    }

    /**
     * Valider les données de paiement
     */
    private function validatePaymentData(array $data): void
    {
        if (empty($data['amount']) || $data['amount'] <= 0) {
            throw new Exception('Le montant du paiement doit être positif');
        }

        if (empty($data['company_id'])) {
            throw new Exception('L\'ID de l\'entreprise est requis');
        }

        if (! Company::find($data['company_id'])) {
            throw new Exception('Entreprise non trouvée');
        }

        if (! empty($data['invoice_id']) && ! Invoice::find($data['invoice_id'])) {
            throw new Exception('Facture non trouvée');
        }
    }

    /**
     * Mettre à jour le statut de la facture selon les paiements
     */
    private function updateInvoiceStatus(Invoice $invoice): void
    {
        $totalPaid = $invoice->payments()
            ->where('status', 'completed')
            ->sum('amount');

        if ($totalPaid >= $invoice->total_amount) {
            $invoice->update(['status' => 'paid']);
        } elseif ($totalPaid > 0) {
            $invoice->update(['status' => 'partially_paid']);
        }
    }

    /**
     * Réactiver un abonnement suspendu si nécessaire
     */
    private function reactivateSubscriptionIfNeeded(Payment $payment): void
    {
        if (! $payment->invoice || ! $payment->invoice->subscription) {
            return;
        }

        $subscription = $payment->invoice->subscription;

        if ($subscription->status === 'suspended') {
            // Vérifier s'il n'y a plus de factures en retard
            $overdueInvoices = Invoice::where('company_id', $subscription->company_id)
                ->where('subscription_id', $subscription->id)
                ->whereIn('status', ['sent', 'overdue'])
                ->where('due_date', '<', now())
                ->count();

            if ($overdueInvoices === 0) {
                $subscription->update(['status' => 'active']);

                Log::info('Abonnement réactivé', [
                    'subscription_id' => $subscription->id,
                ]);
            }
        }
    }

    /**
     * Générer un ID de transaction unique sans dépendre d'un COUNT() concurrent.
     */
    private function generateTransactionId(): string
    {
        return 'TXN_'.now()->format('Ymd_His').'_'.strtoupper(substr(uniqid('', true), -8));
    }

    /**
     * Vérifier la signature du webhook selon le secret configuré.
     *
     * @throws \Exception si le secret n'est pas configuré ou si la signature est invalide
     */
    private function verifyWebhookSignature(array $webhookData): bool
    {
        $secret = config('services.payment_gateway.webhook_secret');

        if (empty($secret)) {
            throw new \Exception(
                'La vérification de la signature webhook n\'est pas configurée. '.
                'Définissez PAYMENT_WEBHOOK_SECRET dans votre fichier .env.'
            );
        }

        $signature = $webhookData['signature'] ?? null;

        if (empty($signature)) {
            throw new \Exception('Signature webhook manquante dans les données reçues.');
        }

        $payload = json_encode(array_diff_key($webhookData, ['signature' => '']));
        $expectedSig = hash_hmac('sha256', $payload, $secret);

        if (! hash_equals($expectedSig, $signature)) {
            throw new \Exception('Signature webhook invalide — requête rejetée.');
        }

        return true;
    }

    /**
     * Mapper le statut du webhook vers le statut de paiement
     */
    private function mapWebhookStatusToPaymentStatus(string $webhookStatus): string
    {
        return match (strtolower($webhookStatus)) {
            'success', 'completed', 'paid' => 'completed',
            'failed', 'error', 'declined' => 'failed',
            'pending', 'processing' => 'processing',
            'cancelled', 'canceled' => 'cancelled',
            default => 'pending'
        };
    }
}
