<?php

namespace App\Filament\Resources\FeatureLocks\Pages;

use App\Filament\Resources\FeatureLocks\FeatureLockResource;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\Auth;

/**
 * Page de création de verrouillage de fonctionnalité
 *
 * Interface simplifiée pour créer rapidement des verrouillages
 * avec validation automatique et pré-remplissage intelligent
 */
class CreateFeatureLock extends CreateRecord
{
    protected static string $resource = FeatureLockResource::class;

    protected ?string $heading = 'Nouveau Verrouillage';

    protected ?string $subheading = 'Verrouiller une fonctionnalité pour une entreprise spécifique';

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Automatiquement remplir les champs de tracking
        $data['locked_at'] = now();
        $data['locked_by_id'] = Auth::id();

        return $data;
    }

    protected function getCreatedNotificationTitle(): ?string
    {
        return 'Verrouillage créé avec succès';
    }

    protected function afterCreate(): void
    {
        // Méthode disponible pour des actions personnalisées après création
        // Ex: notifications, cache invalidation, etc.
    }
}
