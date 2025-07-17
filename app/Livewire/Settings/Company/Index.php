<?php

namespace App\Livewire\Settings\Company;

use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Paramètres de l\'Entreprise - KaziFlow')]
class Index extends Component
{
    use WithFileUploads;

    public Company $company;

    // --- Form Properties ---
    public string $name = '';

    public ?string $legal_name = '';

    public ?string $email = '';

    public ?string $phone_number = '';

    public ?string $address = '';

    public ?string $rccm = '';

    public ?string $nif = '';

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

        // Remplir les propriétés en gérant les valeurs nulles
        $this->name = $this->company->name ?? '';
        $this->legal_name = $this->company->legal_name ?? '';
        $this->email = $this->company->email ?? '';
        $this->phone_number = $this->company->phone_number ?? '';
        $this->address = $this->company->address ?? '';
        $this->rccm = $this->company->rccm ?? '';
        $this->nif = $this->company->nif ?? '';
    }

    public function save()
    {
        try {
            $this->validate();

            $this->company->update([
                'name' => $this->name,
                'legal_name' => $this->legal_name ?: null,
                'email' => $this->email ?: null,
                'phone_number' => $this->phone_number ?: null,
                'address' => $this->address ?: null,
                'rccm' => $this->rccm ?: null,
                'nif' => $this->nif ?: null,
            ]);

            if ($this->logo) {
                try {
                    // Supprime l'ancien logo s'il existe
                    $this->company->clearMediaCollection('logo');
                    // Ajoute le nouveau logo
                    $this->company->addMedia($this->logo->getRealPath())->toMediaCollection('logo');
                    // Reset la propriété logo pour éviter les problèmes d'affichage
                    $this->logo = null;
                } catch (\Exception $logoException) {
                    $this->dispatch('notify', [
                        'message' => 'Erreur lors du téléchargement du logo : ' . $logoException->getMessage(),
                        'type' => 'error',
                    ]);
                    return;
                }
            }

            $this->dispatch('notify', [
                'message' => 'Informations de l\'entreprise mises à jour avec succès !',
                'type' => 'success',
            ]);
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Erreur lors de la sauvegarde : '.$e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        return view('livewire.settings.company.index');
    }
}
