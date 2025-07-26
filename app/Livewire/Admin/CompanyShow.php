<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.admin')]
#[Title('Détails Entreprise - WondoStock Admin')]
class CompanyShow extends Component
{
    use WithPagination;

    public Company $company;
    public bool $showSubscriptionForm = false;
    public bool $showUserForm = false;
    public bool $showEditForm = false;
    
    // Subscription form fields
    public ?int $plan_id = null;
    public ?string $starts_at = null;
    public ?string $ends_at = null;
    public string $status = 'active';

    // User form fields
    public string $userName = '';
    public string $userEmail = '';
    public string $userPassword = '';
    public ?int $editingUserId = null;

    // Company edit form fields
    public string $companyName = '';
    public string $companyLegalName = '';
    public string $companyEmail = '';
    public string $companyPhone = '';
    public string $companyAddress = '';
    public string $companyRccm = '';
    public string $companyNif = '';
    public bool $companyIsActive = true;

    protected function rules()
    {
        $rules = [
            // Subscription rules
            'plan_id' => 'required|exists:plans,id',
            'starts_at' => 'required|date',
            'ends_at' => 'nullable|date|after:starts_at',
            'status' => 'required|in:active,expired,cancelled',
            
            // User rules
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
            'userPassword' => $this->editingUserId ? 'nullable|min:8' : 'required|min:8',
            
            // Company rules
            'companyName' => 'required|string|max:255',
            'companyLegalName' => 'nullable|string|max:255',
            'companyEmail' => 'required|email|unique:companies,email,' . $this->company->id,
            'companyPhone' => 'nullable|string|max:50',
            'companyAddress' => 'nullable|string',
            'companyRccm' => 'nullable|string|max:100',
            'companyNif' => 'nullable|string|max:100',
        ];

        return $rules;
    }

    public function mount(Company $company)
    {
        if (!Auth::user()->is_global_admin) {
            abort(403, 'Accès non autorisé.');
        }

        $this->company = $company->load(['owner', 'users.roles', 'stores', 'subscription.plan']);
        
        // Pre-fill subscription form
        if ($this->company->subscription) {
            $this->plan_id = $this->company->subscription->plan_id;
            $this->starts_at = $this->company->subscription->starts_at?->format('Y-m-d');
            $this->ends_at = $this->company->subscription->ends_at?->format('Y-m-d');
            $this->status = $this->company->subscription->status;
        } else {
            $this->starts_at = now()->format('Y-m-d');
        }

        // Pre-fill company edit form
        $this->companyName = $this->company->name;
        $this->companyLegalName = $this->company->legal_name ?? '';
        $this->companyEmail = $this->company->email;
        $this->companyPhone = $this->company->phone_number ?? '';
        $this->companyAddress = $this->company->address ?? '';
        $this->companyRccm = $this->company->rccm ?? '';
        $this->companyNif = $this->company->nif ?? '';
        $this->companyIsActive = $this->company->is_active;
    }

    public function openSubscriptionForm()
    {
        $this->showSubscriptionForm = true;
    }

    public function closeSubscriptionForm()
    {
        $this->showSubscriptionForm = false;
        $this->resetErrorBag();
    }

    public function saveSubscription()
    {
        $this->validate();

        // Désactiver l'abonnement actuel s'il existe
        if ($this->company->subscription) {
            $this->company->subscription->update(['status' => 'cancelled']);
        }

        // Créer le nouvel abonnement
        Subscription::create([
            'company_id' => $this->company->id,
            'plan_id' => $this->plan_id,
            'starts_at' => $this->starts_at,
            'ends_at' => $this->ends_at,
            'status' => $this->status,
        ]);

        // Recharger les données
        $this->company->refresh();
        $this->company->load(['subscription.plan']);

        $this->dispatch('notify', message: 'Abonnement mis à jour avec succès.');
        $this->closeSubscriptionForm();
    }

