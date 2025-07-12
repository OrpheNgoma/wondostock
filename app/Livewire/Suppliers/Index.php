<?php

namespace App\Livewire\Suppliers;

use Livewire\Component;
use App\Models\Supplier;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Fournisseurs - WondoStock')]
class Index extends Component
{
    use WithPagination;

    // --- State ---
    public bool $showForm = false;
    public ?Supplier $editingSupplier;
    public string $search = '';

    // --- Form Properties ---
    public string $name = '';
    public string $contact_person = '';
    public string $email = '';
    public string $phone_number = '';
    public string $address = '';
    public string $nif = '';
    public string $rccm = '';
    public string $notes = '';
    public bool $is_active = true;

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'nif' => 'nullable|string',
            'rccm' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }
    
    public function mount()
    {
        $this->editingSupplier = new Supplier();
    }
    
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Supplier $supplier)
    {
        $this->editingSupplier = $supplier;
        $this->name = $supplier->name;
        $this->contact_person = $supplier->contact_person;
        $this->email = $supplier->email;
        $this->phone_number = $supplier->phone_number;
        $this->address = $supplier->address;
        $this->nif = $supplier->nif;
        $this->rccm = $supplier->rccm;
        $this->notes = $supplier->notes;
        $this->is_active = $supplier->is_active;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $this->editingSupplier->fill([
            'name' => $this->name,
            'contact_person' => $this->contact_person,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'nif' => $this->nif,
            'rccm' => $this->rccm,
            'notes' => $this->notes,
            'is_active' => $this->is_active,
            'company_id' => Auth::user()->company_id,
        ]);
        
        $this->editingSupplier->save();
        $this->dispatch('notify', message: 'Fournisseur sauvegardé.');
        $this->closeForm();
    }

    public function delete(Supplier $supplier)
    {
        $supplier->delete();
        $this->dispatch('notify', message: 'Fournisseur supprimé.');
    }

    public function closeForm()
    {
        $this->showForm = false;
        $this->resetForm();
    }

    private function resetForm()
    {
        $this->editingSupplier = new Supplier();
        $this->reset(['name', 'contact_person', 'email', 'phone_number', 'address', 'nif', 'rccm', 'notes', 'is_active']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $suppliers = Supplier::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.suppliers.index', [
            'suppliers' => $suppliers,
        ]);
    }
}