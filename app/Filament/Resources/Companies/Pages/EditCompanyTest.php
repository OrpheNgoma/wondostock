<?php

namespace App\Filament\Resources\CompanyTests\Pages;

use App\Filament\Resources\CompanyTests\CompanyTestResource;
use Filament\Actions\DeleteAction;
use Filament\Resources\Pages\EditRecord;

class EditCompanyTest extends EditRecord
{
    protected static string $resource = CompanyTestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            DeleteAction::make(),
        ];
    }
}