    public function toggleCompanyStatus()
    {
        $this->company->update([
            'is_active' => !$this->company->is_active
        ]);

        $status = $this->company->is_active ? 'activée' : 'désactivée';
        $this->dispatch('notify', message: "Entreprise {$status} avec succès.");
    }

    public function openUserForm()
    {
        $this->showUserForm = true;
        $this->resetUserForm();
    }

    public function closeUserForm()
    {
        $this->showUserForm = false;
        $this->resetUserForm();
        $this->resetErrorBag();
    }

    public function saveUser()
    {
        $this->validate([
            'userName' => 'required|string|max:255',
            'userEmail' => 'required|email|unique:users,email,' . $this->editingUserId,
            'userPassword' => $this->editingUserId ? 'nullable|min:8' : 'required|min:8',
        ]);

        $userData = [
            'name' => $this->userName,
            'email' => $this->userEmail,
            'company_id' => $this->company->id,
        ];

        if ($this->userPassword) {
            $userData['password'] = Hash::make($this->userPassword);
        }

        if ($this->editingUserId) {
            User::find($this->editingUserId)->update($userData);
            $message = 'Utilisateur mis à jour avec succès.';
        } else {
            User::create($userData);
            $message = 'Utilisateur créé avec succès.';
        }

        // Recharger les données
        $this->company->refresh();
        $this->company->load(['users.roles']);

        $this->dispatch('notify', message: $message);
        $this->closeUserForm();
    }

    public function editUser(User $user)
    {
        $this->editingUserId = $user->id;
        $this->userName = $user->name;
        $this->userEmail = $user->email;
        $this->userPassword = '';
        $this->showUserForm = true;
    }

    public function deleteUser(User $user)
    {
        if ($user->id === $this->company->owner_id) {
            $this->dispatch('notify', message: 'Impossible de supprimer le propriétaire de l\'entreprise.', type: 'error');
            return;
        }

        $user->delete();
        
        // Recharger les données
        $this->company->refresh();
        $this->company->load(['users.roles']);

        $this->dispatch('notify', message: 'Utilisateur supprimé avec succès.');
    }

    public function openEditForm()
    {
        $this->showEditForm = true;
    }

    public function closeEditForm()
    {
        $this->showEditForm = false;
        $this->resetErrorBag();
        
        // Restaurer les valeurs originales
        $this->companyName = $this->company->name;
        $this->companyLegalName = $this->company->legal_name ?? '';
        $this->companyEmail = $this->company->email;
        $this->companyPhone = $this->company->phone_number ?? '';
        $this->companyAddress = $this->company->address ?? '';
        $this->companyRccm = $this->company->rccm ?? '';
        $this->companyNif = $this->company->nif ?? '';
        $this->companyIsActive = $this->company->is_active;
    }

    public function saveCompany()
    {
        $this->validate([
            'companyName' => 'required|string|max:255',
            'companyLegalName' => 'nullable|string|max:255',
            'companyEmail' => 'required|email|unique:companies,email,' . $this->company->id,
            'companyPhone' => 'nullable|string|max:50',
            'companyAddress' => 'nullable|string',
            'companyRccm' => 'nullable|string|max:100',
            'companyNif' => 'nullable|string|max:100',
        ]);

        $this->company->update([
            'name' => $this->companyName,
            'legal_name' => $this->companyLegalName ?: null,
            'email' => $this->companyEmail,
            'phone_number' => $this->companyPhone ?: null,
            'address' => $this->companyAddress ?: null,
            'rccm' => $this->companyRccm ?: null,
            'nif' => $this->companyNif ?: null,
            'is_active' => $this->companyIsActive,
        ]);

        $this->dispatch('notify', message: 'Entreprise mise à jour avec succès.');
        $this->closeEditForm();
    }

    private function resetUserForm()
    {
        $this->editingUserId = null;
        $this->userName = '';
        $this->userEmail = '';
        $this->userPassword = '';
    }

    public function render()
    {
        $plans = Plan::all();
        $users = $this->company->users()->paginate(10);

        return view('livewire.admin.company-show', [
            'plans' => $plans,
            'users' => $users,
        ]);
    }
}