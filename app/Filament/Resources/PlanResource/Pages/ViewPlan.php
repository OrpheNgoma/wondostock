<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewPlan extends ViewRecord
{
    protected static string $resource = PlanResource::class;

    protected ?string $heading = 'Détails du Plan';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),

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
                ->successNotificationTitle('Plan supprimé')
                ->successRedirectUrl($this->getResource()::getUrl('index')),
        ];
    }

    public function getTitle(): string
    {
        return "Plan : {$this->record->name}";
    }

    public function getSubheading(): string
    {
        return "Slug : {$this->record->slug} • Prix : ".number_format($this->record->price / 100, 0, ',', ' ').' FCFA';
    }
}
