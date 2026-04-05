<?php

namespace App\Filament\Resources\Companies;

use App\Filament\Resources\Companies\Pages\CreateCompany;
use App\Filament\Resources\Companies\Pages\EditCompany;
use App\Filament\Resources\Companies\Pages\ListCompanies;
use App\Filament\Resources\Companies\Pages\ViewCompany;
use App\Models\Company;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CompanyResource extends Resource
{
    protected static ?string $model = Company::class;

    public static function getNavigationIcon(): ?string
    {
        return 'heroicon-o-building-office-2';
    }

    public static function getNavigationLabel(): string
    {
        return 'Entreprises';
    }

    public static function getNavigationGroup(): ?string
    {
        return 'Gestion SaaS';
    }

    public static function getNavigationSort(): ?int
    {
        return 10;
    }

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::where('is_active', true)->count();
    }

    public static function getNavigationBadgeColor(): ?string
    {
        $activeCount = static::getModel()::where('is_active', true)->count();
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
                // Informations de l'Entreprise
                TextInput::make('name')
                    ->label('Nom de l\'Entreprise')
                    ->required()
                    ->maxLength(255)
                    ->placeholder('Ex: Entreprise SARL')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('legal_name')
                    ->label('Raison Sociale')
                    ->maxLength(255)
                    ->placeholder('Raison sociale complète')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('email')
                    ->label('Email Principal')
                    ->email()
                    ->required()
                    ->maxLength(255)
                    ->placeholder('contact@entreprise.com')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('phone_number')
                    ->label('Téléphone')
                    ->tel()
                    ->maxLength(20)
                    ->placeholder('+221 XX XXX XX XX')
                    ->columnSpan(['lg' => 6]),

                // Informations Légales
                TextInput::make('rccm')
                    ->label('N° RCCM')
                    ->maxLength(50)
                    ->placeholder('Numéro RCCM')
                    ->columnSpan(['lg' => 6]),

                TextInput::make('nif')
                    ->label('N° NIF')
                    ->maxLength(50)
                    ->placeholder('Numéro d\'Identification Fiscale')
                    ->columnSpan(['lg' => 6]),

                Textarea::make('address')
                    ->label('Adresse')
                    ->maxLength(1000)
                    ->placeholder('Adresse complète de l\'entreprise')
                    ->columnSpan('full'),

                // Gestion
                Select::make('owner_id')
                    ->label('Propriétaire')
                    ->relationship('owner', 'name')
                    ->searchable()
                    ->preload()
                    ->helperText('Utilisateur propriétaire de cette entreprise')
                    ->columnSpan(['lg' => 6]),

                Toggle::make('is_active')
                    ->label('Entreprise Active')
                    ->default(true)
                    ->helperText('Désactiver pour suspendre l\'accès')
                    ->columnSpan(['lg' => 6]),
            ])->columns(12);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->label('Nom de l\'Entreprise')
                    ->searchable()
                    ->sortable()
                    ->weight('medium')
                    ->limit(30),

                TextColumn::make('email')
                    ->label('Email')
                    ->searchable()
                    ->sortable()
                    ->icon('heroicon-m-envelope')
                    ->copyable(),

                TextColumn::make('subscription.plan.name')
                    ->label('Plan')
                    ->badge()
                    ->color(fn ($record) => match ($record->subscription?->plan?->slug) {
                        'essentiel' => 'info',
                        'pro' => 'warning',
                        'entreprise' => 'success',
                        default => 'gray'
                    })
                    ->placeholder('Aucun plan'),

                BadgeColumn::make('is_active')
                    ->label('Statut')
                    ->colors([
                        'success' => true,
                        'danger' => false,
                    ])
                    ->formatStateUsing(fn ($state) => $state ? 'Actif' : 'Inactif'),

                TextColumn::make('total_users')
                    ->label('Utilisateurs')
                    ->getStateUsing(fn ($record) => $record->users()->count())
                    ->badge()
                    ->color('info'),

                TextColumn::make('total_products')
                    ->label('Produits')
                    ->getStateUsing(fn ($record) => $record->products()->count())
                    ->badge()
                    ->color('warning'),

                TextColumn::make('revenue')
                    ->label('Revenus')
                    ->getStateUsing(fn ($record) => $record->revenue)
                    ->money('XOF', divideBy: 100)
                    ->placeholder('0 FCFA'),

                TextColumn::make('last_login')
                    ->label('Dernière Activité')
                    ->getStateUsing(fn ($record) => $record->last_login)
                    ->placeholder('Aucune activité'),

                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('is_active')
                    ->label('Statut')
                    ->options([
                        true => 'Actif',
                        false => 'Inactif',
                    ]),

                SelectFilter::make('subscription.plan')
                    ->label('Plan')
                    ->relationship('subscription.plan', 'name'),

                Filter::make('has_subscription')
                    ->label('Avec Abonnement')
                    ->query(fn (Builder $query) => $query->whereHas('subscription'))
                    ->toggle(),

                Filter::make('no_subscription')
                    ->label('Sans Abonnement')
                    ->query(fn (Builder $query) => $query->whereDoesntHave('subscription'))
                    ->toggle(),
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
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => true])),

                    BulkAction::make('deactivate')
                        ->label('Désactiver')
                        ->icon('heroicon-o-x-circle')
                        ->color('warning')
                        ->requiresConfirmation()
                        ->action(fn (Collection $records) => $records->each->update(['is_active' => false])),

                    BulkAction::make('delete')
                        ->label('Supprimer sélection')
                        ->icon('heroicon-o-trash')
                        ->color('danger')
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
            'index' => ListCompanies::route('/'),
            'create' => CreateCompany::route('/create'),
            'view' => ViewCompany::route('/{record}'),
            'edit' => EditCompany::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->with(['subscription.plan', 'owner'])
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }
}
