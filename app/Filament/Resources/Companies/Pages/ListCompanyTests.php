<?php

namespace App\Filament\Resources\CompanyTests\Pages;

use App\Filament\Resources\CompanyTests\CompanyTestResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;

class ListCompanyTests extends ListRecords
{
    protected static string $resource = CompanyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
