<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;

class ViewPayment extends ViewRecord
{
    protected static string $resource = PaymentResource::class;

    protected ?string $heading = null;

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->label('Modifier')
                ->icon('heroicon-o-pencil'),

            Action::make('mark_completed')
                ->label('Marquer Terminé')
                ->icon('heroicon-o-check-circle')
                ->color('success')
                ->requiresConfirmation()
                ->visible(fn (Payment $record): bool => $record->status !== 'completed')
                ->action(function (Payment $record): void {
                    $record->update([
                        'status' => 'completed',
                        'processed_at' => now(),
                    ]);

                    // Mettre à jour le statut de la facture si applicable
                    if ($record->invoice) {
                        $invoice = $record->invoice;
                        $totalPaid = $invoice->payments()
                            ->where('status', 'completed')
                            ->sum('amount');

                        if ($totalPaid >= $invoice->total_amount) {
                            $invoice->update(['status' => 'paid']);
                        }
                    }

                    Notification::make()
                        ->title('Paiement marqué comme terminé')
                        ->success()
                        ->send();
                }),

            Action::make('mark_failed')
                ->label('Marquer Échec')
                ->icon('heroicon-o-x-circle')
                ->color('danger')
                ->requiresConfirmation()
                ->visible(fn (Payment $record): bool => ! in_array($record->status, ['completed', 'failed']))
                ->action(function (Payment $record): void {
                    $record->update([
                        'status' => 'failed',
                        'processed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Paiement marqué comme échec')
                        ->warning()
                        ->send();
                }),

            Action::make('refund')
                ->label('Rembourser')
                ->icon('heroicon-o-arrow-uturn-left')
                ->color('warning')
                ->requiresConfirmation()
                ->visible(fn (Payment $record): bool => $record->status === 'completed')
                ->action(function (Payment $record): void {
                    $record->update([
                        'status' => 'refunded',
                        'processed_at' => now(),
                    ]);

                    Notification::make()
                        ->title('Paiement remboursé')
                        ->warning()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string
    {
        $payment = $this->record;

        return "Paiement {$payment->transaction_id}";
    }
}
