<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class CompanyEdit extends Component
{
    public Company $company;
    
    // Informations de l'entreprise
    public $name = '';
    public $legal_name = '';
    public $email = '';
    public $phone_number = '';
    public $address = '';
    public $rccm = '';
    public $nif = '';
    public $is_active = true;

    protected $rules = [
        'name' => 'required|string|max:255',
        'legal_name' => 'nullable|string|max:255',
        'email' => 'required|email|max:255',
        'phone_number' => 'nullable|string|max:255',
        'address' => 'nullable|string|max:500',
        'rccm' => 'nullable|string|max:255',
        'nif' => 'nullable|string|max:255',
        'is_active' => 'boolean',
    ];

    protected $messages = [
        'name.required' => 'Le nom de l\'entreprise est requis.',
        'email.required' => 'L\'email de l\'entreprise est requis.',
        'email.email' => 'L\'email doit être une adresse email valide.',
    ];

    public function mount(Company $company)
    {
        $this->company = $company;
        $this->name = $company->name;
        $this->legal_name = $company->legal_name;
        $this->email = $company->email;
        $this->phone_number = $company->phone_number;
        $this->address = $company->address;
        $this->rccm = $company->rccm;
        $this->nif = $company->nif;
        $this->is_active = $company->is_active;
    }

    public function save()
    {
        // Validation unique pour l'email (exclure l'entreprise actuelle)
        $rules = $this->rules;
        $rules['email'] .= ',email,' . $this->company->id;

        $this->validate($rules);

        try {
            $this->company->update([
                'name' => $this->name,
                'legal_name' => $this->legal_name ?: $this->name,
                'email' => $this->email,
                'phone_number' => $this->phone_number,
                'address' => $this->address,
                'rccm' => $this->rccm,
                'nif' => $this->nif,
                'is_active' => $this->is_active,
            ]);

            session()->flash('success', 'Entreprise modifiée avec succès !');
            return $this->redirect(route('admin.companies.index'));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification de l\'entreprise : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.company-edit');
    }
}