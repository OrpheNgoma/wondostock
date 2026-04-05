<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PlanResource\Pages;
use App\Models\Plan;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class PlanResource extends Resource
{
    protected static ?string $model = Plan::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-rectangle-stack';
    }

    public static function getNavigationLabel(): string
    {
        return 'Plans';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion SaaS';
    }

    public static function getNavigationSort(): ?int
    {
        return 30;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Informations du Plan
                TextInput::make('name')
                    ->label('Nom du Plan')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ex: Plan Pro')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('slug')
                    ->label('Slug')
                    ->required()
                    ->maxLength(255)
                    ->unique(ignoreRecord: true)
                    ->placeholder('Ex: plan-pro')
                    ->helperText('Identifiant unique pour le plan (généré automatiquement)')
                    ->columnSpan(['lg' => 6]),

                Textarea::make('description')
                    ->label('Description')
                    ->maxLength(1000)
                    ->placeholder('Description détaillée du plan...')
                    ->columnSpan('full'),

                TextInput::make('price')
                    ->label('Prix (FCFA)')
                    ->required()
                    ->numeric()
                    ->default(0)
                    ->helperText('Prix en centimes FCFA (ex: 1000 = 10 FCFA)')
                    ->columnSpan(['lg' => 6]),

                // Limitations et Fonctionnalités
                Toggle::make('unlimited_users')
                    ->label('Utilisateurs Illimités')
                    ->default(false)
                    ->reactive()
                    ->helperText('Si activé, ignore la limite d\'utilisateurs')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('user_limit')
                    ->label('Limite d\'Utilisateurs')
                    ->numeric()
                    ->default(1)
                    ->minValue(1)
                    ->hidden(fn (callable $get) => $get('unlimited_users'))
                    ->helperText('Nombre maximum d\'utilisateurs autorisés')
                    ->columnSpan(['lg' => 6]),

                Repeater::make('features')
                    ->label('Fonctionnalités')
                    ->schema([
                        TextInput::make('key')
                            ->label('Fonctionnalité')
                            ->required(),
                        TextInput::make('description')
                            ->label('Description')
                            ->required(),
                    ])
                    ->defaultItems(1)
                    ->addActionLabel('Ajouter une fonctionnalité')
                    ->helperText('Liste des fonctionnalités disponibles pour ce plan')
                    ->columnSpan('full'),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('slug')
                    ->label('Slug')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color('gray'),

                TextColumn::make('price')
                    ->label('Prix')
                    ->money('XOF', divideBy: 100)
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('user_limit')
                    ->label('Limite Utilisateurs')
                    ->formatStateUsing(function ($state, $record) {
                        return $record->unlimited_users ? 'Illimité' : $state;
                    })
                    ->badge()
                    ->color(fn ($record) => $record->unlimited_users ? 'success' : 'info'),

                TextColumn::make('subscriptions_count')
                    ->label('Abonnements')
                    ->counts('subscriptions')
                    ->badge()
                    ->color('warning'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('delete')
                        ->label('Supprimer sélection')
                        ->icon('heroicon-o-trash')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->delete()),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
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
            'index' => Pages\ListPlans::route('/'),
            'create' => Pages\CreatePlan::route('/create'),
            'view' => Pages\ViewPlan::route('/{record}'),
            'edit' => Pages\EditPlan::route('/{record}/edit'),
        ];
    }
}
