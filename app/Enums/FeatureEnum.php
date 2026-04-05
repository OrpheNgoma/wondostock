<?php

namespace App\Enums;

/**
 * Énumération des fonctionnalités verrouillables du système.
 *
 * Chaque fonctionnalité est identifiée par une clé unique qui permet
 * à l'admin global de verrouiller/déverrouiller l'accès pour une entreprise.
 */
enum FeatureEnum: string
{
    // === GESTION DES PRODUITS ===
    case PRODUCTS_VIEW = 'products.view';
    case PRODUCTS_MANAGE = 'products.manage';
    case PRODUCTS_ANALYTICS = 'products.analytics';
    case PRODUCTS_BARCODE = 'products.barcode';
    case PRODUCTS_CATEGORIES = 'products.categories';
    case PRODUCTS_SETTINGS = 'products.settings';
    case PRODUCTS_LABELS = 'products.labels';
    case PRODUCTS_VARIABLE = 'variable_products';

    // === GESTION DES STOCKS ===
    case INVENTORY_MANAGE = 'inventory.manage';
    case INVENTORY_ENTRY = 'inventory.entry';
    case INVENTORY_MOVEMENTS = 'inventory.movements';
    case INVENTORY_TRANSFERS = 'inventory.transfers';
    case INVENTORY_CRITICAL = 'inventory.critical';
    case INVENTORY_REPORTS = 'inventory.reports';
    case STOCK_MANAGE = 'stock.manage';

    // === VENTES ET DOCUMENTS ===
    case SALES_VIEW = 'sales.view';
    case SALES_CREATE = 'sales.create';
    case SALES_EDIT = 'sales.edit';
    case SALES_INVOICES = 'sales.invoices';
    case SALES_QUOTES = 'sales.quotes';
    case SALES_DELIVERY = 'sales.delivery';
    case SALES_CREDIT_NOTES = 'sales.credit_notes';
    case SALES_PDF = 'sales.pdf';

    // === ACHATS ET APPROVISIONNEMENTS ===
    case PURCHASES_VIEW = 'purchases.view';
    case PURCHASES_CREATE = 'purchases.create';
    case PURCHASES_EDIT = 'purchases.edit';
    case PURCHASES_ORDERS = 'purchases.orders';
    case PURCHASES_RECEPTION = 'purchases.reception';

    // === GESTION COMMERCIALE ===
    case CUSTOMERS_MANAGE = 'customers.manage';
    case SUPPLIERS_MANAGE = 'suppliers.manage';
    case COMMERCIAL_ANALYTICS = 'commercial.analytics';

    // === MULTI-MAGASINS ===
    case STORES_MANAGE = 'stores.manage';
    case STORES_ACTIVITY = 'stores.activity';
    case STORES_BRANCHES = 'stores.branches';
    case STORES_TRANSFERS = 'stores.transfers';

    // === RAPPORTS ET ANALYTICS ===
    case DASHBOARD_STATS = 'dashboard.stats';
    case REPORTS_SALES = 'reports.sales';
    case REPORTS_INVENTORY = 'reports.inventory';
    case REPORTS_FINANCIAL = 'reports.financial';
    case ANALYTICS_ADVANCED = 'analytics.advanced';

    // === ADMINISTRATION ===
    case USERS_MANAGE = 'users.manage';
    case ROLES_PERMISSIONS = 'roles.permissions';
    case COMPANY_SETTINGS = 'company.settings';
    case NUMBERING_SETTINGS = 'numbering.settings';
    case INVITATIONS_MANAGE = 'invitations.manage';

    // === FONCTIONNALITÉS SAAS ===
    case SUBSCRIPTION_MANAGE = 'subscription.manage';
    case BILLING_VIEW = 'billing.view';
    case PAYMENTS_MANAGE = 'payments.manage';

