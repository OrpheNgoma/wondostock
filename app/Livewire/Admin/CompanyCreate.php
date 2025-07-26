<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class CompanyCreate extends Component
{
    // Informations de l'entreprise
    public $name = '';
    public $legal_name = '';
    public $email = '';
    public $phone_number = '';
    public $address = '';
    public $rccm = '';
    public $nif = '';
    public $is_active = true;

    // Informations du propriétaire
    public $ownerName = '';
    public $ownerEmail = '';
    public $ownerPassword = '';
    public $ownerPasswordConfirmation = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'legal_name' => 'nullable|string|max:255',
        'email' => 'required|email|max:255|unique:companies,email',
        'phone_number' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'rccm' => 'nullable|string|max:255',
        'nif' => 'nullable|string|max:255',
        'is_active' => 'boolean',
        
        'ownerName' => 'required|string|max:255',
        'ownerEmail' => 'required|email|max:255|unique:users,email',
        'ownerPassword' => 'required|min:8|confirmed',
        'ownerPasswordConfirmation' => 'required',
    ];

    protected $messages = [
        'name.required' => 'Le nom de l\'entreprise est requis.',
        'email.required' => 'L\'email de l\'entreprise est requis.',
        'email.email' => 'L\'email doit être une adresse email valide.',
        'email.unique' => 'Cette adresse email est déjà utilisée par une autre entreprise.',
        
        'ownerName.required' => 'Le nom du propriétaire est requis.',
        'ownerEmail.required' => 'L\'email du propriétaire est requis.',
        'ownerEmail.email' => 'L\'email du propriétaire doit être une adresse email valide.',
        'ownerEmail.unique' => 'Cette adresse email est déjà utilisée.',
        'ownerPassword.required' => 'Le mot de passe est requis.',
        'ownerPassword.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
        'ownerPassword.confirmed' => 'Les mots de passe ne correspondent pas.',
        'ownerPasswordConfirmation.required' => 'La confirmation du mot de passe est requise.',
    ];

    public function save()
    {
        $this->validate();

        try {
            // Créer l'utilisateur propriétaire d'abord
            $owner = User::create([
                'name' => $this->ownerName,
                'email' => $this->ownerEmail,
                'password' => Hash::make($this->ownerPassword),
                'is_global_admin' => false,
            ]);

            // Créer l'entreprise
            $company = Company::create([
                'name' => $this->name,
                'legal_name' => $this->legal_name ?: $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'rccm' => $this->rccm,
                'nif' => $this->nif,
                'is_active' => $this->is_active,
                'owner_id' => $owner->id,
            ]);

            // Associer l'utilisateur à l'entreprise
            $owner->update(['company_id' => $company->id]);

            session()->flash('success', 'Entreprise créée avec succès !');
            return $this->redirect(route('admin.companies.index'));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création de l\'entreprise : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.company-create');
    }
}