<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.admin')]
#[Title('Dashboard Administration Globale - WondoStock')]
class Dashboard extends Component
{
    public function mount()
    {
        if (!Auth::user()->is_global_admin) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function getSystemStatsProperty()
    {
        return [
            'total_companies' => Company::count(),
            'active_companies' => Company::where('is_active', true)->count(),
            'total_users' => User::whereHas('company')->count(),
            'total_subscriptions' => Subscription::where('status', 'active')->count(),
        ];
    }

    public function getRevenueStatsProperty()
    {
        $monthlyRevenue = Subscription::where('status', 'active')
            ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
            ->sum('plans.price');

        $annualRevenue = $monthlyRevenue * 12;

        return [
            'monthly_revenue' => $monthlyRevenue,
            'annual_revenue' => $annualRevenue,
            'average_revenue_per_company' => $this->system_stats['active_companies'] > 0 
                ? $monthlyRevenue / $this->system_stats['active_companies'] 
                : 0,
        ];
    }

    public function getPlanDistributionProperty()
    {
        return Plan::leftJoin('subscriptions', function ($join) {
            $join->on('plans.id', '=', 'subscriptions.plan_id')
                 ->where('subscriptions.status', '=', 'active');
        })
        ->select('plans.name', 'plans.slug', 'plans.price')
        ->selectRaw('COUNT(subscriptions.id) as subscription_count')
        ->groupBy('plans.id', 'plans.name', 'plans.slug', 'plans.price')
        ->orderBy('plans.price')
        ->get();
    }

    public function getRecentCompaniesProperty()
    {
        return Company::with(['owner', 'subscription.plan'])
            ->latest()
            ->limit(5)
            ->get();
    }

    public function getCompanyGrowthProperty()
    {
        $last6Months = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $count = Company::whereYear('created_at', $date->year)
                           ->whereMonth('created_at', $date->month)
                           ->count();
            
            $last6Months->push([
                'month' => $date->format('M Y'),
                'count' => $count,
            ]);
        }

        return $last6Months;
    }

    public function getSystemHealthProperty()
    {
        $totalCompanies = $this->system_stats['total_companies'];
        $activeCompanies = $this->system_stats['active_companies'];
        $withSubscriptions = $this->system_stats['total_subscriptions'];

        return [
            'activation_rate' => $totalCompanies > 0 ? ($activeCompanies / $totalCompanies) * 100 : 0,
            'subscription_rate' => $totalCompanies > 0 ? ($withSubscriptions / $totalCompanies) * 100 : 0,
            'average_users_per_company' => $activeCompanies > 0 ? $this->system_stats['total_users'] / $activeCompanies : 0,
        ];
    }

    public function render()
    {
        return view('livewire.admin.dashboard');
    }
}