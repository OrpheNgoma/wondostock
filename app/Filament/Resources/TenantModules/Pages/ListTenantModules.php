<?php

namespace App\Filament\Resources\TenantModules\Pages;

use App\Enums\ModuleKey;
use App\Filament\Resources\TenantModules\TenantModuleResource;
use App\Models\Company;
use App\Services\ModuleService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListTenantModules extends ListRecords
{
    protected static string $resource = TenantModuleResource::class;

    protected ?string $heading = 'Gestion des Modules';

    protected ?string $subheading = 'Activez ou désactivez les modules par entreprise';

    protected function getHeaderActions(): array
    {
        return [
            Action::make('sync_all')
                ->label('Initialiser toutes les companies')
                ->icon('heroicon-o-arrow-path')
                ->color('gray')
                ->requiresConfirmation()
                ->modalHeading('Initialiser les modules')
                ->modalDescription('Crée les entrées manquantes (désactivées) pour chaque company × module. N\'écrase pas les configurations existantes.')
                ->action(function (): void {
                    $service = app(ModuleService::class);
                    $count = 0;

                    Company::all()->each(function (Company $company) use ($service, &$count): void {
                        $service->syncDefaults($company);
                        $count++;
                    });

                    Notification::make()
                        ->title("{$count} entreprise(s) synchronisée(s)")
                        ->success()
                        ->send();
                }),

            Action::make('enable_module_all')
                ->label('Activer un module pour toutes')
                ->icon('heroicon-o-bolt')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('module_key')
                        ->label('Module à activer')
                        ->options(
                            collect(ModuleKey::cases())
                                ->mapWithKeys(fn ($case) => [$case->value => "{$case->label()} — {$case->description()}"])
                                ->toArray()
                        )
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $service = app(ModuleService::class);
                    $moduleKey = ModuleKey::from($data['module_key']);
                    $count = 0;

                    Company::all()->each(function (Company $company) use ($service, $moduleKey, &$count): void {
                        $service->enable($company, $moduleKey);
                        $count++;
                    });

                    Notification::make()
                        ->title("Module « {$moduleKey->label()} » activé pour {$count} entreprise(s)")
                        ->success()
                        ->send();
                }),
        ];
    }

    public function getTitle(): string
    {
        $total = \App\Models\TenantModule::where('is_enabled', true)->count();

        return "Modules ({$total} activés)";
    }
}
