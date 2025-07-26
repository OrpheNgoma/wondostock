<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Invoice;
use App\Models\PaymentNotification;
use App\Models\Subscription;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

/**
 * Service pour gérer les notifications de paiement automatiques.
 */
class PaymentNotificationService
{
    /**
     * Créer une notification de rappel de paiement.
     */
    public function createPaymentReminder(Invoice $invoice, int $daysBefore = 7): PaymentNotification
    {
        $dueDate = $invoice->due_date;
        $scheduledFor = $dueDate->subDays($daysBefore);

        return PaymentNotification::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'type' => PaymentNotification::TYPE_REMINDER,
            'title' => 'Rappel de paiement',
            'message' => "Votre facture {$invoice->invoice_number} d'un montant de " . 
                        number_format($invoice->total_amount, 0, ',', ' ') . 
                        " FCFA arrive à échéance le " . $dueDate->format('d/m/Y') . ".",
            'scheduled_for' => $scheduledFor,
            'notification_data' => [
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->total_amount,
                'due_date' => $dueDate->toDateString(),
                'days_before' => $daysBefore,
            ],
        ]);
    }

    /**
     * Créer une notification de facture en retard.
     */
    public function createOverdueNotification(Invoice $invoice): PaymentNotification
    {
        $daysPastDue = $invoice->due_date->diffInDays(now());

        return PaymentNotification::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'type' => PaymentNotification::TYPE_OVERDUE,
            'title' => 'Facture en retard',
            'message' => "Votre facture {$invoice->invoice_number} d'un montant de " . 
                        number_format($invoice->total_amount, 0, ',', ' ') . 
                        " FCFA est en retard de {$daysPastDue} jour(s). Veuillez procéder au paiement rapidement.",
            'scheduled_for' => now(),
            'notification_data' => [
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->total_amount,
                'due_date' => $invoice->due_date->toDateString(),
                'days_past_due' => $daysPastDue,
            ],
        ]);
    }

    /**
     * Créer une notification de paiement reçu.
     */
    public function createPaymentReceivedNotification(Invoice $invoice): PaymentNotification
    {
        return PaymentNotification::create([
            'company_id' => $invoice->company_id,
            'invoice_id' => $invoice->id,
            'type' => PaymentNotification::TYPE_PAYMENT_RECEIVED,
            'title' => 'Paiement reçu',
            'message' => "Nous avons bien reçu votre paiement pour la facture {$invoice->invoice_number}. Merci !",
            'scheduled_for' => now(),
            'notification_data' => [
                'invoice_number' => $invoice->invoice_number,
                'amount' => $invoice->total_amount,
                'paid_at' => $invoice->paid_at->toDateString(),
            ],
        ]);
    }

    /**
     * Créer une notification d'avertissement de suspension.
     */
    public function createSuspensionWarningNotification(Company $company, int $daysBeforeSuspension = 3): PaymentNotification
    {
        $overdueInvoices = Invoice::where('company_id', $company->id)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->count();

        return PaymentNotification::create([
            'company_id' => $company->id,
            'type' => PaymentNotification::TYPE_SUSPENSION_WARNING,
            'title' => 'Avertissement de suspension',
            'message' => "Attention ! Vous avez {$overdueInvoices} facture(s) en retard. " .
                        "Votre compte sera suspendu dans {$daysBeforeSuspension} jour(s) si aucun paiement n'est effectué.",
            'scheduled_for' => now(),
            'notification_data' => [
                'overdue_invoices_count' => $overdueInvoices,
                'days_before_suspension' => $daysBeforeSuspension,
                'suspension_date' => now()->addDays($daysBeforeSuspension)->toDateString(),
            ],
        ]);
    }

    /**
     * Traiter les notifications programmées qui doivent être envoyées.
     */
    public function processPendingNotifications(): int
    {
        $notifications = PaymentNotification::where('status', PaymentNotification::STATUS_PENDING)
            ->where('scheduled_for', '<=', now())
            ->get();

        $sentCount = 0;

        foreach ($notifications as $notification) {
            try {
                $this->sendNotification($notification);
                $notification->markAsSent();
                $sentCount++;
            } catch (\Exception $e) {
                Log::error('Erreur lors de l\'envoi de notification', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage(),
                ]);
                $notification->markAsFailed();
            }
        }

        return $sentCount;
    }

    /**
     * Envoyer une notification spécifique.
     */
    protected function sendNotification(PaymentNotification $notification): void
    {
        // Ici, on peut implémenter différents canaux de notification
        // Pour l'instant, on se contente de logger
        Log::info('Notification envoyée', [
            'company_id' => $notification->company_id,
            'type' => $notification->type,
            'title' => $notification->title,
            'message' => $notification->message,
        ]);

        // Dans une implémentation réelle, on pourrait envoyer :
        // - Des emails
        // - Des notifications push
        // - Des SMS
        // - Des notifications dans l'interface utilisateur
    }

    /**
     * Générer automatiquement les notifications pour les factures existantes.
     */
    public function generateNotificationsForExistingInvoices(): array
    {
        $stats = [
            'reminders_created' => 0,
            'overdue_created' => 0,
            'suspension_warnings_created' => 0,
        ];

        // Créer des rappels pour les factures à venir
        $upcomingInvoices = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '>', now())
            ->where('due_date', '<=', now()->addDays(7))
            ->whereDoesntHave('paymentNotifications', function ($query) {
                $query->where('type', PaymentNotification::TYPE_REMINDER);
            })
            ->get();

        foreach ($upcomingInvoices as $invoice) {
            $this->createPaymentReminder($invoice);
            $stats['reminders_created']++;
        }

        // Créer des notifications pour les factures en retard
        $overdueInvoices = Invoice::where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->whereDoesntHave('paymentNotifications', function ($query) {
                $query->where('type', PaymentNotification::TYPE_OVERDUE)
                    ->where('created_at', '>=', now()->subDays(1));
            })
            ->get();

        foreach ($overdueInvoices as $invoice) {
            $this->createOverdueNotification($invoice);
            $stats['overdue_created']++;
        }

        // Créer des avertissements de suspension pour les entreprises avec plusieurs factures en retard
        $companiesWithOverdueInvoices = Company::whereHas('invoices', function ($query) {
                $query->where('status', '!=', 'paid')
                    ->where('due_date', '<', now()->subDays(15));
            })
            ->whereDoesntHave('paymentNotifications', function ($query) {
                $query->where('type', PaymentNotification::TYPE_SUSPENSION_WARNING)
                    ->where('created_at', '>=', now()->subDays(7));
            })
            ->get();

        foreach ($companiesWithOverdueInvoices as $company) {
            $this->createSuspensionWarningNotification($company);
            $stats['suspension_warnings_created']++;
        }

        return $stats;
    }

    /**
     * Obtenir les notifications d'une entreprise.
     */
    public function getCompanyNotifications(int $companyId, int $limit = 10): Collection
    {
        return PaymentNotification::where('company_id', $companyId)
            ->with('invoice')
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Marquer une notification comme lue (si on implémente un système de notifications dans l'interface).
     */
    public function markNotificationAsRead(PaymentNotification $notification): void
    {
        $notificationData = $notification->notification_data ?? [];
        $notificationData['read_at'] = now()->toISOString();
        
        $notification->update([
            'notification_data' => $notificationData,
        ]);
    }
}