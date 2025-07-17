<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use App\Services\MetricsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GlobalAdminController extends Controller
{
    public function __construct()
    {
        // La vérification d'authentification sera gérée par les routes
    }

    /**
     * Vérifier que l'utilisateur est un admin global
     */
    private function ensureGlobalAdmin()
    {
        if (! auth()->check() || ! auth()->user()->is_global_admin) {
            abort(403, 'Accès réservé aux administrateurs globaux.');
        }
    }

    /**
     * Dashboard principal de l'admin global
     */
    public function dashboard()
    {
        $this->ensureGlobalAdmin();

        $stats = [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'total_users' => User::count(),
            'monthly_revenue' => $this->getMonthlyRevenue(),
            'recent_signups' => Company::orderBy('created_at', 'desc')->take(5)->get(),
            'top_companies' => $this->getTopCompaniesByUsage(),
        ];

        // Ajouter les métriques d'activité
        $activityStats = MetricsService::getGlobalActivityStats(30);
        $stats = array_merge($stats, $activityStats);

        return view('admin.dashboard', compact('stats'));
    }

    /**
     * Liste de toutes les companies
     */
    public function companies(Request $request)
    {
        $this->ensureGlobalAdmin();

        $query = Company::withCount('users')
            ->withoutGlobalScope(\App\Scopes\CompanyScope::class);

        if ($request->filled('search')) {
            $query->where('name', 'like', '%'.$request->search.'%')
                ->orWhere('email', 'like', '%'.$request->search.'%');
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $companies = $query->orderBy('created_at', 'desc')->paginate(20);

        return view('admin.companies.index', compact('companies'));
    }

    /**
     * Détails d'une company
     */
    public function showCompany(Company $company)
    {
        $this->ensureGlobalAdmin();

        $company->load(['users', 'subscription']);

        $metrics = [
            'users_count' => $company->users()->count(),
            'products_count' => $company->products()->count(),
            'sales_this_month' => $this->getCompanySalesThisMonth($company->id),
            'storage_used' => $this->getCompanyStorageUsed($company->id),
        ];

        // Ajouter les métriques d'activité spécifiques à cette company
        $activityMetrics = MetricsService::getCompanyStats($company->id, 30);
        $metrics = array_merge($metrics, $activityMetrics);

        return view('admin.companies.show', compact('company', 'metrics'));
    }

    /**
     * Activer/Désactiver une company
     */
    public function toggleCompanyStatus(Company $company)
    {
        $this->ensureGlobalAdmin();

        $company->update(['is_active' => ! $company->is_active]);

        $status = $company->is_active ? 'activée' : 'désactivée';

        return back()->with('success', "L'entreprise {$company->name} a été {$status}.");
    }

    /**
     * Statistiques système
     */
    public function systemStats()
    {
        $this->ensureGlobalAdmin();

        $stats = [
            'database_size' => $this->getDatabaseSize(),
            'storage_usage' => $this->getStorageUsage(),
            'system_performance' => $this->getSystemPerformance(),
            'recent_errors' => $this->getRecentErrors(),
        ];

        return view('admin.system-stats', compact('stats'));
    }

    /**
     * Méthodes privées pour les calculs
     */
    private function getMonthlyRevenue()
    {
        return Company::where('is_active', true)
            ->whereMonth('created_at', now()->month)
            ->count() * 50; // Exemple: 50€ par mois par company
    }

    private function getTopCompaniesByUsage()
    {
        return Company::withCount(['users', 'products'])
            ->orderByDesc('users_count')
            ->take(10)
            ->get();
    }

    private function getCompanySalesThisMonth($companyId)
    {
        // Logique pour calculer les ventes du mois
        return 0; // Placeholder
    }

    private function getCompanyStorageUsed($companyId)
    {
        // Logique pour calculer l'espace de stockage utilisé
        return '0 MB'; // Placeholder
    }

    private function getDatabaseSize()
    {
        try {
            $result = DB::select("SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 1) AS 'size_mb' FROM information_schema.tables WHERE table_schema = DATABASE()");

            return $result[0]->size_mb.' MB';
        } catch (\Exception $e) {
            return 'N/A';
        }
    }

    private function getStorageUsage()
    {
        // Logique pour calculer l'usage du stockage fichiers
        return '0 GB'; // Placeholder
    }

    private function getSystemPerformance()
    {
        return [
            'cpu_usage' => '15%',
            'memory_usage' => '45%',
            'disk_usage' => '30%',
        ];
    }

    private function getRecentErrors()
    {
        // Logique pour récupérer les erreurs récentes depuis les logs
        return [];
    }
}
