<?php

namespace App\Filament\Resources\SubscriptionResource\Pages;

use App\Filament\Resources\SubscriptionResource;
use Carbon\Carbon;
use Filament\Resources\Pages\CreateRecord;

class CreateSubscription extends CreateRecord
{
    protected static string $resource = SubscriptionResource::class;

    protected ?string $heading = 'Nouvel Abonnement';

    protected ?string $subheading = 'Créer un abonnement pour une entreprise';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Si pas de date de fin spécifiée et que c'est un abonnement mensuel, calculer automatiquement
        if (empty($data['ends_at']) && ! empty($data['starts_at'])) {
            $startDate = Carbon::parse($data['starts_at']);
            $data['ends_at'] = $startDate->copy()->addMonth();
        }

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Abonnement créé avec succès';
    }

    protected function afterCreate(): void
    {
        // Ici on pourrait déclencher d'autres actions comme :
        // - Génération d'une facture
        // - Envoi d'un email de bienvenue
        // - Activation des fonctionnalités
    }
}
