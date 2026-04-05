<?php

namespace App\Filament\Resources\SimpleCompanies\Pages;

use App\Filament\Resources\SimpleCompanies\SimpleCompanyResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSimpleCompanies extends ManageRecords
{
    protected static string $resource = SimpleCompanyResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
