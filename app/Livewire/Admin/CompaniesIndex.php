<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Gestion des Entreprises - WondoStock Admin')]
class CompaniesIndex extends Component
{
    use WithPagination;

    public string $search = '';

    public string $statusFilter = '';

    public string $planFilter = '';

    public string $sortBy = 'created_at';

    public string $sortDirection = 'desc';

    // Modal de création/édition d'entreprise
    public bool $showModal = false;

    public ?int $editingCompany = null;

    public string $companyName = '';

    public string $companyLegalName = '';

    public string $companyEmail = '';

    public string $companyPhone = '';

    public string $companyAddress = '';

    public string $companyRccm = '';

    public string $companyNif = '';

    public bool $companyIsActive = true;

    // Champs propriétaire
    public string $ownerName = '';

    public string $ownerEmail = '';

    public string $ownerPassword = '';

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

    public function toggleCompanyStatus(Company $company)
    {
        $company->update([
            'is_active' => ! $company->is_active,
        ]);

        $status = $company->is_active ? 'activée' : 'désactivée';
        $this->dispatch('notify', message: "Entreprise {$status} avec succès.", type: 'success');
    }

    public function deleteCompany(Company $company)
    {
        // Soft delete de l'entreprise
        $company->delete();

        $this->dispatch('notify', message: 'Entreprise supprimée avec succès.', type: 'success');
    }

    public function restoreCompany($companyId)
    {
        $company = Company::withTrashed()->findOrFail($companyId);
        $company->restore();

        $this->dispatch('notify', message: 'Entreprise restaurée avec succès.', type: 'success');
    }

    public function createCompany()
    {
        $this->resetForm();
        $this->showModal = true;
    }

    public function editCompany(Company $company)
    {
        $this->editingCompany = $company->id;
        $this->companyName = $company->name;
        $this->companyLegalName = $company->legal_name ?? '';
        $this->companyEmail = $company->email;
        $this->companyPhone = $company->phone_number ?? '';
        $this->companyAddress = $company->address ?? '';
        $this->companyRccm = $company->rccm ?? '';
        $this->companyNif = $company->nif ?? '';
        $this->companyIsActive = $company->is_active;

        // Charger les infos du propriétaire
        if ($company->owner) {
            $this->ownerName = $company->owner->name;
            $this->ownerEmail = $company->owner->email;
        }

        $this->showModal = true;
    }

    public function saveCompany()
    {
        $rules = [
            'companyName' => 'required|string|max:255',
            'companyLegalName' => 'nullable|string|max:255',
            'companyEmail' => 'required|email|unique:companies,email,'.$this->editingCompany,
            'companyPhone' => 'nullable|string|max:50',
            'companyAddress' => 'nullable|string',
            'companyRccm' => 'nullable|string|max:100',
            'companyNif' => 'nullable|string|max:100',
        ];

        // Pour la création, valider aussi les champs propriétaire
        if (! $this->editingCompany) {
            $rules['ownerName'] = 'required|string|max:255';
            $rules['ownerEmail'] = 'required|email|unique:users,email';
            $rules['ownerPassword'] = 'required|min:8';
        } else {
            $rules['ownerName'] = 'required|string|max:255';
            $rules['ownerEmail'] = 'required|email|unique:users,email,'.($this->editingCompany ? Company::find($this->editingCompany)->owner_id : '');
            $rules['ownerPassword'] = 'nullable|min:8';
        }

        $this->validate($rules);

        try {
            $companyData = [
                'name' => $this->companyName,
                'legal_name' => $this->companyLegalName ?: null,
                'email' => $this->companyEmail,
                'phone_number' => $this->companyPhone ?: null,
                'address' => $this->companyAddress ?: null,
                'rccm' => $this->companyRccm ?: null,
                'nif' => $this->companyNif ?: null,
                'is_active' => $this->companyIsActive,
            ];

            if ($this->editingCompany) {
                // Mise à jour
                $company = Company::find($this->editingCompany);
                $company->update($companyData);

                // Mettre à jour le propriétaire
                if ($company->owner) {
                    $ownerData = [
                        'name' => $this->ownerName,
                        'email' => $this->ownerEmail,
                    ];
                    if ($this->ownerPassword) {
                        $ownerData['password'] = Hash::make($this->ownerPassword);
                    }
                    $company->owner->update($ownerData);
                }

                $message = 'Entreprise mise à jour avec succès.';
            } else {
                // Création
                $company = Company::create($companyData);

                // Créer le propriétaire
                $owner = User::create([
                    'name' => $this->ownerName,
                    'email' => $this->ownerEmail,
                    'password' => Hash::make($this->ownerPassword),
                    'company_id' => $company->id,
                ]);

                // Assigner le propriétaire
                $company->update(['owner_id' => $owner->id]);

                $message = 'Entreprise créée avec succès.';
            }

            $this->dispatch('notify', message: $message, type: 'success');

            $this->closeModal();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur lors de la sauvegarde: '.$e->getMessage(), type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingCompany = null;
        $this->companyName = '';
        $this->companyLegalName = '';
        $this->companyEmail = '';
        $this->companyPhone = '';
        $this->companyAddress = '';
        $this->companyRccm = '';
        $this->companyNif = '';
        $this->companyIsActive = true;
        $this->ownerName = '';
        $this->ownerEmail = '';
        $this->ownerPassword = '';
        $this->resetErrorBag();
    }

    public function getStatsProperty()
    {
        return [
            'total' => Company::count(),
            'active' => Company::where('is_active', true)->count(),
            'inactive' => Company::where('is_active', false)->count(),
            'with_subscription' => Company::whereHas('subscription', function ($q) {
                $q->where('status', 'active');
            })->count(),
        ];
    }

    public function render()
    {
        $companies = Company::withTrashed()
            ->with(['owner', 'users', 'subscription.plan'])
            ->withCount(['users', 'products', 'documents'])
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'like', '%'.$this->search.'%')
                        ->orWhere('legal_name', 'like', '%'.$this->search.'%')
                        ->orWhere('email', 'like', '%'.$this->search.'%')
                        ->orWhereHas('owner', function ($subQuery) {
                            $subQuery->where('name', 'like', '%'.$this->search.'%')
                                ->orWhere('email', 'like', '%'.$this->search.'%');
                        });
                });
            })
            ->when($this->statusFilter, function ($query) {
                if ($this->statusFilter === 'active') {
                    $query->where('is_active', true)->whereNull('deleted_at');
                } elseif ($this->statusFilter === 'inactive') {
                    $query->where('is_active', false)->whereNull('deleted_at');
                } elseif ($this->statusFilter === 'deleted') {
                    $query->whereNotNull('deleted_at');
                }
            })
            ->when($this->planFilter, function ($query) {
                $query->whereHas('subscription.plan', function ($q) {
                    $q->where('slug', $this->planFilter);
                });
            })
            ->orderBy($this->sortBy, $this->sortDirection)
            ->paginate(15);

        $plans = Plan::all();

        return view('livewire.admin.companies-index', [
            'companies' => $companies,
            'plans' => $plans,
        ]);
    }
}
