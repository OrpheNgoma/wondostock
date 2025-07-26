<?php

namespace App\Livewire\Settings\Users;

use App\Models\Store;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.saas')]
#[Title('Utilisateurs - KaziFlow')]
class Index extends Component
{
    use WithPagination;

    // --- State ---
    public bool $showForm = false;

    public ?User $editingUser;

    public string $search = '';

    // --- Form Properties ---
    public string $name = '';

    public string $email = '';

    public string $password = '';

    public ?int $store_id = null;

    public ?int $role_id = null;

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,'.($this->editingUser?->id ?? 'NULL'),
            'password' => $this->editingUser?->exists ? 'nullable|min:8' : 'required|min:8',
            'role_id' => [
                'required',
                'exists:roles,id,company_id,'.Auth::user()->company_id,
            ],
            'store_id' => 'nullable|exists:stores,id',
        ];
    }

    public function mount()
    {
        $this->editingUser = new User;
    }

    public function create()
    {
        if (! $this->canCreateUser()) {
            $this->dispatch('notify', message: 'Limite d\'utilisateurs atteinte pour votre forfait. Veuillez upgrader votre plan.', type: 'error');

            return;
        }

        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(User $user)
    {
        $this->editingUser = $user;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->password = ''; // Ne pas pré-remplir le mot de passe
        $this->store_id = $user->store_id;
        $this->role_id = $user->roles->first()?->id;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        // Vérifier la limite avant de créer un nouvel utilisateur
        if (! $this->editingUser->exists && ! $this->canCreateUser()) {
            $this->dispatch('notify', message: 'Limite d\'utilisateurs atteinte pour votre forfait. Veuillez upgrader votre plan.', type: 'error');

            return;
        }

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'store_id' => $this->store_id,
            'company_id' => Auth::user()->company_id,
        ];

        if ($this->password) {
            $data['password'] = Hash::make($this->password);
        }

        if ($this->editingUser->exists) {
            $this->editingUser->update($data);
        } else {
            $this->editingUser = User::create($data);
        }

        $role = Role::where('id', $this->role_id)
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if (! $role) {
            session()->flash('error', 'Le rôle sélectionné n\'existe pas ou n\'appartient pas à votre entreprise.');

            return;
        }

        // S'assurer que l'utilisateur a le bon company_id avant d'assigner le rôle
        $this->editingUser->company_id = Auth::user()->company_id;
        $this->editingUser->save();

        // Assigner le rôle avec le contexte de l'équipe (company_id)
        // La configuration utilise maintenant company_id comme team_foreign_key
        setPermissionsTeamId(Auth::user()->company_id);
        $this->editingUser->syncRoles([$role]);

        $this->dispatch('notify', message: 'Utilisateur sauvegardé.');
        $this->closeForm();
    }

    public function delete(User $user)
    {
        if ($user->id === Auth::id()) {
            $this->dispatch('notify', message: 'Vous ne pouvez pas vous supprimer vous-même.', type: 'error');

            return;
        }
        $user->delete();
        $this->dispatch('notify', message: 'Utilisateur supprimé.');
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingUser = new User;
        $this->reset(['name', 'email', 'password', 'store_id', 'role_id']);
        $this->resetErrorBag();
    }

    private function canCreateUser(): bool
    {
        $company = Auth::user()->company;
        if (! $company || ! $company->subscription || ! $company->subscription->plan) {
            return false;
        }

        $plan = $company->subscription->plan;
        $currentUserCount = User::where('company_id', $company->id)->count();

        return $plan->canHaveUsers($currentUserCount + 1);
    }

    public function render()
    {
        $companyId = Auth::user()->company_id;
        $users = User::where('company_id', $companyId)
            ->where('name', 'like', '%'.$this->search.'%')
            ->with(['roles', 'store'])
            ->paginate(10);

        $roles = Role::where('company_id', $companyId)
            ->where('name', '!=', 'Super-Administrateur')
            ->pluck('name', 'id');
        $stores = Store::where('company_id', $companyId)->pluck('name', 'id');

        $company = Auth::user()->company;
        $canCreateUser = $this->canCreateUser();
        $userLimit = $company->subscription?->plan?->getUserLimit() ?? 0;
        $currentUserCount = User::where('company_id', $companyId)->count();

        return view('livewire.saas.settings.users.index', [
            'users' => $users,
            'roles' => $roles,
            'stores' => $stores,
            'canCreateUser' => $canCreateUser,
            'userLimit' => $userLimit,
            'currentUserCount' => $currentUserCount,
        ]);
    }
}
