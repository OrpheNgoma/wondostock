<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewSubscription extends ViewRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected ?string $heading = 'Détails de l\'Abonnement';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer l\'abonnement')
                ->modalDescription('Êtes-vous sûr de vouloir supprimer cet abonnement ?')
                ->successNotificationTitle('Abonnement supprimé'),
        ];
    }

    public function getTitle(): string
    {
        return "Abonnement : {$this->record->company->name}";
    }

    public function getSubheading(): string
    {
        return "Plan : {$this->record->plan->name} • Statut : {$this->record->status}";
    }
}