    /**
     * Retourne la description lisible de la fonctionnalité.
     */
    public function getDescription(): string
    {
        return match ($this) {
            // Produits
            self::PRODUCTS_VIEW => 'Consultation des produits',
            self::PRODUCTS_MANAGE => 'Gestion complète des produits',
            self::PRODUCTS_ANALYTICS => 'Analytics et rapports produits',
            self::PRODUCTS_BARCODE => 'Génération de codes-barres',
            self::PRODUCTS_CATEGORIES => 'Gestion des catégories',
            self::PRODUCTS_SETTINGS => 'Paramètres produits',
            self::PRODUCTS_LABELS => 'Impression d\'étiquettes',
            self::PRODUCTS_VARIABLE => 'Produits variables (variantes)',

            // Stocks
            self::INVENTORY_MANAGE => 'Gestion des inventaires',
            self::INVENTORY_ENTRY => 'Entrées de stock',
            self::INVENTORY_MOVEMENTS => 'Suivi des mouvements',
            self::INVENTORY_TRANSFERS => 'Transferts entre magasins',
            self::INVENTORY_CRITICAL => 'Alertes stocks critiques',
            self::INVENTORY_REPORTS => 'Rapports de stock',
            self::STOCK_MANAGE => 'Gestion globale du stock',

            // Ventes
            self::SALES_VIEW => 'Consultation des ventes',
            self::SALES_CREATE => 'Création de documents de vente',
            self::SALES_EDIT => 'Modification des ventes',
            self::SALES_INVOICES => 'Gestion des factures',
            self::SALES_QUOTES => 'Gestion des devis',
            self::SALES_DELIVERY => 'Bons de livraison',
            self::SALES_CREDIT_NOTES => 'Notes de crédit',
            self::SALES_PDF => 'Génération PDF',

            // Achats
            self::PURCHASES_VIEW => 'Consultation des achats',
            self::PURCHASES_CREATE => 'Création de commandes',
            self::PURCHASES_EDIT => 'Modification des achats',
            self::PURCHASES_ORDERS => 'Bons de commande',
            self::PURCHASES_RECEPTION => 'Réception marchandises',

            // Commercial
            self::CUSTOMERS_MANAGE => 'Gestion des clients',
            self::SUPPLIERS_MANAGE => 'Gestion des fournisseurs',
            self::COMMERCIAL_ANALYTICS => 'Analytics commercial',

            // Multi-magasins
            self::STORES_MANAGE => 'Gestion des magasins',
            self::STORES_ACTIVITY => 'Activité par magasin',
            self::STORES_BRANCHES => 'Succursales pays',
            self::STORES_TRANSFERS => 'Transferts inter-magasins',

            // Rapports
            self::DASHBOARD_STATS => 'Statistiques tableau de bord',
            self::REPORTS_SALES => 'Rapports de vente',
            self::REPORTS_INVENTORY => 'Rapports d\'inventaire',
            self::REPORTS_FINANCIAL => 'Rapports financiers',
            self::ANALYTICS_ADVANCED => 'Analytics avancées',

            // Administration
            self::USERS_MANAGE => 'Gestion des utilisateurs',
            self::ROLES_PERMISSIONS => 'Rôles et permissions',
            self::COMPANY_SETTINGS => 'Paramètres entreprise',
            self::NUMBERING_SETTINGS => 'Numérotation automatique',
            self::INVITATIONS_MANAGE => 'Gestion des invitations',

            // SaaS
            self::SUBSCRIPTION_MANAGE => 'Gestion abonnement',
            self::BILLING_VIEW => 'Consultation facturation',
            self::PAYMENTS_MANAGE => 'Gestion des paiements',
        };
    }

    /**
     * Retourne la catégorie de la fonctionnalité.
     */
    public function getCategory(): string
    {
        return match ($this) {
            self::PRODUCTS_VIEW, self::PRODUCTS_MANAGE, self::PRODUCTS_ANALYTICS,
            self::PRODUCTS_BARCODE, self::PRODUCTS_CATEGORIES, self::PRODUCTS_SETTINGS,
            self::PRODUCTS_LABELS, self::PRODUCTS_VARIABLE => 'Produits',

            self::INVENTORY_MANAGE, self::INVENTORY_ENTRY, self::INVENTORY_MOVEMENTS,
            self::INVENTORY_TRANSFERS, self::INVENTORY_CRITICAL, self::INVENTORY_REPORTS,
            self::STOCK_MANAGE => 'Stocks',

            self::SALES_VIEW, self::SALES_CREATE, self::SALES_EDIT, self::SALES_INVOICES,
            self::SALES_QUOTES, self::SALES_DELIVERY, self::SALES_CREDIT_NOTES,
            self::SALES_PDF => 'Ventes',

            self::PURCHASES_VIEW, self::PURCHASES_CREATE, self::PURCHASES_EDIT,
            self::PURCHASES_ORDERS, self::PURCHASES_RECEPTION => 'Achats',

            self::CUSTOMERS_MANAGE, self::SUPPLIERS_MANAGE, self::COMMERCIAL_ANALYTICS => 'Commercial',

            self::STORES_MANAGE, self::STORES_ACTIVITY, self::STORES_BRANCHES,
            self::STORES_TRANSFERS => 'Multi-magasins',

            self::DASHBOARD_STATS, self::REPORTS_SALES, self::REPORTS_INVENTORY,
            self::REPORTS_FINANCIAL, self::ANALYTICS_ADVANCED => 'Rapports',

            self::USERS_MANAGE, self::ROLES_PERMISSIONS, self::COMPANY_SETTINGS,
            self::NUMBERING_SETTINGS, self::INVITATIONS_MANAGE => 'Administration',

            self::SUBSCRIPTION_MANAGE, self::BILLING_VIEW, self::PAYMENTS_MANAGE => 'SaaS',
        };
    }

    /**
     * Retourne toutes les fonctionnalités groupées par catégorie.
     */
    public static function getGroupedFeatures(): array
    {
        $grouped = [];
        foreach (self::cases() as $feature) {
            $category = $feature->getCategory();
            $grouped[$category][] = [
                'key' => $feature->value,
                'description' => $feature->getDescription(),
                'enum' => $feature,
            ];
        }

        return $grouped;
    }

    /**
     * Retourne les fonctionnalités critiques qui ne peuvent pas être verrouillées.
     */
    public static function getCriticalFeatures(): array
    {
        return [
            self::USERS_MANAGE->value,
            self::COMPANY_SETTINGS->value,
            self::SUBSCRIPTION_MANAGE->value,
        ];
    }

    /**
     * Vérifie si une fonctionnalité est critique.
     */
    public function isCritical(): bool
    {
        return in_array($this->value, self::getCriticalFeatures());
    }
}
