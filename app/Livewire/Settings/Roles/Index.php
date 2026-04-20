<?php

namespace App\Livewire\Settings\Roles;

use App\Models\User;
use App\Services\RolePermissionService;
use Illuminate\Validation\Rule;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.saas')]
#[Title('Rôles & Permissions - WondoStock')]
class Index extends Component
{
    use WithPagination;

    public $search = '';

    public $selectedTab = 'roles'; // 'roles' ou 'permissions'

    // Modales
    public $showCreateRoleModal = false;

    public $showEditRoleModal = false;

    public $showPermissionsModal = false;

    public $showAssignRoleModal = false;

    public $showDeleteModal = false;

    // Données du formulaire
    public $roleName = '';

    public $roleDescription = '';

    public $selectedRole = null;

    public $selectedUser = null;

    public $rolePermissions = [];

    public $availableUsers = [];

    protected $messages = [
        'roleName.required' => 'Le nom du rôle est requis.',
        'roleName.unique' => 'Ce nom de rôle existe déjà dans votre entreprise.',
        'roleName.max' => 'Le nom du rôle ne peut pas dépasser 255 caractères.',
        'roleDescription.max' => 'La description ne peut pas dépasser 500 caractères.',
    ];

    public function mount()
    {
        // Vérifier que l'utilisateur peut gérer les rôles
        if (! $this->getRolePermissionService()->canManageRoles()) {
            abort(403, 'Accès non autorisé.');
        }
    }

    private function getRolePermissionService(): RolePermissionService
    {
        return app(RolePermissionService::class);
    }

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function createRole()
    {
        $this->resetForm();
        $this->showCreateRoleModal = true;
    }

    public function editRole($roleId)
    {
        $role = $this->getRolePermissionService()->getCompanyRole($roleId);
        if (! $role) {
            return;
        }

        $this->selectedRole = $role;
        $this->roleName = $role->name;
        $this->roleDescription = $role->description ?? '';
        $this->showEditRoleModal = true;
    }

    public function managePermissions($roleId)
    {
        $role = $this->getRolePermissionService()->getCompanyRole($roleId);
        if (! $role) {
            return;
        }

        $this->selectedRole = $role;
        $this->rolePermissions = $role->permissions()->pluck('id')->toArray();
        $this->showPermissionsModal = true;
    }

    public function assignRole($roleId)
    {
        $role = $this->getRolePermissionService()->getCompanyRole($roleId);
        if (! $role) {
            return;
        }

        $this->selectedRole = $role;
        $this->availableUsers = $this->getRolePermissionService()->getUsersWithoutRole($role);
        $this->showAssignRoleModal = true;
    }

    public function confirmDelete($roleId)
    {
        $role = $this->getRolePermissionService()->getCompanyRole($roleId);
        if (! $role) {
            return;
        }

        $this->selectedRole = $role;
        $this->showDeleteModal = true;
    }

    public function saveRole()
    {
        $this->validate([
            'roleName' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')->where('company_id', auth()->user()->company_id),
            ],
            'roleDescription' => 'nullable|string|max:500',
        ]);

        try {
            $this->getRolePermissionService()->createRole($this->roleName, $this->roleDescription);

            $this->resetForm();
            $this->showCreateRoleModal = false;
            $this->dispatch('notify', message: 'Rôle créé avec succès !', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur : '.$e->getMessage(), type: 'error');
        }
    }

    public function updateRole()
    {
        $this->validate([
            'roleName' => [
                'required', 'string', 'max:255',
                Rule::unique('roles', 'name')
                    ->where('company_id', auth()->user()->company_id)
                    ->ignore($this->selectedRole->id),
            ],
            'roleDescription' => 'nullable|string|max:500',
        ]);

        try {
            $this->getRolePermissionService()->updateRole($this->selectedRole, $this->roleName, $this->roleDescription);

            $this->resetForm();
            $this->showEditRoleModal = false;
            $this->dispatch('notify', message: 'Rôle modifié avec succès !', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur : '.$e->getMessage(), type: 'error');
        }
    }

    public function savePermissions()
    {
        try {
            $this->getRolePermissionService()->syncRolePermissions($this->selectedRole, $this->rolePermissions);

            $this->showPermissionsModal = false;
            $this->dispatch('notify', message: 'Permissions mises à jour pour le rôle '.$this->selectedRole->name, type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur : '.$e->getMessage(), type: 'error');
        }
    }

    public function assignRoleToUser()
    {
        $this->validate([
            'selectedUser' => [
                'required',
                Rule::exists('users', 'id')->where('company_id', auth()->user()->company_id),
            ],
        ]);

        try {
            $user = User::where('company_id', auth()->user()->company_id)
                ->findOrFail((int) $this->selectedUser);

            $this->getRolePermissionService()->assignRoleToUser($user, $this->selectedRole);

            $this->showAssignRoleModal = false;
            $this->dispatch('notify', message: 'Rôle assigné à '.$user->name.' avec succès !', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur : '.$e->getMessage(), type: 'error');
        }
    }

    public function deleteRole()
    {
        try {
            $this->getRolePermissionService()->deleteRole($this->selectedRole);

            $this->showDeleteModal = false;
            $this->dispatch('notify', message: 'Rôle supprimé avec succès !', type: 'success');

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur : '.$e->getMessage(), type: 'error');
        }
    }

    public function removeRoleFromUser($userId, $roleId)
    {
        try {
            $user = User::where('company_id', auth()->user()->company_id)->find($userId);
            $role = $this->getRolePermissionService()->getCompanyRole($roleId);

            if ($user && $role) {
                $this->getRolePermissionService()->removeRoleFromUser($user, $role);
                $this->dispatch('notify', message: 'Rôle retiré de '.$user->name.' avec succès !', type: 'success');
            }

        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur : '.$e->getMessage(), type: 'error');
        }
    }

    public function closeModal()
    {
        $this->showCreateRoleModal = false;
        $this->showEditRoleModal = false;
        $this->showPermissionsModal = false;
        $this->showAssignRoleModal = false;
        $this->showDeleteModal = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->roleName = '';
        $this->roleDescription = '';
        $this->selectedRole = null;
        $this->selectedUser = null;
        $this->rolePermissions = [];
        $this->availableUsers = [];
        $this->resetErrorBag();
    }

    public function render(RolePermissionService $rolePermissionService)
    {
        // Récupérer les rôles de l'entreprise
        $roles = Role::where('company_id', auth()->user()->company_id)
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%');
            })
            ->withCount('users')
            ->paginate(10);

        // Grouper les permissions par catégorie
        $permissions = $rolePermissionService->getPermissionsByGroup();

        // Utilisateurs avec leurs rôles
        $users = User::where('company_id', auth()->user()->company_id)
            ->with('roles')
            ->when($this->search, function ($query) {
                $query->where('name', 'like', '%'.$this->search.'%')
                    ->orWhere('email', 'like', '%'.$this->search.'%');
            })
            ->paginate(10, ['*'], 'users');

        return view('livewire.saas.settings.roles.index', [
            'roles' => $roles,
            'permissions' => $permissions,
            'users' => $users,
        ]);
    }
}
