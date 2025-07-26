<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Gestion des Plans - WondoStock Admin')]
class PlansIndex extends Component
{
    use WithPagination;

    public string $search = '';
    public string $sortBy = 'name';
    public string $sortDirection = 'asc';
    
    // Propriétés pour le modal de création/édition
    public bool $showModal = false;
    public ?int $editingPlan = null;
    public string $name = '';
    public string $slug = '';
    public string $description = '';
    public float $price = 0.00;
    public int $user_limit = 1;
    public bool $unlimited_users = false;
    public array $features = [];
    public string $newFeature = '';

    // Liste des fonctionnalités disponibles
    public array $availableFeatures = [
        'products' => 'Gestion des produits',
        'stores' => 'Gestion des magasins',
        'users' => 'Gestion des utilisateurs',
        'inventory' => 'Gestion des stocks',
        'sales' => 'Gestion des ventes',
        'purchases' => 'Gestion des achats',
        'reports' => 'Rapports et analyses',
        'api-access' => 'Accès API',
        'advanced-reports' => 'Rapports avancés',
        'multi-store' => 'Multi-magasins',
        'roles-permissions' => 'Rôles et permissions',
        'integrations' => 'Intégrations tierces',
        'priority-support' => 'Support prioritaire',
        'custom-branding' => 'Personnalisation marque',
    ];

    public function mount()
    {
        if (!Auth::user()->is_global_admin) {
            abort(403, 'Accès non autorisé.');
        }
    }

    public function updatedSearch()
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

    public function createPlan()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function updatedName()
    {
        // Auto-générer le slug basé sur le nom si on est en création
        if (!$this->editingPlan && $this->name) {
            $this->slug = \Illuminate\Support\Str::slug($this->name);
        }
    }

    public function editPlan(Plan $plan)
    {
        $this->editingPlan = $plan->id;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->user_limit = $plan->user_limit;
        $this->unlimited_users = $plan->unlimited_users;
        $this->features = $plan->getFeaturesArray();
        $this->showModal = true;
    }

    public function savePlan()
    {
        $rules = [
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:plans,slug,' . $this->editingPlan,
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'features' => 'array',
        ];

        // Ajouter la validation pour user_limit seulement si unlimited_users est false
        if (!$this->unlimited_users) {
            $rules['user_limit'] = 'required|integer|min:1';
        }

        $this->validate($rules);

        $data = [
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'price' => $this->price,
            'user_limit' => $this->unlimited_users ? null : $this->user_limit,
            'unlimited_users' => $this->unlimited_users,
            'features' => $this->features,
        ];

        if ($this->editingPlan) {
            Plan::find($this->editingPlan)->update($data);
            $message = 'Plan mis à jour avec succès.';
        } else {
            Plan::create($data);
            $message = 'Plan créé avec succès.';
        }

        $this->dispatch('notify', [
            'message' => $message,
            'type' => 'success'
        ]);

        $this->resetForm();
        $this->showModal = false;
    }

    public function deletePlan(Plan $plan)
    {
        try {
            // Vérifier s'il y a des abonnements actifs
            $activeSubscriptions = $plan->subscriptions()->where('status', 'active')->count();
            
            if ($activeSubscriptions > 0) {
                $this->dispatch('notify', [
                    'message' => "Impossible de supprimer ce plan. Il y a {$activeSubscriptions} abonnement(s) actif(s).",
                    'type' => 'error'
                ]);
                return;
            }

            // Vérifier s'il y a des abonnements en général
            $totalSubscriptions = $plan->subscriptions()->count();
            
            if ($totalSubscriptions > 0) {
                $this->dispatch('notify', [
                    'message' => "Impossible de supprimer ce plan. Il y a {$totalSubscriptions} abonnement(s) associé(s). Supprimez d'abord les abonnements.",
                    'type' => 'error'
                ]);
                return;
            }

            $plan->delete();
            
            $this->dispatch('notify', [
                'message' => 'Plan supprimé avec succès.',
                'type' => 'success'
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de la suppression du plan: ' . $e->getMessage(),
                'type' => 'error'
            ]);
        }
    }

    public function addFeature()
    {
        if ($this->newFeature && !in_array($this->newFeature, $this->features)) {
            $this->features[] = $this->newFeature;
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        if (isset($this->features[$index])) {
            unset($this->features[$index]);
            $this->features = array_values($this->features);
        }
    }

    public function toggleFeature($featureKey)
    {
        if (in_array($featureKey, $this->features)) {
            $this->features = array_values(array_diff($this->features, [$featureKey]));
        } else {
            $this->features[] = $featureKey;
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingPlan = null;
        $this->name = '';
        $this->slug = '';
        $this->description = '';
        $this->price = 0.00;
        $this->user_limit = 1;
        $this->unlimited_users = false;
        $this->features = [];
        $this->newFeature = '';
    }

    public function getStatsProperty()
    {
        return [
            'total_plans' => Plan::count(),
            'total_subscriptions' => \App\Models\Subscription::where('status', 'active')->count(),
            'monthly_revenue' => \App\Models\Subscription::where('status', 'active')
                ->join('plans', 'subscriptions.plan_id', '=', 'plans.id')
                ->sum('plans.price'),
            'average_price' => Plan::avg('price'),
        ];
    }

    public function render()
    {
        $plans = Plan::withCount(['subscriptions' => function($query) {
                $query->where('status', 'active');
            }])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('description', 'like', '%' . $this->search . '%');
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(10);

        return view('livewire.admin.plans-index', [
            'plans' => $plans,
        ]);
    }
}
