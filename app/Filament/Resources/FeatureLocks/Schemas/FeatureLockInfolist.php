<?php

namespace App\Filament\Resources\FeatureLocks\Schemas;

use App\Enums\FeatureEnum;
use Filament\Infolists\Components\Actions;
use Filament\Infolists\Components\Actions\Action;
use Filament\Infolists\Components\Grid;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use Filament\Schemas\Schema;
use Filament\Support\Enums\FontWeight;

/**
 * Configuration de la vue détaillée des verrouillages
 *
 * Interface d'information complète pour chaque verrouillage :
 * - Statut visuel avec indicateurs colorés
 * - Informations d'entreprise et fonctionnalité
 * - Historique et traçabilité
 * - Actions rapides depuis la vue
 */
class FeatureLockInfolist
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Section statut : Vue d'ensemble rapide
                Section::make('Statut du Verrouillage')
                    ->icon('heroicon-o-shield-check')
                    ->schema([
                        Grid::make(3)
                            ->schema([
                                // Statut principal avec badge
                                IconEntry::make('is_locked')
                                    ->label('Statut')
                                    ->boolean()
                                    ->trueIcon('heroicon-o-lock-closed')
                                    ->falseIcon('heroicon-o-lock-open')
                                    ->trueColor('danger')
                                    ->falseColor('success')
                                    ->size('xl'),

                                // Expiration avec indicateur
                                TextEntry::make('expires_at')
                                    ->label('Expiration')
                                    ->dateTime('d/m/Y H:i')
                                    ->placeholder('Permanent')
                                    ->badge()
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
                                            return 'heroicon-o-infinity';
                                        }

                                        $now = now();
                                        if ($state->isPast()) {
                                            return 'heroicon-o-exclamation-triangle';
                                        }
                                        if ($state->diffInDays($now) <= 7) {
                                            return 'heroicon-o-clock';
                                        }

                                        return 'heroicon-o-calendar';
                                    }),

                                // Temps restant calculé
                                TextEntry::make('expires_at')
                                    ->label('Temps restant')
                                    ->formatStateUsing(function ($state) {
                                        if (! $state) {
                                            return 'Permanent';
                                        }

                                        if ($state->isPast()) {
                                            return 'Expiré depuis '.$state->diffForHumans();
                                        }

                                        return 'Expire '.$state->diffForHumans();
                                    })
                                    ->color(function ($state) {
                                        if (! $state) {
                                            return 'gray';
                                        }

                                        return $state->isPast() ? 'danger' : 'info';
                                    }),
                            ]),
                    ]),

                // Section détails : Informations principales
                Section::make('Détails')
                    ->icon('heroicon-o-information-circle')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Entreprise concernée
                                TextEntry::make('company.name')
                                    ->label('Entreprise')
                                    ->weight(FontWeight::Bold)
                                    ->icon('heroicon-o-building-office-2')
                                    ->copyable()
                                    ->url(fn ($record) => $record->company ?
                                        route('filament.admin.resources.companies.view', $record->company) : null),

                                // Email de l'entreprise
                                TextEntry::make('company.email')
                                    ->label('Email')
                                    ->icon('heroicon-o-envelope')
                                    ->copyable(),

                                // Fonctionnalité avec description
                                TextEntry::make('feature_key')
                                    ->label('Fonctionnalité')
                                    ->formatStateUsing(function (string $state): string {
                                        $enum = FeatureEnum::tryFrom($state);

                                        return $enum ? $enum->getDescription() : $state;
                                    })
                                    ->badge()
                                    ->color('primary'),

                                // Clé technique
                                TextEntry::make('feature_key')
                                    ->label('Clé technique')
                                    ->copyable()
                                    ->icon('heroicon-o-code-bracket'),
                            ]),
                    ]),

                // Section justification : Raison et contexte
                Section::make('Justification')
                    ->icon('heroicon-o-document-text')
                    ->schema([
                        TextEntry::make('reason')
                            ->label('Raison du verrouillage')
                            ->placeholder('Aucune raison spécifiée')
                            ->markdown()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),

                // Section historique : Traçabilité
                Section::make('Historique')
                    ->icon('heroicon-o-clock')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Administrateur responsable
                                TextEntry::make('lockedBy.name')
                                    ->label('Verrouillé par')
                                    ->placeholder('Système')
                                    ->icon('heroicon-o-user')
                                    ->badge()
                                    ->color('gray'),

                                // Admin qui a déverrouillé
                                TextEntry::make('unlockedBy.name')
                                    ->label('Déverrouillé par')
                                    ->placeholder('Toujours verrouillé')
                                    ->icon('heroicon-o-user')
                                    ->badge()
                                    ->color('success'),

                                // Date de création
                                TextEntry::make('created_at')
                                    ->label('Créé le')
                                    ->dateTime('d/m/Y H:i')
                                    ->since()
                                    ->icon('heroicon-o-plus-circle'),

                                // Dernière modification
                                TextEntry::make('updated_at')
                                    ->label('Modifié le')
                                    ->dateTime('d/m/Y H:i')
                                    ->since()
                                    ->icon('heroicon-o-pencil'),
                            ]),
                    ])
                    ->collapsible(),

                // Actions rapides
                Actions::make([
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
                                $record->lock('Basculement rapide depuis la vue', auth()->user());
                            }
                        }),

                    Action::make('edit')
                        ->label('Modifier')
                        ->icon('heroicon-o-pencil-square')
                        ->color('primary')
                        ->url(fn ($record) => route('filament.admin.resources.feature-locks.edit', $record)),
                ]),
            ]);
    }
}
