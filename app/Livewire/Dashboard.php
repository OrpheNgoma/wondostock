<?php

namespace App\Livewire;

use App\Models\Product;
use Livewire\Component;
use App\Models\Customer;
use App\Models\Document;
use App\Enums\DocumentType;
use App\Enums\DocumentStatus;
use App\Traits\SecureCompanyAccess;
use Illuminate\Support\Carbon;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

#[Layout('components.layouts.app')]
#[Title('Tableau de Bord - KaziFlow')]
class Dashboard extends Component
{
    use SecureCompanyAccess;
    
    public string $period = '30';

    public function setPeriod(string $period)
    {
        $this->period = $period;
        $this->dispatch('periodChanged');
    }

    public function render()
    {
        $startDate = Carbon::now()->subDays((int)$this->period);
        $userCompany = $this->getSecureUserAndCompany();
        
        // Vérification de sécurité
        if (!$userCompany['valid']) {
            return view('livewire.dashboard', [
                'totalRevenue' => 0, 'totalSales' => 0, 'estimatedProfit' => 0,
                'newCustomersCount' => 0, 'lineChartLabels' => [], 'lineChartValues' => [],
                'donutChartLabels' => [], 'donutChartValues' => [], 'topProducts' => collect(),
                'lowStockProducts' => collect()
            ]);
        }

        $user = $userCompany['user'];
        $company = $userCompany['company'];
        $isGlobalView = Gate::allows('view_global_reports');

        // --- Requête optimisée unique pour tous les KPIs ---
        $kpiQuery = DB::table('documents')
            ->where('company_id', $company->id)
            ->where('type', DocumentType::Invoice->value)
            ->where('document_date', '>=', $startDate)
            ->whereIn('status', [
                DocumentStatus::Validated->value, 
                DocumentStatus::Paid->value, 
                DocumentStatus::PartiallyPaid->value
            ]);

        if (!$isGlobalView && $user->store_id) {
            $kpiQuery->where('store_id', $user->store_id);
        }

        // Récupération des données de base en une seule requête
        $kpiData = $kpiQuery->select([
            DB::raw('SUM(total_amount) as total_revenue'),
            DB::raw('COUNT(id) as total_sales'),
            DB::raw('DATE(document_date) as date'),
            DB::raw('GROUP_CONCAT(id) as document_ids')
        ])->groupBy('date')->get();

        $totalRevenue = $kpiData->sum('total_revenue');
        $totalSales = $kpiData->sum('total_sales');

        // --- Calcul optimisé du bénéfice estimé ---
        $documentIds = $kpiData->flatMap(fn($item) => explode(',', $item->document_ids))->unique();
        $estimatedProfit = 0;
        
        if ($documentIds->isNotEmpty()) {
            $estimatedProfit = DB::table('document_items')
                ->join('products', 'document_items.product_id', '=', 'products.id')
                ->whereIn('document_items.document_id', $documentIds)
                ->sum(DB::raw('document_items.quantity * (document_items.unit_price - COALESCE(products.purchase_price, 0))'));
        }

        // --- Calcul des nouveaux clients ---
        $newCustomersCount = Customer::where('company_id', $company->id)
            ->where('created_at', '>=', $startDate)
            ->count();
        
        // --- Préparation optimisée des données pour le graphique linéaire ---
        $salesData = $kpiData->pluck('total_revenue', 'date');
        $chartData = [];
        $date = clone $startDate;
        while ($date <= now()) {
            $formattedDate = $date->format('Y-m-d');
            $chartData[$formattedDate] = $salesData->get($formattedDate, 0);
            $date->addDay();
        }
        $lineChartLabels = array_keys($chartData);
        $lineChartValues = array_values($chartData);

        // --- Données pour le graphique Donut optimisées ---
        $salesByCategory = collect();
        if ($documentIds->isNotEmpty()) {
            $salesByCategory = DB::table('document_items')
                ->join('products', 'document_items.product_id', '=', 'products.id')
                ->join('categories', 'products.category_id', '=', 'categories.id')
                ->whereIn('document_items.document_id', $documentIds)
                ->select('categories.name', DB::raw('SUM(document_items.total_amount) as total'))
                ->groupBy('categories.name')
                ->orderByDesc('total')
                ->limit(5)
                ->get();
        }
        
        $donutChartLabels = $salesByCategory->pluck('name');
        $donutChartValues = $salesByCategory->pluck('total');

        // --- Top produits optimisé ---
        $topProducts = collect();
        if ($documentIds->isNotEmpty()) {
            $topProductsQuery = DB::table('document_items')
                ->join('products', 'document_items.product_id', '=', 'products.id')
                ->whereIn('document_items.document_id', $documentIds)
                ->select('products.name', 'products.sku', DB::raw('SUM(document_items.quantity) as total_quantity'))
                ->groupBy('products.id', 'products.name', 'products.sku')
                ->orderByDesc('total_quantity')
                ->limit(5);
            
            $topProducts = $topProductsQuery->get();
        }
        
        // --- Stock bas optimisé ---
        $lowStockQuery = Product::where('company_id', $company->id)
            ->where('is_active', true)
            ->with(['stores' => function($query) use ($user, $isGlobalView) {
                $query->whereRaw('quantity <= low_stock_threshold');
                if (!$isGlobalView && $user->store_id) {
                    $query->where('stores.id', $user->store_id);
                }
            }])
            ->limit(5);

        $lowStockProducts = $lowStockQuery->get()->filter(function ($product) {
            return $product->stores->isNotEmpty();
        });

        // On envoie les nouvelles données via un événement
        $this->dispatch('update-charts', [
            'lineLabels' => $lineChartLabels,
            'lineValues' => $lineChartValues,
            'donutLabels' => $donutChartLabels,
            'donutValues' => $donutChartValues,
        ]);

        return view('livewire.dashboard', [
            'totalRevenue' => $totalRevenue,
            'totalSales' => $totalSales,
            'estimatedProfit' => $estimatedProfit,
            'newCustomersCount' => $newCustomersCount,
            'lineChartLabels' => $lineChartLabels,
            'lineChartValues' => $lineChartValues,
            'donutChartLabels' => $donutChartLabels,
            'donutChartValues' => $donutChartValues,
            'topProducts' => $topProducts,
            'lowStockProducts' => $lowStockProducts,
        ]);
    }
}