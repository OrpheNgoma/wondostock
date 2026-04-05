<?php

namespace App\Filament\Resources\FeatureLocks\Pages;

use App\Filament\Resources\FeatureLocks\FeatureLockResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\Auth;

/**
 * Page d'édition de verrouillage de fonctionnalité
 *
 * Interface complète pour modifier les paramètres d'un verrouillage
 * avec actions contextuelles et historique des modifications
 */
class EditFeatureLock extends EditRecord
{
    protected static string $resource = FeatureLockResource::class;

    protected ?string $heading = 'Modifier le Verrouillage';

    protected function getHeaderActions(): array
    {
        return [
            // Action de basculement rapide
            Action::make('toggle_lock')
                ->label(function (): string {
                    return $this->record->is_locked ? 'Déverrouiller' : 'Verrouiller';
                })
                ->icon(function (): string {
                    return $this->record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed';
                })
                ->color(function (): string {
                    return $this->record->is_locked ? 'success' : 'danger';
                })
                ->requiresConfirmation()
                ->action(function (): void {
                    if ($this->record->is_locked) {
                        $this->record->unlock();
                        Notification::make()
                            ->title('Fonctionnalité déverrouillée')
                            ->success()
                            ->send();
                    } else {
                        $this->record->lock('Verrouillage via édition', Auth::user());
                        Notification::make()
                            ->title('Fonctionnalité verrouillée')
                            ->warning()
                            ->send();
                    }

                    // Refresh the record and form data
                    $this->record = $this->record->fresh();
                }),

            ViewAction::make()
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer le verrouillage')
                ->modalDescription('Cette action supprimera définitivement ce verrouillage. La fonctionnalité redeviendra accessible pour l\'entreprise.')
                ->successNotificationTitle('Verrouillage supprimé'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Verrouillage modifié avec succès';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Tracker les modifications
        if (isset($data['is_locked']) && $data['is_locked'] !== $this->record->is_locked) {
            if ($data['is_locked']) {
                $data['locked_at'] = now();
                $data['locked_by_id'] = Auth::id();
                $data['unlocked_at'] = null;
                $data['unlocked_by_id'] = null;
            } else {
                $data['unlocked_at'] = now();
                $data['unlocked_by_id'] = Auth::id();
            }
        }

        return $data;
    }

    protected function afterSave(): void
    {
        // Méthode disponible pour des actions personnalisées après sauvegarde
        // Ex: notifications, cache invalidation, logs personnalisés, etc.
    }
}
