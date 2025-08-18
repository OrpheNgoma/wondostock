<?php

namespace App\Helpers;

class PlanHelper
{
    /**
     * Libellés des fonctionnalités en français
     */
    public static function getFeatureLabels(): array
    {
        return [
            'gestion_produits_ventes' => 'Gestion produits & ventes',
            'facturation' => 'Facturation',
            'roles_permissions' => 'Rôles & Permissions',
            'suivi_paiements' => 'Suivi paiements',
            'stock_de_base' => 'Stock de base',
            'multi_magasins' => 'Multi-magasins',
            'statistiques_avancees' => 'Statistiques avancées',
            'transferts_de_stock' => 'Transferts de stock',
            'support_prioritaire' => 'Support prioritaire',
            'api_integration' => 'API d\'intégration',
            'onboarding_personnalise' => 'Onboarding personnalisé',
        ];
    }

    /**
     * Traduit une fonctionnalité en français
     */
    public static function translateFeature(string $feature): string
    {
        $labels = self::getFeatureLabels();

        return $labels[$feature] ?? ucfirst(str_replace('_', ' ', $feature));
    }

    /**
     * Formate le prix d'un plan
     */
    public static function formatPrice(float $price): string
    {
        if ($price <= 0) {
            return 'Sur Devis';
        }

        return number_format($price, 0, ',', ' ').' XAF';
    }

    /**
     * Formate la limite d'utilisateurs
     */
    public static function formatUserLimit(bool $unlimited, int $userLimit): string
    {
        if ($unlimited) {
            return 'Utilisateurs illimités';
        }

        return 'Jusqu\'à '.$userLimit.' utilisateur'.($userLimit > 1 ? 's' : '');
    }

    /**
     * Retourne les informations de limite de magasins selon le plan
     */
    public static function getStoreLimit(string $planSlug): string
    {
        switch ($planSlug) {
            case 'essentiel':
                return '1 magasin';
            case 'pro':
                return 'Jusqu\'à 3 magasins';
            case 'entreprise':
                return 'Magasins illimités';
            default:
                return 'Non défini';
        }
    }

    /**
     * Retourne la description du plan selon son slug
     */
    public static function getPlanTargetDescription(string $planSlug): string
    {
        switch ($planSlug) {
            case 'essentiel':
                return 'Idéal pour TPE, Indépendants, 1 magasin';
            case 'pro':
                return 'PME en croissance, multi-sites';
            case 'entreprise':
                return 'Grandes PME, besoins spécifiques';
            default:
                return 'Plan non défini';
        }
    }

    /**
     * Retourne les prix par périodicité pour un plan
     */
    public static function getPlanPricing(string $planSlug): array
    {
        switch ($planSlug) {
            case 'essentiel':
                return [
                    'monthly' => 25000,
                    'quarterly' => 70000,
                    'yearly' => 250000,
                    'yearly_discount' => '2 mois offerts',
                ];
            case 'pro':
                return [
                    'monthly' => 55000,
                    'quarterly' => 155000,
                    'yearly' => 550000,
                    'yearly_discount' => '2 mois offerts',
                ];
            case 'entreprise':
                return [
                    'monthly' => 0,
                    'quarterly' => 0,
                    'yearly' => 0,
                    'yearly_discount' => null,
                ];
            default:
                return [
                    'monthly' => 0,
                    'quarterly' => 0,
                    'yearly' => 0,
                    'yearly_discount' => null,
                ];
        }
    }
}
