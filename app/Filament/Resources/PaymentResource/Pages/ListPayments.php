<?php

namespace App\Filament\Resources\PaymentResource\Pages;

use App\Filament\Resources\PaymentResource;
use App\Models\Payment;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPayments extends ListRecords
{
    protected static string $resource = PaymentResource::class;

    protected ?string $heading = 'Gestion des Paiements';

    protected ?string $subheading = 'Suivi et traçabilité des paiements SaaS';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau Paiement')
                ->icon('heroicon-o-plus')
                ->modalWidth('4xl')
                ->mutateFormDataUsing(function (array $data): array {
                    // Générer automatiquement l'ID de transaction si pas fourni
                    if (empty($data['transaction_id'])) {
                        $data['transaction_id'] = 'TXN_'.now()->format('Ymd').'_'.str_pad(Payment::count() + 1, 3, '0', STR_PAD_LEFT);
                    }

                    return $data;
                }),

            // Action pour marquer les paiements en attente comme traités
            Action::make('process_pending')
                ->label('Traiter Paiements en Attente')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Traiter les paiements en attente')
                ->modalDescription('Cette action va marquer tous les paiements en attente comme étant en cours de traitement.')
                ->action(function (): void {
                    $updated = Payment::where('status', 'pending')->update([
                        'status' => 'processing',
                        'processed_at' => now(),
                    ]);

                    Notification::make()
                        ->title("{$updated} paiement(s) mis en traitement")
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => Payment::where('status', 'pending')->count() > 0),

            // Action pour calculer les revenus du jour
            Action::make('daily_revenue')
                ->label('Revenus du Jour')
                ->icon('heroicon-o-banknotes')
                ->color('success')
                ->action(function (): void {
                    $dailyRevenue = Payment::where('status', 'completed')
                        ->whereDate('paid_at', now()->toDateString())
                        ->sum('amount') / 100;

                    $dailyCount = Payment::where('status', 'completed')
                        ->whereDate('paid_at', now()->toDateString())
                        ->count();

                    Notification::make()
                        ->title('Revenus du jour')
                        ->body("{$dailyCount} paiements pour ".number_format($dailyRevenue, 0, ',', ' ').' FCFA')
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string
    {
        $paymentCount = Payment::count();
        $completedCount = Payment::where('status', 'completed')->count();

        return "Paiements ({$paymentCount}) - {$completedCount} terminés";
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // Widget pour afficher les statistiques rapides
        ];
    }
}
