<?php

namespace App\Filament\Resources\FeatureLocks\Schemas;

use App\Enums\FeatureEnum;
use App\Models\Company;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Schemas\Schema;

/**
 * Configuration du formulaire de verrouillage de fonctionnalités
 *
 * Formulaire structuré pour la gestion granulaire des verrouillages :
 * - Sélection d'entreprise avec recherche
 * - Choix de fonctionnalité organisé par catégorie
 * - Configuration d'expiration optionnelle
 * - Validation intelligente et UX optimisée
 */
class FeatureLockForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->schema([
                // Section principale : Configuration du verrouillage
                Section::make('Configuration du Verrouillage')
                    ->description('Définir les paramètres du verrouillage de fonctionnalité')
                    ->icon('heroicon-o-lock-closed')
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Sélection de l'entreprise cible
                                Select::make('company_id')
                                    ->label('Entreprise')
                                    ->relationship('company', 'name')
                                    ->searchable(['name', 'email'])
                                    ->preload()
                                    ->required()
                                    ->placeholder('Sélectionner une entreprise')
                                    ->getOptionLabelFromRecordUsing(fn (Company $record): string => "{$record->name} ({$record->email})"
                                    )
                                    ->helperText('Entreprise concernée par le verrouillage')
                                    ->columnSpanFull(),

                                // Fonctionnalité à verrouiller
                                Select::make('feature_key')
                                    ->label('Fonctionnalité')
                                    ->options(self::getGroupedFeatureOptions())
                                    ->required()
                                    ->searchable()
                                    ->placeholder('Choisir une fonctionnalité')
                                    ->helperText('Fonctionnalité à verrouiller pour cette entreprise'),

                                // Statut du verrouillage
                                Toggle::make('is_locked')
                                    ->label('Verrouillage actif')
                                    ->default(true)
                                    ->onIcon('heroicon-m-lock-closed')
                                    ->offIcon('heroicon-m-lock-open')
                                    ->onColor('danger')
                                    ->offColor('success')
                                    ->helperText('Activer ou désactiver le verrouillage'),
                            ]),
                    ]),

                // Section temporalité : Gestion des dates
                Section::make('Temporalité')
                    ->description('Configuration de la durée du verrouillage')
                    ->icon('heroicon-o-clock')
                    ->collapsible()
                    ->schema([
                        Grid::make(2)
                            ->schema([
                                // Date d'expiration optionnelle
                                DateTimePicker::make('expires_at')
                                    ->label('Date d\'expiration')
                                    ->placeholder('Permanent si non défini')
                                    ->helperText('Le verrouillage sera automatiquement levé à cette date')
                                    ->displayFormat('d/m/Y H:i')
                                    ->format('Y-m-d H:i:s')
                                    ->timezone('Africa/Douala')
                                    ->minDate(now())
                                    ->suffixIcon('heroicon-m-calendar')
                                    ->suffixIconColor('primary'),

                                // Administrateur responsable
                                Select::make('locked_by_id')
                                    ->label('Administrateur responsable')
                                    ->relationship('lockedBy', 'name')
                                    ->searchable(['name', 'email'])
                                    ->placeholder('Admin actuel')
                                    ->default(auth()->id())
                                    ->helperText('Administrateur ayant effectué le verrouillage')
                                    ->disabled()
                                    ->dehydrated(),
                            ]),
                    ]),

                // Section justification : Raison et contexte
                Section::make('Justification')
                    ->description('Documenter la raison du verrouillage')
                    ->icon('heroicon-o-document-text')
                    ->collapsible()
                    ->schema([
                        // Raison détaillée
                        Textarea::make('reason')
                            ->label('Raison du verrouillage')
                            ->placeholder('Expliquer pourquoi cette fonctionnalité est verrouillée...')
                            ->helperText('Description claire pour faciliter la gestion future')
                            ->rows(4)
                            ->maxLength(1000)
                            ->columnSpanFull()
                            ->extraInputAttributes(['style' => 'resize: vertical']),
                    ]),
            ]);
    }

    /**
     * Options de fonctionnalités groupées par catégorie
     */
    private static function getGroupedFeatureOptions(): array
    {
        $grouped = [];

        foreach (FeatureEnum::cases() as $feature) {
            $category = self::getCategoryLabel($feature->getCategory());
            $grouped[$category][$feature->value] = $feature->getDescription();
        }

        // Trier les catégories et les fonctionnalités
        ksort($grouped);
        foreach ($grouped as &$features) {
            asort($features);
        }

        return $grouped;
    }

    /**
     * Libellés des catégories de fonctionnalités
     */
    private static function getCategoryLabel(string $category): string
    {
        return match ($category) {
            'products' => '📦 Gestion des Produits',
            'sales' => '💰 Ventes & Facturation',
            'stock' => '📊 Gestion des Stocks',
            'reports' => '📈 Rapports & Analyses',
            'settings' => '⚙️ Paramètres',
            'users' => '👥 Gestion Utilisateurs',
            'financial' => '💳 Module Financier',
            'integrations' => '🔗 Intégrations',
            'advanced' => '🚀 Fonctionnalités Avancées',
            default => '📋 '.ucfirst($category),
        };
    }
}
