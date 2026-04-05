<?php

namespace App\Filament\Resources\FeatureLocks\Pages;

use App\Filament\Resources\FeatureLocks\FeatureLockResource;
use Filament\Actions\Action;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ViewRecord;
use Illuminate\Support\Facades\Auth;

/**
 * Page de vue détaillée d'un verrouillage
 *
 * Interface de consultation complète avec actions contextuelles
 * et informations détaillées sur l'historique du verrouillage
 */
class ViewFeatureLock extends ViewRecord
{
    protected static string $resource = FeatureLockResource::class;

    protected ?string $heading = 'Détails du Verrouillage';

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
                ->modalHeading(function (): string {
                    return $this->record->is_locked ? 'Déverrouiller la fonctionnalité' : 'Verrouiller la fonctionnalité';
                })
                ->modalDescription(function (): string {
                    $action = $this->record->is_locked ? 'déverrouillera' : 'verrouillera';

                    return "Cette action {$action} la fonctionnalité '{$this->record->feature_key}' pour l'entreprise '{$this->record->company->name}'.";
                })
                ->action(function (): void {
                    if ($this->record->is_locked) {
                        $this->record->unlock();
                        Notification::make()
                            ->title('Fonctionnalité déverrouillée')
                            ->success()
                            ->send();
                    } else {
                        $this->record->lock('Verrouillage depuis la vue détaillée', Auth::user());
                        Notification::make()
                            ->title('Fonctionnalité verrouillée')
                            ->warning()
                            ->send();
                    }

                    // Refresh the record data
                    $this->record = $this->record->fresh();
                }),

            // Navigation vers l'entreprise
            Action::make('view_company')
                ->label('Voir l\'Entreprise')
                ->icon('heroicon-o-building-office-2')
                ->color('info')
                ->url(fn () => route('filament.admin.resources.companies.view', $this->record->company))
                ->openUrlInNewTab(),

            EditAction::make()
                ->icon('heroicon-o-pencil-square'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer le verrouillage')
                ->modalDescription(function (): string {
                    return "Êtes-vous sûr de vouloir supprimer ce verrouillage ? La fonctionnalité '{$this->record->feature_key}' redeviendra accessible pour '{$this->record->company->name}'.";
                })
                ->successNotificationTitle('Verrouillage supprimé')
                ->successRedirectUrl($this->getResource()::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return "Verrouillage : {$this->record->feature_key}";
    }

    public function getSubheading(): string
    {
        $status = $this->record->is_locked ? 'Verrouillé' : 'Déverrouillé';

        return "Entreprise : {$this->record->company->name} • Statut : {$status}";
    }
}
