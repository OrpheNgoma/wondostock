<?php

namespace App\Livewire\Settings\Company;

use App\Models\Company;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Paramètres de l\'Entreprise - KaziFlow')]
class Index extends Component
{
    use WithFileUploads;

    public Company $company;

    // --- Form Properties ---
    public string $name = '';
    public string $legal_name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $address = '';
    public string $rccm = '';
    public string $nif = '';
    public $logo; // Pour le nouveau logo

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'legal_name' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string|max:255',
            'address' => 'nullable|string',
            'rccm' => 'nullable|string',
            'nif' => 'nullable|string',
            'logo' => 'nullable|image|max:1024', // 1MB Max
        ];
    }

    public function mount()
    {
        $this->company = Auth::user()->company;
        $this->fill($this->company->toArray());
    }

    public function save()
    {
        $this->validate();

        $this->company->update([
            'name' => $this->name,
            'legal_name' => $this->legal_name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'rccm' => $this->rccm,
            'nif' => $this->nif,
        ]);

        if ($this->logo) {
            // Supprime l'ancien logo s'il existe
            $this->company->clearMediaCollection('logo');
            // Ajoute le nouveau logo
            $this->company->addMedia($this->logo->getRealPath())->toMediaCollection('logo');
        }

        $this->dispatch('notify', message: 'Informations de l\'entreprise mises à jour.');
    }

    public function render()
    {
        return view('livewire.settings.company.index');
    }
}