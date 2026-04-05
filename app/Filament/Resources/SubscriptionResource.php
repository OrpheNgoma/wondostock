<?php

namespace App\Filament\Resources;

use App\Filament\Resources\SubscriptionResource\Pages;
use App\Models\Subscription;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Select;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class SubscriptionResource extends Resource
{
    protected static ?string $model = Subscription::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-credit-card';
    }

    public static function getNavigationLabel(): string
    {
        return 'Abonnements';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion SaaS';
    }

    public static function getNavigationSort(): ?int
    {
        return 40;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('status', 'active')->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $activeCount = static::getModel()::where('status', 'active')->count();
        $totalCount = static::getModel()::count();

        if ($totalCount === 0) {
            return 'gray';
        }

        $percentage = ($activeCount / $totalCount) * 100;

        if ($percentage >= 80) {
            return 'success';
        } elseif ($percentage >= 50) {
            return 'warning';
        } else {
            return 'danger';
        }
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Abonnement
                Select::make('company_id')
                    ->label('Entreprise')
                    ->relationship('company', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Sélectionner l\'entreprise abonnée')
                    ->columnSpan(['lg' => 6]),

                Select::make('plan_id')
                    ->label('Plan')
                    ->relationship('plan', 'name')
                    ->required()
                    ->searchable()
                    ->preload()
                    ->helperText('Sélectionner le plan d\'abonnement')
                    ->columnSpan(['lg' => 6]),

                Select::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'expired' => 'Expiré',
                        'suspended' => 'Suspendu',
                        'cancelled' => 'Annulé',
                    ])
                    ->default('active')
                    ->required()
                    ->helperText('Statut de l\'abonnement')
                    ->columnSpan(['lg' => 12]),

                // Période d'Abonnement
                DatePicker::make('starts_at')
                    ->label('Date de Début')
                    ->required()
                    ->default(now())
                    ->helperText('Date d\'activation de l\'abonnement')
                    ->columnSpan(['lg' => 6]),

                DatePicker::make('ends_at')
                    ->label('Date de Fin')
                    ->after('starts_at')
                    ->helperText('Date d\'expiration de l\'abonnement (optionnelle pour les abonnements permanents)')
                    ->columnSpan(['lg' => 6]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn ($record) => match ($record->plan?->slug) {
                        'essentiel' => 'info',
                        'pro' => 'warning',
                        'entreprise' => 'success',
                        default => 'gray'
                    }),

                BadgeColumn::make('status')
                    ->label('Statut')
                    ->colors([
                        'success' => 'active',
                        'warning' => 'inactive',
                        'danger' => fn ($state) => in_array($state, ['expired', 'cancelled']),
                        'secondary' => 'suspended',
                    ])
                    ->formatStateUsing(fn ($state) => match ($state) {
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'expired' => 'Expiré',
                        'suspended' => 'Suspendu',
                        'cancelled' => 'Annulé',
                        default => $state
                    }),

                TextColumn::make('starts_at')
                    ->label('Début')
                    ->date('d/m/Y')
                    ->sortable(),

                TextColumn::make('ends_at')
                    ->label('Fin')
                    ->date('d/m/Y')
                    ->placeholder('Permanent')
                    ->sortable(),

                TextColumn::make('plan.price')
                    ->label('Prix')
                    ->money('XOF', divideBy: 100)
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('status')
                    ->label('Statut')
                    ->options([
                        'active' => 'Actif',
                        'inactive' => 'Inactif',
                        'expired' => 'Expiré',
                        'suspended' => 'Suspendu',
                        'cancelled' => 'Annulé',
                    ]),

                SelectFilter::make('plan')
                    ->label('Plan')
                    ->relationship('plan', 'name'),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('activate')
                        ->label('Activer')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'active'])),

                    BulkAction::make('suspend')
                        ->label('Suspendre')
                        ->icon('heroicon-o-pause-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'suspended'])),

                    BulkAction::make('cancel')
                        ->label('Annuler')
                        ->icon('heroicon-o-x-circle')
                        ->color('danger')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['status' => 'cancelled'])),
                ]),
            ])
            ->defaultSort('created_at', 'desc');
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['company', 'plan']);
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
            'index' => Pages\ListSubscriptions::route('/'),
            'create' => Pages\CreateSubscription::route('/create'),
            'view' => Pages\ViewSubscription::route('/{record}'),
            'edit' => Pages\EditSubscription::route('/{record}/edit'),
        ];
    }
}
