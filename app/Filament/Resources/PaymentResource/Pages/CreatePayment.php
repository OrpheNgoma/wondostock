<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;

class CreatePayment extends CreateRecord
{
    protected static string $resource = PaymentResource::class;

    protected ?string $heading = 'Créer un Nouveau Paiement';

    protected ?string $subheading = 'Enregistrer un nouveau paiement dans le système';

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Générer automatiquement l'ID de transaction si pas fourni
        if (empty($data['transaction_id'])) {
            $data['transaction_id'] = 'TXN_'.now()->format('Ymd_His').'_'.str_pad(Payment::count() + 1, 3, '0', STR_PAD_LEFT);
        }

        // Si le paiement est marqué comme terminé, définir processed_at
        if ($data['status'] === 'completed' && empty($data['processed_at'])) {
            $data['processed_at'] = now();
        }

        return $data;
    }

    protected function afterCreate(): void
    {
        $payment = $this->record;

        Notification::make()
            ->title('Paiement créé avec succès')
            ->body("Transaction {$payment->transaction_id} créée pour ".number_format($payment->amount / 100, 0, ',', ' ').' FCFA')
            ->success()
            ->send();

        // Si le paiement est terminé et lié à une facture, mettre à jour le statut de la facture
        if ($payment->status === 'completed' && $payment->invoice) {
            $invoice = $payment->invoice;
            $totalPaid = $invoice->payments()
                ->where('status', 'completed')
                ->sum('amount');

            if ($totalPaid >= $invoice->total_amount) {
                $invoice->update(['status' => 'paid']);

                Notification::make()
                    ->title('Facture marquée comme payée')
                    ->body("La facture {$invoice->invoice_number} est maintenant entièrement payée")
                    ->success()
                    ->send();
            }
        }
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }
}
