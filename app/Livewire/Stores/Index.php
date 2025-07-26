<?php

namespace App\Livewire\Stores;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Gérer les Magasins - KaziFlow')]
class Index extends Component
{
    // Contrôle l'affichage du panneau latéral de formulaire
    // public bool $showForm = false;

    // Utilisé pour savoir si on est en mode édition ou création
    public ?Store $editingStore;

    // Propriétés du formulaire liées aux champs de la BDD
    public string $name = '';

    public string $address = '';

    public string $city = '';

    public string $contact_phone = '';

    public bool $is_active = true;

    /**
     * Définition des règles de validation.
     */
    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'address' => 'nullable|string',
            'city' => 'nullable|string',
            'contact_phone' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Personnalisation des messages d'erreur en français.
     */
    protected function messages()
    {
        return [
            'name.required' => 'Le nom du magasin est obligatoire.',
            'name.min' => 'Le nom doit comporter au moins 3 caractères.',
        ];
    }

    /**
     * Initialise le composant.
     */
    public function mount()
    {
        $this->editingStore = new Store; // Initialise un modèle vide
    }

    /**
     * Ouvre le panneau pour la création d'un nouveau magasin.
     */
    public function create()
    {
        $this->resetForm();
        // $this->showForm = true;
        $this->dispatch('open-form');
    }

    /**
     * Ouvre le panneau pour l'édition d'un magasin existant.
     */
    public function edit(Store $store)
    {
        $this->editingStore = $store;
        $this->name = $store->name;
        $this->address = $store->address;
        $this->city = $store->city;
        $this->contact_phone = $store->contact_phone;
        $this->is_active = $store->is_active;
        // $this->showForm = true;
        $this->dispatch('open-form');
    }

    /**
     * Sauvegarde les données (création ou mise à jour).
     */
    public function save()
    {
        $this->validate();

        // On vérifie les droits liés au plan d'abonnement
        // Si l'utilisateur n'a pas la feature 'multi_store' ET qu'il a déjà au moins 1 magasin ET qu'il n'est pas en train d'éditer, on bloque.
        if (Gate::denies('use-feature-multi-store') && Auth::user()->company->stores()->count() >= 1 && ! $this->editingStore->exists) {
            $this->dispatch('notify', message: 'Passez au plan PRO pour gérer plusieurs magasins.', type: 'error');
            //  $this->showForm = false;
            $this->dispatch('close-form'); // On ferme le formulaire même en cas d'erreur de droits

            return;
        }

        // Si on a un magasin en cours d'édition, on met à jour.
        if ($this->editingStore->exists) {
            $this->editingStore->update($this->getFormData());
            $this->dispatch('notify', message: 'Magasin mis à jour avec succès.');
        } else {
            // Sinon, on crée un nouveau magasin.
            Auth::user()->company->stores()->create($this->getFormData());
            $this->dispatch('notify', message: 'Magasin créé avec succès.');
        }

        // $this->showForm = false;
        // On envoie un événement au navigateur pour fermer le formulaire
        $this->dispatch('close-form');
    }

    /**
     * Supprime un magasin.
     */
    public function delete(Store $store)
    {
        // On ne peut pas supprimer le dernier magasin de la compagnie.
        if (Auth::user()->company->stores()->count() === 1) {
            $this->dispatch('notify', message: 'Vous ne pouvez pas supprimer votre seul magasin.', type: 'error');

            return;
        }

        $store->delete();
        $this->dispatch('notify', message: 'Magasin supprimé avec succès.');
    }

    /**
     * Réinitialise les champs du formulaire.
     */
    private function resetForm()
    {
        $this->editingStore = new Store;
        $this->name = '';
        $this->address = '';
        $this->city = '';
        $this->contact_phone = '';
        $this->is_active = true;
        $this->resetErrorBag();
    }

    /**
     * Regroupe les données du formulaire pour la création/mise à jour.
     */
    private function getFormData(): array
    {
        return [
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'contact_phone' => $this->contact_phone,
            'is_active' => $this->is_active,
        ];
    }

    /**
     * Rend la vue avec les données nécessaires.
     */
    public function render()
    {
        $regularStores = Auth::user()->company->stores()->regularStores()->orderBy('name')->get();
        $countryBranches = Auth::user()->company->stores()->countryBranches()->orderBy('country_name')->get();

        return view('livewire.saas.stores.index', [
            'regularStores' => $regularStores,
            'countryBranches' => $countryBranches,
        ]);
    }
}
