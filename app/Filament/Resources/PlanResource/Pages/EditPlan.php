<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Str;

class EditPlan extends EditRecord
{
    protected static string $resource = PlanResource::class;

    protected ?string $heading = 'Modifier le Plan';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer le plan')
                ->modalDescription(function () {
                    $subscriptionsCount = $this->record->subscriptions()->count();
                    if ($subscriptionsCount > 0) {
                        return "Attention : Ce plan a {$subscriptionsCount} abonnement(s) actif(s). La suppression pourrait affecter les entreprises utilisant ce plan.";
                    }

                    return 'Êtes-vous sûr de vouloir supprimer ce plan ?';
                })
                ->successNotificationTitle('Plan supprimé'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Plan modifié avec succès';
    }

    protected function mutateFormDataBeforeSave(array $data): array
    {
        // Générer automatiquement le slug si changement du nom
        if (! empty($data['name']) && ($data['name'] !== $this->record->name)) {
            if (empty($data['slug']) || $data['slug'] === Str::slug($this->record->name)) {
                $data['slug'] = Str::slug($data['name']);
            }
        }

        return $data;
    }

    public function getTitle(): string
    {
        return "Modifier : {$this->record->name}";
    }

    public function getSubheading(): string
    {
        $subscriptionsCount = $this->record->subscriptions()->count();

        return "{$subscriptionsCount} abonnement(s) utilise(nt) ce plan";
    }
}
