<?php

namespace App\Filament\Resources\FeatureLocks;

use App\Filament\Resources\FeatureLocks\Pages\CreateFeatureLock;
use App\Filament\Resources\FeatureLocks\Pages\EditFeatureLock;
use App\Filament\Resources\FeatureLocks\Pages\ListFeatureLocks;
use App\Filament\Resources\FeatureLocks\Pages\ViewFeatureLock;
use App\Models\FeatureLock;
use App\Services\FeatureLockService;
use Filament\Actions\Action;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BooleanColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class FeatureLockResource extends Resource
{
    protected static ?string $model = FeatureLock::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-lock-closed';
    }

    public static function getNavigationLabel(): string
    {
        return 'Verrouillages';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion SaaS';
    }

    public static function getNavigationSort(): ?int
    {
        return 20;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::active()->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $activeCount = static::getModel()::active()->count();

        if ($activeCount === 0) {
            return 'success';
        } elseif ($activeCount <= 5) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                Select::make('company_id')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->label('Entreprise'),

                TextInput::make('feature_key')
                    ->required()
                    ->maxLength(255)
                    ->label('Clé de la fonctionnalité')
                    ->placeholder('Ex: advanced_reporting'),

                Toggle::make('is_locked')
                    ->default(true)
                    ->label('Verrouillé'),

                Textarea::make('reason')
                    ->maxLength(1000)
                    ->label('Raison du verrouillage')
                    ->placeholder('Décrivez pourquoi cette fonctionnalité est verrouillée'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->searchable()
                    ->sortable()
                    ->label('Entreprise'),

                TextColumn::make('feature_key')
                    ->searchable()
                    ->sortable()
                    ->label('Fonctionnalité'),

                BooleanColumn::make('is_locked')
                    ->label('Verrouillé')
                    ->trueIcon('heroicon-o-lock-closed')
                    ->falseIcon('heroicon-o-lock-open')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('reason')
                    ->limit(50)
                    ->tooltip(fn ($state): ?string => strlen($state ?? '') > 50 ? $state : null)
                    ->label('Raison'),

                TextColumn::make('locked_at')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->label('Verrouillé le'),

                TextColumn::make('expires_at')
                    ->dateTime('d/m/Y H:i')
                    ->placeholder('Permanent')
                    ->sortable()
                    ->label('Expire le'),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                Action::make('toggle_lock')
                    ->label(fn (FeatureLock $record): string => $record->is_locked ? 'Déverrouiller' : 'Verrouiller')
                    ->icon(fn (FeatureLock $record): string => $record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed')
                    ->color(fn (FeatureLock $record): string => $record->is_locked ? 'success' : 'danger')
                    ->requiresConfirmation()
                    ->modalHeading(fn (FeatureLock $record): string => $record->is_locked ? 'Déverrouiller la fonctionnalité' : 'Verrouiller la fonctionnalité')
                    ->modalDescription(fn (FeatureLock $record): string => $record->is_locked
                        ? "Voulez-vous déverrouiller la fonctionnalité '{$record->feature_key}' pour {$record->company->name} ?"
                        : "Voulez-vous verrouiller la fonctionnalité '{$record->feature_key}' pour {$record->company->name} ?"
                    )
                    ->action(function (FeatureLock $record) {
                        $featureLockService = app(FeatureLockService::class);

                        if ($record->is_locked) {
                            $featureLockService->unlockFeature($record->feature_key, $record->company);

                            Notification::make()
                                ->success()
                                ->title('Fonctionnalité déverrouillée')
                                ->body("La fonctionnalité '{$record->feature_key}' a été déverrouillée pour {$record->company->name}")
                                ->send();
                        } else {
                            $featureLockService->lockFeature($record->feature_key, $record->company, 'Verrouillé manuellement via l\'interface admin');

                            Notification::make()
                                ->success()
                                ->title('Fonctionnalité verrouillée')
                                ->body("La fonctionnalité '{$record->feature_key}' a été verrouillée pour {$record->company->name}")
                                ->send();
                        }
                    }),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('bulk_lock')
                        ->label('Verrouiller sélection')
                        ->icon('heroicon-o-lock-closed')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->modalHeading('Verrouiller les fonctionnalités sélectionnées')
                        ->modalDescription('Voulez-vous vraiment verrouiller toutes les fonctionnalités sélectionnées ?')
                        ->action(function (Collection $records) {
                            $featureLockService = app(FeatureLockService::class);
                            $count = 0;

                            foreach ($records as $record) {
                                if (! $record->is_locked) {
                                    $featureLockService->lockFeature($record->feature_key, $record->company, 'Verrouillé en lot via l\'interface admin');
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title('Fonctionnalités verrouillées')
                                ->body("{$count} fonctionnalité(s) ont été verrouillée(s)")
                                ->send();
                        }),
                    BulkAction::make('bulk_unlock')
                        ->label('Déverrouiller sélection')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->modalHeading('Déverrouiller les fonctionnalités sélectionnées')
                        ->modalDescription('Voulez-vous vraiment déverrouiller toutes les fonctionnalités sélectionnées ?')
                        ->action(function (Collection $records) {
                            $featureLockService = app(FeatureLockService::class);
                            $count = 0;

                            foreach ($records as $record) {
                                if ($record->is_locked) {
                                    $featureLockService->unlockFeature($record->feature_key, $record->company);
                                    $count++;
                                }
                            }

                            Notification::make()
                                ->success()
                                ->title('Fonctionnalités déverrouillées')
                                ->body("{$count} fonctionnalité(s) ont été déverrouillée(s)")
                                ->send();
                        }),
                    BulkAction::make('delete')
                        ->label('Supprimer sélection')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete()),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => ListFeatureLocks::route('/'),
            'create' => CreateFeatureLock::route('/create'),
            'view' => ViewFeatureLock::route('/{record}'),
            'edit' => EditFeatureLock::route('/{record}/edit'),
        ];
    }
}
