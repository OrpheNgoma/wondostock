<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Gestion des Abonnements - WondoStock Admin')]
class SubscriptionsIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $planFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    // Modal de création/édition d'abonnement
    public bool $showModal = false;

    public ?int $editingSubscription = null;

    public ?int $company_id = null;

    public ?int $plan_id = null;

    public ?string $starts_at = null;

    public ?string $ends_at = null;

    public string $status = 'active';

    // Modal de création d'abonnement rapide
    public bool $showQuickCreateModal = false;

    public string $companySearch = '';

    public ?int $selectedCompanyId = null;

    public function mount()
    {
        if (! Auth::user()->is_global_admin) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function updatedStatusFilter()
    {
        $this->resetPage();
    }

    public function updatedPlanFilter()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortBy === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortBy = $field;
            $this->sortDirection = 'asc';
        }
        $this->resetPage();
    }

    public function createSubscription()
    {
        $this->resetForm();
        $this->starts_at = now()->format('Y-m-d');
        $this->showModal = true;
    }

    public function quickCreateSubscription()
    {
        $this->resetForm();
        $this->companySearch = '';
        $this->selectedCompanyId = null;
        $this->starts_at = now()->format('Y-m-d');
        $this->showQuickCreateModal = true;
    }

    public function editSubscription(Subscription $subscription)
    {
        $this->editingSubscription = $subscription->id;
        $this->company_id = $subscription->company_id;
        $this->plan_id = $subscription->plan_id;
        $this->starts_at = $subscription->starts_at?->format('Y-m-d');
        $this->ends_at = $subscription->ends_at?->format('Y-m-d');
        $this->status = $subscription->status;
        $this->showModal = true;
    }

    public function saveSubscription()
    {
        // Gérer la création rapide où selectedCompanyId est utilisé
        if ($this->showQuickCreateModal && $this->selectedCompanyId) {
            $this->company_id = $this->selectedCompanyId;
        }

        $this->validate([
            'company_id' => 'required|exists:companies,id',
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'status' => 'required|in:active,expired,cancelled',
        ]);

        $data = [
            'company_id' => $this->company_id,
            'plan_id' => $this->plan_id,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'status' => $this->status,
        ];

        if ($this->editingSubscription) {
            $subscription = Subscription::find($this->editingSubscription);

            // Désactiver l'ancien abonnement de l'entreprise s'il change
            if ($subscription->company_id !== $this->company_id) {
                $oldCompanySubscriptions = Subscription::where('company_id', $subscription->company_id)
                    ->where('id', '!=', $subscription->id)
                    ->where('status', 'active')
                    ->get();

                foreach ($oldCompanySubscriptions as $oldSub) {
                    $oldSub->update(['status' => 'cancelled']);
                }
            }

            // Désactiver les autres abonnements de la nouvelle entreprise
            if ($this->status === 'active') {
                Subscription::where('company_id', $this->company_id)
                    ->where('id', '!=', $subscription->id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);
            }

            $subscription->update($data);
            $message = 'Abonnement mis à jour avec succès.';
        } else {
            // Désactiver les abonnements actifs existants pour cette entreprise
            if ($this->status === 'active') {
                Subscription::where('company_id', $this->company_id)
                    ->where('status', 'active')
                    ->update(['status' => 'cancelled']);
            }

            Subscription::create($data);
            $message = 'Abonnement créé avec succès.';
        }

        $this->dispatch('notify', message: $message, type: 'success');

        $this->closeModal();
    }

    public function cancelSubscription(Subscription $subscription)
    {
        $subscription->update(['status' => 'cancelled']);

        $this->dispatch('notify', message: 'Abonnement annulé avec succès.', type: 'success');
    }

    public function renewSubscription(Subscription $subscription)
    {
        // Désactiver d'abord l'ancien abonnement et tous les autres abonnements actifs
        $subscription->update(['status' => 'cancelled']);

        Subscription::where('company_id', $subscription->company_id)
            ->where('id', '!=', $subscription->id)
            ->where('status', 'active')
            ->update(['status' => 'cancelled']);

        // Créer un nouvel abonnement basé sur l'ancien
        $newSubscription = $subscription->replicate();
        $newSubscription->starts_at = now();
        $newSubscription->ends_at = $subscription->ends_at ? now()->addYear() : null;
        $newSubscription->status = 'active';
        $newSubscription->save();

        $this->dispatch('notify', message: 'Abonnement renouvelé avec succès.', type: 'success');
    }

    public function deleteSubscription(Subscription $subscription)
    {
        $subscription->delete();

        $this->dispatch('notify', message: 'Abonnement supprimé avec succès.', type: 'success');
    }

    public function selectCompany($companyId)
    {
        $this->selectedCompanyId = $companyId;
        $this->company_id = $companyId;
        $this->companySearch = '';
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->showQuickCreateModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingSubscription = null;
        $this->company_id = null;
        $this->plan_id = null;
        $this->starts_at = null;
        $this->ends_at = null;
        $this->status = 'active';
        $this->companySearch = '';
        $this->selectedCompanyId = null;
        $this->resetErrorBag();
    }

    public function getStatsProperty()
    {
        return [
            'total_subscriptions' => Subscription::count(),
            'active_subscriptions' => Subscription::where('status', 'active')->count(),
            'expired_subscriptions' => Subscription::where('status', 'expired')->count(),
            'cancelled_subscriptions' => Subscription::where('status', 'cancelled')->count(),
            'monthly_revenue' => Subscription::where('status', 'active')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.price'),
            'companies_with_subscriptions' => Subscription::where('status', 'active')
                ->distinct('company_id')
                ->count(),
        ];
    }

    public function getSearchCompaniesProperty()
    {
        if (strlen($this->companySearch) < 2) {
            return collect();
        }

        return Company::where('name', 'like', '%'.$this->companySearch.'%')
            ->orWhere('email', 'like', '%'.$this->companySearch.'%')
            ->limit(10)
            ->get();
    }

    public function render()
    {
        $subscriptions = Subscription::with(['company', 'plan'])
            ->when($this->search, function ($query) {
                $query->whereHas('company', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%');
                })->orWhereHas('plan', function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%');
                });
            })
            ->when($this->statusFilter, function ($query) {
                $query->where('status', $this->statusFilter);
            })
            ->when($this->planFilter, function ($query) {
                $query->where('plan_id', $this->planFilter);
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $companies = Company::orderBy('name')->get();
        $plans = Plan::orderBy('name')->get();

        return view('livewire.admin.subscriptions-index', [
            'subscriptions' => $subscriptions,
            'companies' => $companies,
            'plans' => $plans,
        ]);
    }
}
