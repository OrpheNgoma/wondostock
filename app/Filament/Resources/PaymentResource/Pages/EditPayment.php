<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;

class EditPayment extends EditRecord
{
    protected static string $resource = PaymentResource::class;

    protected ?string $heading = null;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->label('Voir')
                ->icon('heroicon-o-eye'),

            Action::make('duplicate')
                ->label('Dupliquer')
                ->icon('heroicon-o-document-duplicate')
                ->color('gray')
                ->action(function (Payment $record): void {
                    $duplicateData = $record->toArray();

                    // Supprimer les champs uniques
                    unset($duplicateData['id']);
                    unset($duplicateData['created_at']);
                    unset($duplicateData['updated_at']);

                    // Générer un nouvel ID de transaction
                    $duplicateData['transaction_id'] = 'TXN_'.now()->format('Ymd_His').'_DUP_'.str_pad(Payment::count() + 1, 3, '0', STR_PAD_LEFT);

                    // Réinitialiser les statuts
                    $duplicateData['status'] = 'pending';
                    $duplicateData['processed_at'] = null;
                    $duplicateData['paid_at'] = now();

                    $duplicate = Payment::create($duplicateData);

                    Notification::make()
                        ->title('Paiement dupliqué')
                        ->body("Nouveau paiement créé: {$duplicate->transaction_id}")
                        ->success()
                        ->send();

                    $this->redirect($this->getResource()::getUrl('edit', ['record' => $duplicate]));
                }),
        ];
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Si le statut passe à "completed" et que processed_at n'est pas défini
        if ($data['status'] === 'completed' && empty($data['processed_at'])) {
            $data['processed_at'] = now();
        }

        // Si le statut n'est plus "completed", réinitialiser processed_at
        if ($data['status'] !== 'completed' && ! empty($data['processed_at'])) {
            $data['processed_at'] = null;
        }

        return $data;
    }

    protected function afterSave(): void
    {
        $payment = $this->record;

        // Si le paiement vient d'être marqué comme terminé et qu'il est lié à une facture
        if ($payment->status === 'completed' && $payment->invoice) {
            $invoice = $payment->invoice;
            $totalPaid = $invoice->payments()
                ->where('status', 'completed')
                ->sum('amount');

            // Vérifier si la facture est maintenant entièrement payée
            if ($totalPaid >= $invoice->total_amount && $invoice->status !== 'paid') {
                $invoice->update(['status' => 'paid']);

                Notification::make()
                    ->title('Facture mise à jour')
                    ->body("La facture {$invoice->invoice_number} est maintenant marquée comme payée")
                    ->success()
                    ->send();
            } elseif ($totalPaid < $invoice->total_amount && $invoice->status === 'paid') {
                // Si le paiement a été modifié et que la facture n'est plus entièrement payée
                $invoice->update(['status' => 'partially_paid']);

                Notification::make()
                    ->title('Facture mise à jour')
                    ->body("La facture {$invoice->invoice_number} est maintenant partiellement payée")
                    ->warning()
                    ->send();
            }
        }

        $this->record = $this->record->fresh();

        Notification::make()
            ->title('Paiement mis à jour')
            ->body("Transaction {$payment->transaction_id} mise à jour avec succès")
            ->success()
            ->send();
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('view', ['record' => $this->record]);
    }

    public function getTitle(): string
    {
        $payment = $this->record;

        return "Modifier le paiement {$payment->transaction_id}";
    }
}
