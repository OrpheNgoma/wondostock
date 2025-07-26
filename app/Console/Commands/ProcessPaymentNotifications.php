<?php

namespace App\Console\Commands;

use App\Services\PaymentNotificationService;
use Illuminate\Console\Command;

class ProcessPaymentNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payment:process-notifications
                            {--generate : Génère automatiquement les notifications pour les factures existantes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Traite les notifications de paiement en attente et génère de nouvelles notifications';

    /**
     * Execute the console command.
     */
    public function handle(PaymentNotificationService $notificationService)
    {
        $this->info('Traitement des notifications de paiement...');

        if ($this->option('generate')) {
            $this->info('Génération de nouvelles notifications...');
            
            $stats = $notificationService->generateNotificationsForExistingInvoices();
            
            $this->table(
                ['Type', 'Nombre créé'],
                [
                    ['Rappels de paiement', $stats['reminders_created']],
                    ['Notifications de retard', $stats['overdue_created']],
                    ['Avertissements de suspension', $stats['suspension_warnings_created']],
                ]
            );
        }

        $this->info('Envoi des notifications programmées...');
        
        $sentCount = $notificationService->processPendingNotifications();
        
        $this->info("✅ {$sentCount} notification(s) envoyée(s) avec succès.");
        
        return 0;
    }
}
