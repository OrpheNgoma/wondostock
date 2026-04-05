<?php

namespace App\Filament\Resources\FeatureLocks\Pages;

use App\Filament\Resources\FeatureLocks\FeatureLockResource;
use App\Models\FeatureLock;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Support\Facades\Auth;

/**
 * Page de liste des verrouillages de fonctionnalités
 *
 * Interface optimisée pour la gestion en lot des verrouillages :
 * - Actions rapides de création et gestion globale
 * - Statistiques en temps réel
 * - Raccourcis pour opérations courantes
 */
class ListFeatureLocks extends ListRecords
{
    protected static string $resource = FeatureLockResource::class;

    protected ?string $heading = 'Gestion des Verrouillages';

    protected ?string $subheading = 'Contrôle granulaire des fonctionnalités par entreprise';

    protected function getHeaderActions(): array
    {
        return [
            // Action de création standard
            CreateAction::make()
                ->label('Nouveau Verrouillage')
                ->icon('heroicon-o-plus')
                ->modalWidth('2xl'),

            // Action de déverrouillage en lot des expirés
            Action::make('unlock_expired')
                ->label('Déverrouiller Expirés')
                ->icon('heroicon-o-clock')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Déverrouiller les verrouillages expirés')
                ->modalDescription('Cette action va déverrouiller automatiquement tous les verrouillages dont la date d\'expiration est dépassée.')
                ->action(function (): void {
                    $count = FeatureLock::expired()->update([
                        'is_locked' => false,
                        'unlocked_at' => now(),
                        'unlocked_by_id' => Auth::id(),
                    ]);

                    Notification::make()
                        ->title("{$count} verrouillage(s) expiré(s) déverrouillé(s)")
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => FeatureLock::expired()->exists()),

            // Statistiques rapides
            Action::make('stats')
                ->label('Statistiques')
                ->icon('heroicon-o-chart-bar')
                ->color('info')
                ->modalContent(function () {
                    $totalLocks = FeatureLock::count();
                    $activeLocks = FeatureLock::active()->count();
                    $expiredLocks = FeatureLock::expired()->count();
                    $companiesWithLocks = FeatureLock::distinct('company_id')->count('company_id');

                    return view('components.stats-cards', compact(
                        'totalLocks', 'activeLocks', 'expiredLocks', 'companiesWithLocks'
                    ));
                })
                ->modalHeading('Statistiques des Verrouillages')
                ->modalSubmitAction(false)
                ->modalCancelActionLabel('Fermer'),
        ];
    }

    public function getTitle(): string
    {
        $activeCount = FeatureLock::active()->count();

        return "Verrouillages ({$activeCount} actifs)";
    }
}
