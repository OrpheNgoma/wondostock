<?php

namespace App\Filament\Resources\PlanResource\Pages;

use App\Filament\Resources\PlanResource;
use App\Models\Plan;
use Filament\Actions\Action;
use Filament\Actions\CreateAction;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ListRecords;

class ListPlans extends ListRecords
{
    protected static string $resource = PlanResource::class;

    protected ?string $heading = 'Gestion des Plans';

    protected ?string $subheading = 'Configuration des plans d\'abonnement SaaS';

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make()
                ->label('Nouveau Plan')
                ->icon('heroicon-o-plus')
                ->modalWidth('4xl'),

            // Action pour créer des plans par défaut
            Action::make('create_default_plans')
                ->label('Créer Plans par Défaut')
                ->icon('heroicon-o-sparkles')
                ->color('info')
                ->requiresConfirmation()
                ->modalHeading('Créer les plans par défaut')
                ->modalDescription('Cette action va créer les plans Essentiel, Pro et Entreprise avec leurs fonctionnalités de base.')
                ->action(function (): void {
                    $defaultPlans = [
                        [
                            'name' => 'Plan Essentiel',
                            'slug' => 'essentiel',
                            'description' => 'Plan de base pour les petites entreprises',
                            'price' => 1500000, // 15,000 FCFA
                            'user_limit' => 3,
                            'unlimited_users' => false,
                            'features' => [
                                'inventory_management' => 'Gestion d\'inventaire de base',
                                'sales_tracking' => 'Suivi des ventes',
                                'basic_reports' => 'Rapports de base',
                                'customer_management' => 'Gestion clients',
                            ],
                        ],
                        [
                            'name' => 'Plan Pro',
                            'slug' => 'pro',
                            'description' => 'Plan avancé pour les entreprises en croissance',
                            'price' => 3500000, // 35,000 FCFA
                            'user_limit' => 10,
                            'unlimited_users' => false,
                            'features' => [
                                'inventory_management' => 'Gestion d\'inventaire avancée',
                                'sales_tracking' => 'Suivi des ventes avancé',
                                'advanced_reports' => 'Rapports avancés',
                                'customer_management' => 'Gestion clients complète',
                                'multi_store' => 'Multi-magasins',
                                'barcode_support' => 'Support codes-barres',
                                'api_access' => 'Accès API',
                            ],
                        ],
                        [
                            'name' => 'Plan Entreprise',
                            'slug' => 'entreprise',
                            'description' => 'Plan complet pour les grandes entreprises',
                            'price' => 7500000, // 75,000 FCFA
                            'user_limit' => 50,
                            'unlimited_users' => false,
                            'features' => [
                                'inventory_management' => 'Gestion d\'inventaire complète',
                                'sales_tracking' => 'Suivi des ventes expert',
                                'premium_reports' => 'Rapports premium',
                                'customer_management' => 'CRM intégré',
                                'multi_store' => 'Multi-magasins illimités',
                                'barcode_support' => 'Support codes-barres avancé',
                                'api_access' => 'Accès API complet',
                                'white_labeling' => 'Marquage blanc',
                                'priority_support' => 'Support prioritaire',
                                'custom_integrations' => 'Intégrations personnalisées',
                            ],
                        ],
                    ];

                    $created = 0;
                    foreach ($defaultPlans as $planData) {
                        if (! Plan::where('slug', $planData['slug'])->exists()) {
                            Plan::create($planData);
                            $created++;
                        }
                    }

                    Notification::make()
                        ->title("{$created} plan(s) créé(s) avec succès")
                        ->success()
                        ->send();
                })
                ->visible(fn (): bool => Plan::count() === 0),
        ];
    }

    public function getTitle(): string
    {
        $planCount = Plan::count();

        return "Plans d'Abonnement ({$planCount})";
    }
}
