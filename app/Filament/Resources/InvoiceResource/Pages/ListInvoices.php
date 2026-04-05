<?php

namespace App\Filament\Resources\InvoiceResource\Pages;

use App\Filament\Resources\InvoiceResource;
use App\Models\Invoice;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListInvoices extends ListRecords
{
    protected static string $resource = InvoiceResource::class;

    protected ?string $heading = 'Gestion des Factures';

    protected ?string $subheading = 'Suivi des factures et paiements des abonnements';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouvelle Facture')
                ->icon('heroicon-o-plus')
                ->modalWidth('4xl'),
        ];
    }

    public function getTitle(): string
    {
        $pendingCount = Invoice::whereIn('status', ['sent', 'overdue'])->count();
        $totalCount = Invoice::count();

        return "Factures ({$pendingCount}/{$totalCount} en attente)";
    }
}
