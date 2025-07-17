<?php

namespace App\Livewire\Settings\Roles;

use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

#[Layout('components.layouts.app')]
#[Title('Rôles & Permissions - WondoStock')]
class Index extends Component
{
    public $roles;

    public ?Role $selectedRole = null;

    public $rolePermissions = [];

    public function mount()
    {
        $this->roles = Role::where('name', '!=', 'Super-Administrateur')->get();
    }

    public function selectRole(int $roleId)
    {
        $this->selectedRole = Role::findById($roleId);
        $this->rolePermissions = $this->selectedRole->permissions()->pluck('id')->toArray();
    }

    public function savePermissions()
    {
        if ($this->selectedRole) {
            $permissions = Permission::whereIn('id', $this->rolePermissions)->get();
            $this->selectedRole->syncPermissions($permissions);
            $this->dispatch('notify', message: 'Permissions mises à jour pour le rôle '.$this->selectedRole->name);
        }
    }

    public function render()
    {
        $permissions = Permission::all()->groupBy(function ($permission) {
            return explode('_', $permission->name)[1]; // Groupe par ex: 'products', 'users', etc.
        });

        return view('livewire.settings.roles.index', [
            'permissions' => $permissions,
        ]);
    }
}
