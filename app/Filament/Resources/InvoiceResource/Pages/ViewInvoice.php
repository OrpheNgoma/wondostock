<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use Filament\Actions\DeleteAction;
use Filament\Actions\EditAction;
use Filament\Resources\Pages\ViewRecord;

class ViewInvoice extends ViewRecord
{
    protected static string $resource = InvoiceResource::class;

    protected ?string $heading = 'Détails de la Facture';

    protected function getHeaderActions(): array
    {
        return [
            EditAction::make()
                ->icon('heroicon-o-pencil-square'),

            DeleteAction::make()
                ->icon('heroicon-o-trash')
                ->requiresConfirmation()
                ->modalHeading('Supprimer la facture')
                ->modalDescription('Êtes-vous sûr de vouloir supprimer cette facture ?')
                ->successNotificationTitle('Facture supprimée'),
        ];
    }

    public function getTitle(): string
    {
        return "Facture : {$this->record->invoice_number}";
    }

    public function getSubheading(): string
    {
        return "Entreprise : {$this->record->company->name} • Statut : {$this->record->status}";
    }
}
