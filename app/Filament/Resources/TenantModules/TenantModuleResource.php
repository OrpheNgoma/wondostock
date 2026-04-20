<?php

namespace App\Filament\Resources\TenantModules;

use App\Enums\ModuleKey;
use App\Filament\Resources\TenantModules\Pages\ListTenantModules;
use App\Models\TenantModule;
use App\Services\ModuleService;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class TenantModuleResource extends Resource
{
    protected static ?string $model = TenantModule::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-puzzle-piece';
    }

    public static function getNavigationLabel(): string
    {
        return 'Modules Tenants';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Configuration';
    }

    public static function getNavigationSort(): ?int
    {
        return 2;
    }

    public static function getModelLabel(): string
    {
        return 'Module';
    }

    public static function getPluralModelLabel(): string
    {
        return 'Modules';
    }

    public static function form(Schema $schema): Schema
    {
        return $schema->components([
            Select::make('company_id')
                ->label('Entreprise')
                ->relationship('company', 'name')
                ->searchable()
                ->preload()
                ->required(),

            Select::make('module_key')
                ->label('Module')
                ->options(
                    collect(ModuleKey::cases())
                        ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                        ->toArray()
                )
                ->required(),

            Toggle::make('is_enabled')
                ->label('Activé')
                ->default(false),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('module_key')
                    ->label('Module')
                    ->formatStateUsing(fn ($state) => $state instanceof ModuleKey ? $state->label() : $state)
                    ->badge()
                    ->color('primary'),

                TextColumn::make('module_key_category')
                    ->label('Catégorie')
                    ->getStateUsing(fn ($record) => $record->module_key instanceof ModuleKey ? $record->module_key->category() : '')
                    ->badge()
                    ->color('gray'),

                IconColumn::make('is_enabled')
                    ->label('Statut')
                    ->boolean()
                    ->trueIcon('heroicon-o-check-circle')
                    ->falseIcon('heroicon-o-x-circle')
                    ->trueColor('success')
                    ->falseColor('danger'),

                TextColumn::make('enabledBy.name')
                    ->label('Activé par')
                    ->placeholder('—')
                    ->toggleable(),

                TextColumn::make('enabled_at')
                    ->label('Activé le')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('—')
                    ->sortable()
                    ->toggleable(),
            ])
            ->filters([
                SelectFilter::make('company_id')
                    ->label('Entreprise')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('module_key')
                    ->label('Module')
                    ->options(
                        collect(ModuleKey::cases())
                            ->mapWithKeys(fn ($case) => [$case->value => $case->label()])
                            ->toArray()
                    ),
            ])
            ->actions([
                Action::make('toggle')
                    ->label(fn (TenantModule $record): string => $record->is_enabled ? 'Désactiver' : 'Activer')
                    ->icon(fn (TenantModule $record): string => $record->is_enabled
                        ? 'heroicon-o-x-circle'
                        : 'heroicon-o-check-circle')
                    ->color(fn (TenantModule $record): string => $record->is_enabled ? 'danger' : 'success')
                    ->requiresConfirmation()
                    ->modalHeading(fn (TenantModule $record): string => $record->is_enabled
                        ? "Désactiver « {$record->module_key->label()} »"
                        : "Activer « {$record->module_key->label()} »")
                    ->modalDescription(fn (TenantModule $record): string => $record->is_enabled
                        ? "Ce module sera désactivé pour {$record->company->name}."
                        : "Ce module sera activé pour {$record->company->name}.")
                    ->action(function (TenantModule $record): void {
                        $enabled = app(ModuleService::class)->toggle($record->company, $record->module_key);

                        Notification::make()
                            ->title($enabled
                                ? "Module « {$record->module_key->label()} » activé"
                                : "Module « {$record->module_key->label()} » désactivé")
                            ->color($enabled ? 'success' : 'warning')
                            ->send();
                    }),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ListTenantModules::route('/'),
        ];
    }
}
