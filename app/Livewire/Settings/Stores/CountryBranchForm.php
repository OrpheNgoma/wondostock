<?php

namespace App\Livewire\Settings\Stores;

use App\Models\Store;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Gestion des Branches Pays - WondoStock')]
class CountryBranchForm extends Component
{
    use WithFileUploads;

    public ?Store $store = null;

    public bool $isEditing = false;

    // Champs du formulaire
    public $name = '';

    public $address = '';

    public $city = '';

    public $contact_phone = '';

    public $country_code = '';

    public $country_name = '';

    public $nif = '';

    public $rccm = '';

    public $business_permit = '';

    public $tax_id = '';

    public $email = '';

    public $website = '';

    public $postal_box = '';

    public $is_active = true;

    // Fichiers d'images
    public $header_image;

    public $footer_image;

    // URLs des images existantes
    public $current_header_image = '';

    public $current_footer_image = '';

    protected function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'address' => 'required|string|max:500',
            'city' => 'required|string|max:255',
            'contact_phone' => 'required|string|max:20',
            'country_code' => 'required|string|max:3',
            'country_name' => 'required|string|max:255',
            'nif' => 'nullable|string|max:50',
            'rccm' => 'nullable|string|max:50',
            'business_permit' => 'nullable|string|max:50',
            'tax_id' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'postal_box' => 'nullable|string|max:50',
            'is_active' => 'boolean',
            'header_image' => 'nullable|image|max:2048', // 2MB max
            'footer_image' => 'nullable|image|max:2048', // 2MB max
        ];
    }

    public function mount(?Store $store = null)
    {
        if ($store && $store->exists) {
            $this->store = $store;
            $this->isEditing = true;
            $this->loadStoreData();
        }
    }

    private function loadStoreData()
    {
        $this->name = $this->store->name;
        $this->address = $this->store->address;
        $this->city = $this->store->city;
        $this->contact_phone = $this->store->contact_phone;
        $this->country_code = $this->store->country_code;
        $this->country_name = $this->store->country_name;
        $this->nif = $this->store->nif;
        $this->rccm = $this->store->rccm;
        $this->business_permit = $this->store->business_permit;
        $this->tax_id = $this->store->tax_id;
        $this->email = $this->store->email;
        $this->website = $this->store->website;
        $this->postal_box = $this->store->postal_box;
        $this->is_active = $this->store->is_active;

        // URLs des images existantes
        $this->current_header_image = $this->store->getInvoiceHeaderImageUrl();
        $this->current_footer_image = $this->store->getInvoiceFooterImageUrl();
    }

    public function save()
    {
        $this->validate();

        $data = [
            'company_id' => Auth::user()->company_id,
            'name' => $this->name,
            'address' => $this->address,
            'city' => $this->city,
            'contact_phone' => $this->contact_phone,
            'country_code' => $this->country_code,
            'country_name' => $this->country_name,
            'nif' => $this->nif,
            'rccm' => $this->rccm,
            'business_permit' => $this->business_permit,
            'tax_id' => $this->tax_id,
            'email' => $this->email,
            'website' => $this->website,
            'postal_box' => $this->postal_box,
            'is_active' => $this->is_active,
            'is_country_branch' => true, // Toujours true pour les branches pays
        ];

        // Gestion de l'upload de l'image d'en-tête
        if ($this->header_image) {
            try {
                // Supprimer l'ancienne image si elle existe
                if ($this->isEditing && $this->store->invoice_header_image) {
                    Storage::delete($this->store->invoice_header_image);
                }

                $data['invoice_header_image'] = $this->header_image->store('invoice-headers', 'public');
            } catch (\Exception $e) {
                $this->dispatch('notify', message: 'Erreur lors du téléchargement de l\'image d\'en-tête : ' . $e->getMessage());
                return;
            }
        }

        // Gestion de l'upload de l'image de pied de page
        if ($this->footer_image) {
            try {
                // Supprimer l'ancienne image si elle existe
                if ($this->isEditing && $this->store->invoice_footer_image) {
                    Storage::delete($this->store->invoice_footer_image);
                }

                $data['invoice_footer_image'] = $this->footer_image->store('invoice-footers', 'public');
            } catch (\Exception $e) {
                $this->dispatch('notify', message: 'Erreur lors du téléchargement de l\'image de pied de page : ' . $e->getMessage());
                return;
            }
        }

        if ($this->isEditing) {
            $this->store->update($data);
            $message = 'Branche pays mise à jour avec succès !';
            // Recharger les URLs des images après mise à jour
            $this->current_header_image = $this->store->getInvoiceHeaderImageUrl();
            $this->current_footer_image = $this->store->getInvoiceFooterImageUrl();
        } else {
            $this->store = Store::create($data);
            $message = 'Branche pays créée avec succès !';
        }

        // Reset les propriétés de fichiers après sauvegarde
        $this->header_image = null;
        $this->footer_image = null;

        $this->dispatch('notify', message: $message);
        
        if (!$this->isEditing) {
            $this->redirectRoute('stores.index');
        }
    }

    public function removeHeaderImage()
    {
        if ($this->isEditing && $this->store->invoice_header_image) {
            Storage::delete($this->store->invoice_header_image);
            $this->store->update(['invoice_header_image' => null]);
            $this->current_header_image = '';
            $this->dispatch('notify', message: 'Image d\'en-tête supprimée avec succès !');
        }
    }

    public function removeFooterImage()
    {
        if ($this->isEditing && $this->store->invoice_footer_image) {
            Storage::delete($this->store->invoice_footer_image);
            $this->store->update(['invoice_footer_image' => null]);
            $this->current_footer_image = '';
            $this->dispatch('notify', message: 'Image de pied de page supprimée avec succès !');
        }
    }

    public function render()
    {
        return view('livewire.settings.stores.country-branch-form');
    }
}
