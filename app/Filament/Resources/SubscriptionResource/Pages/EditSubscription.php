<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditSubscription extends EditRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected ?string $heading = 'Modifier l\'Abonnement';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer l\'abonnement')
                ->modalDescription('Êtes-vous sûr de vouloir supprimer cet abonnement ?')
                ->successNotificationTitle('Abonnement supprimé'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Abonnement modifié avec succès';
    }

    public function getTitle(): string
    {
        return "Modifier : {$this->record->company->name}";
    }

    public function getSubheading(): string
    {
        return "Plan : {$this->record->plan->name}";
    }
}
