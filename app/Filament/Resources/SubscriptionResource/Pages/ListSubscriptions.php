<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use App\Models\Subscription;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListSubscriptions extends ListRecords
{
    protected static string $resource = SubscriptionResource::class;

    protected ?string $heading = 'Gestion des Abonnements';

    protected ?string $subheading = 'Suivi des abonnements des entreprises aux plans SaaS';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvel Abonnement')
                ->icon('heroicon-o-plus')
                ->modalWidth('3xl'),

            // Action pour traiter les abonnements expirés
            Action::make('process_expired')
                ->label('Traiter Expirés')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Traiter les abonnements expirés')
                ->modalDescription('Cette action va marquer comme expirés tous les abonnements dont la date de fin est dépassée.')
                ->action(function (): void {
                    $count = Subscription::where('status', 'active')
                        ->whereNotNull('ends_at')
                        ->where('ends_at', '<', now())
                        ->update(['status' => 'expired']);

                    Notification::make()
                        ->title("{$count} abonnement(s) marqué(s) comme expiré(s)")
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => Subscription::where('status', 'active')
                    ->whereNotNull('ends_at')
                    ->where('ends_at', '<', now())
                    ->exists()),

            // Statistiques rapides
            Action::make('stats')
                ->label('Statistiques')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalContent(function () {
                    $totalSubscriptions = Subscription::count();
                    $activeSubscriptions = Subscription::where('status', 'active')->count();
                    $expiredSubscriptions = Subscription::where('status', 'expired')->count();
                    $suspendedSubscriptions = Subscription::where('status', 'suspended')->count();

                    return view('components.subscription-stats-cards', compact(
                        'totalSubscriptions', 'activeSubscriptions', 'expiredSubscriptions', 'suspendedSubscriptions'
                    ));
                })
                ->modalHeading('Statistiques des Abonnements')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fermer')
                ->modalWidth('3xl'),
        ];
    }

    public function getTitle(): string
    {
        $activeCount = Subscription::where('status', 'active')->count();
        $totalCount = Subscription::count();

        return "Abonnements ({$activeCount}/{$totalCount} actifs)";
    }
}
