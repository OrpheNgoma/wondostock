<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\ViewAction;
use Filament\Resources\Pages\EditRecord;

class EditInvoice extends EditRecord
{
    protected static string $resource = InvoiceResource::class;

    protected ?string $heading = 'Modifier la Facture';

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make()
                ->icon('heroicon-o-eye'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer la facture')
                ->modalDescription('Êtes-vous sûr de vouloir supprimer cette facture ?')
                ->successNotificationTitle('Facture supprimée'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'Facture modifiée avec succès';
    }

    public function getTitle(): string
    {
        return "Modifier : {$this->record->invoice_number}";
    }

    public function getSubheading(): string
    {
        return "Entreprise : {$this->record->company->name}";
    }
}
