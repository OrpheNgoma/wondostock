<?php

namespace App\Filament\Resources\FeatureLocks\Tables;

use App\Enums\FeatureEnum;
use Filament\Actions\Action;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Support\Enums\FontWeight;
use Filament\Tables\Columns\BadgeColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;

/**
 * Configuration du tableau des verrouillages de fonctionnalités
 *
 * Interface optimisée pour la gestion des verrouillages avec :
 * - Colonnes informatives avec badges de statut
 * - Filtres avancés par entreprise, fonctionnalité et statut
 * - Actions rapides de verrouillage/déverrouillage
 * - Performance optimisée avec eager loading
 */
class FeatureLocksTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                // Entreprise concernée
                TextColumn::make('company.name')
                    ->label('Entreprise')
                    ->searchable(['company.name', 'company.email'])
                    ->sortable()
                    ->weight(FontWeight::Medium)
                    ->description(fn ($record) => $record->company?->email),

                // Fonctionnalité avec description lisible
                TextColumn::make('feature_key')
                    ->label('Fonctionnalité')
                    ->searchable()
                    ->sortable()
                    ->formatStateUsing(function (string $state): string {
                        $enum = FeatureEnum::tryFrom($state);

                        return $enum ? $enum->getDescription() : $state;
                    })
                    ->description(fn (string $state): string => $state),

                // Statut du verrouillage avec badge
                BadgeColumn::make('is_locked')
                    ->label('Statut')
                    ->formatStateUsing(function (bool $state, $record): string {
                        if (! $state) {
                            return 'Déverrouillé';
                        }

                        if ($record->expires_at && $record->expires_at->isPast()) {
                            return 'Expiré';
                        }

                        return 'Verrouillé';
                    })
                    ->colors([
                        'success' => 'Déverrouillé',
                        'danger' => 'Verrouillé',
                        'warning' => 'Expiré',
                    ])
                    ->icons([
                        'heroicon-o-lock-open' => 'Déverrouillé',
                        'heroicon-o-lock-closed' => 'Verrouillé',
                        'heroicon-o-clock' => 'Expiré',
                    ])
                    ->sortable(),

                // Raison du verrouillage
                TextColumn::make('reason')
                    ->label('Raison')
                    ->limit(50)
                    ->tooltip(function (TextColumn $column): ?string {
                        $state = $column->getState();

                        return strlen($state) > 50 ? $state : null;
                    })
                    ->placeholder('Aucune raison spécifiée')
                    ->toggleable(),

                // Date d'expiration avec indicateur visuel
                TextColumn::make('expires_at')
                    ->label('Expire le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->color(function ($state) {
                        if (! $state) {
                            return 'gray';
                        }

                        $now = now();
                        if ($state->isPast()) {
                            return 'danger';
                        }
                        if ($state->diffInDays($now) <= 7) {
                            return 'warning';
                        }

                        return 'success';
                    })
                    ->icon(function ($state) {
                        if (! $state) {
                            return 'heroicon-o-minus';
                        }

                        $now = now();
                        if ($state->isPast()) {
                            return 'heroicon-o-exclamation-triangle';
                        }
                        if ($state->diffInDays($now) <= 7) {
                            return 'heroicon-o-clock';
                        }

                        return 'heroicon-o-calendar';
                    })
                    ->placeholder('Permanent')
                    ->toggleable(),

                // Administrateur responsable
                TextColumn::make('lockedBy.name')
                    ->label('Verrouillé par')
                    ->placeholder('Système')
                    ->toggleable(),

                // Date de création
                TextColumn::make('created_at')
                    ->label('Créé le')
                    ->dateTime('d/m/Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                // Filtre par entreprise
                SelectFilter::make('company')
                    ->label('Entreprise')
                    ->relationship('company', 'name')
                    ->searchable()
                    ->preload()
                    ->placeholder('Toutes les entreprises'),

                // Filtre par statut
                SelectFilter::make('is_locked')
                    ->label('Statut')
                    ->options([
                        1 => 'Verrouillé',
                        0 => 'Déverrouillé',
                    ])
                    ->placeholder('Tous les statuts'),

                // Filtre par catégorie de fonctionnalité
                SelectFilter::make('feature_category')
                    ->label('Catégorie')
                    ->options([
                        'products' => 'Produits',
                        'sales' => 'Ventes',
                        'stock' => 'Stock',
                        'reports' => 'Rapports',
                        'settings' => 'Paramètres',
                        'users' => 'Utilisateurs',
                        'financial' => 'Financier',
                        'integrations' => 'Intégrations',
                        'advanced' => 'Avancé',
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query->when(
                            $data['value'],
                            fn (Builder $query, $category): Builder => $query->where('feature_key', 'like', $category.'.%')
                        );
                    })
                    ->placeholder('Toutes les catégories'),

                // Filtre par verrouillages expirés
                Filter::make('expired')
                    ->label('Verrouillages expirés')
                    ->toggle()
                    ->query(fn (Builder $query): Builder => $query->expired()),
            ])
            ->actions([
                // Action rapide de basculement
                Action::make('toggle_lock')
                    ->label(function ($record): string {
                        return $record->is_locked ? 'Déverrouiller' : 'Verrouiller';
                    })
                    ->icon(function ($record): string {
                        return $record->is_locked ? 'heroicon-o-lock-open' : 'heroicon-o-lock-closed';
                    })
                    ->color(function ($record): string {
                        return $record->is_locked ? 'success' : 'danger';
                    })
                    ->requiresConfirmation()
                    ->action(function ($record): void {
                        if ($record->is_locked) {
                            $record->unlock();
                        } else {
                            $record->lock('Verrouillage rapide via admin', auth()->user());
                        }
                    }),

                ViewAction::make()
                    ->icon('heroicon-o-eye'),
                EditAction::make()
                    ->icon('heroicon-o-pencil-square'),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    // Action en lot pour déverrouiller
                    \Filament\Actions\BulkAction::make('bulk_unlock')
                        ->label('Déverrouiller sélectionnés')
                        ->icon('heroicon-o-lock-open')
                        ->color('success')
                        ->requiresConfirmation()
                        ->action(function ($records): void {
                            foreach ($records as $record) {
                                if ($record->is_locked) {
                                    $record->unlock();
                                }
                            }
                        }),

                    DeleteBulkAction::make()
                        ->icon('heroicon-o-trash'),
                ]),
            ])
            ->defaultSort('created_at', 'desc')
            ->persistSortInSession()
            ->persistSearchInSession()
            ->persistFiltersInSession()
            ->striped()
            ->paginated([10, 25, 50, 100]);
    }
}
