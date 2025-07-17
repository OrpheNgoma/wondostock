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

#[Layout('components.layouts.app')]
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
            'role_id' => 'required|exists:roles,id',
            'store_id' => 'nullable|exists:stores,id',
        ];
    }

    public function mount()
    {
        $this->editingUser = new User;
    }

    public function create()
    {
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

        $role = Role::findById($this->role_id);
        $this->editingUser->syncRoles([$role->name]);

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

    public function render()
    {
        $companyId = Auth::user()->company_id;
        $users = User::where('company_id', $companyId)
            ->where('name', 'like', '%'.$this->search.'%')
            ->with(['roles', 'store'])
            ->paginate(10);

        $roles = Role::where('name', '!=', 'Super-Administrateur')->pluck('name', 'id');
        $stores = Store::where('company_id', $companyId)->pluck('name', 'id');

        return view('livewire.settings.users.index', [
            'users' => $users,
            'roles' => $roles,
            'stores' => $stores,
        ]);
    }
}
