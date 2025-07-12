<?php

namespace App\Services;

use App\Models\Analytics\Metric;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class MetricsService
{
    /**
     * Enregistrer une connexion utilisateur
     */
    public static function recordLogin(int $userId, int $companyId): void
    {
        Metric::record(
            type: 'user_login',
            category: 'user_activity',
            companyId: $companyId,
            data: ['user_id' => $userId]
        );
    }

    /**
     * Enregistrer la création d'un produit
     */
    public static function recordProductCreated(int $companyId, array $productData = []): void
    {
        Metric::record(
            type: 'product_created',
            category: 'business_activity',
            companyId: $companyId,
            data: $productData,
            value: 1
        );
    }

    /**
     * Enregistrer la création d'un document (facture, devis, etc.)
     */
    public static function recordDocumentCreated(int $companyId, string $documentType, float $amount = null): void
    {
        Metric::record(
            type: 'document_created',
            category: 'business_activity',
            companyId: $companyId,
            data: ['document_type' => $documentType],
            value: $amount
        );
    }

    /**
     * Enregistrer l'utilisation de storage
     */
    public static function recordStorageUsage(int $companyId, int $bytes): void
    {
        Metric::record(
            type: 'storage_used',
            category: 'system',
            companyId: $companyId,
            value: $bytes
        );
    }

    /**
     * Obtenir les statistiques d'activité pour l'admin global
     */
    public static function getGlobalActivityStats(int $days = 30): array
    {
        $start = now()->subDays($days)->startOfDay();
        $end = now()->endOfDay();

        return [
            'total_logins' => Metric::byType('user_login')->inPeriod($start, $end)->count(),
            'total_products_created' => Metric::byType('product_created')->inPeriod($start, $end)->count(),
            'total_documents_created' => Metric::byType('document_created')->inPeriod($start, $end)->count(),
            'total_revenue' => Metric::byType('document_created')->inPeriod($start, $end)->sum('value') ?? 0,
            'daily_activity' => self::getDailyActivity($start, $end),
            'top_active_companies' => self::getTopActiveCompanies($start, $end),
        ];
    }

    /**
     * Obtenir les statistiques d'une company spécifique
     */
    public static function getCompanyStats(int $companyId, int $days = 30): array
    {
        $start = now()->subDays($days)->startOfDay();
        $end = now()->endOfDay();

        return [
            'logins_count' => Metric::byType('user_login')->where('company_id', $companyId)->inPeriod($start, $end)->count(),
            'products_created' => Metric::byType('product_created')->where('company_id', $companyId)->inPeriod($start, $end)->count(),
            'documents_created' => Metric::byType('document_created')->where('company_id', $companyId)->inPeriod($start, $end)->count(),
            'revenue' => Metric::byType('document_created')->where('company_id', $companyId)->inPeriod($start, $end)->sum('value') ?? 0,
            'storage_used' => Metric::byType('storage_used')->where('company_id', $companyId)->latest()->value('value') ?? 0,
        ];
    }

    /**
     * Obtenir l'activité quotidienne
     */
    private static function getDailyActivity($start, $end): array
    {
        return Metric::select(
                DB::raw('DATE(recorded_at) as date'),
                DB::raw('COUNT(*) as total_actions'),
                DB::raw('COUNT(DISTINCT company_id) as active_companies')
            )
            ->inPeriod($start, $end)
            ->groupBy(DB::raw('DATE(recorded_at)'))
            ->orderBy('date')
            ->get()
            ->toArray();
    }

    /**
     * Obtenir les companies les plus actives
     */
    private static function getTopActiveCompanies($start, $end, int $limit = 10): array
    {
        return Metric::select('company_id')
            ->selectRaw('COUNT(*) as activity_count')
            ->with('company:id,name')
            ->inPeriod($start, $end)
            ->whereNotNull('company_id')
            ->groupBy('company_id')
            ->orderByDesc('activity_count')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    /**
     * Nettoyer les anciennes métriques (à exécuter périodiquement)
     */
    public static function cleanOldMetrics(int $daysToKeep = 365): int
    {
        $cutoffDate = now()->subDays($daysToKeep);
        return Metric::where('recorded_at', '<', $cutoffDate)->delete();
    }
}