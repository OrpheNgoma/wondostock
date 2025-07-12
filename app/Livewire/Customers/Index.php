<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use App\Enums\CustomerType;
use Livewire\WithPagination;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Gérer les Clients - KaziFlow')]
class Index extends Component
{
    use WithPagination;

    // State
    public bool $showForm = false;
    public ?Customer $editingCustomer;
    public string $search = '';

    // Form Properties
    public string $name = '';
    public string $email = '';
    public string $phone_number = '';
    public string $address = '';
    public string $type = 'individual';

    protected function rules()
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'email' => 'nullable|email|max:255|unique:customers,email,' . ($this->editingCustomer?->id ?? 'NULL'),
            'phone_number' => 'nullable|string',
            'address' => 'nullable|string',
            'type' => 'required|in:individual,professional',
        ];
    }
    
    public function mount()
    {
        $this->editingCustomer = new Customer();
    }
    
    public function create()
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function edit(Customer $customer)
    {
        $this->editingCustomer = $customer;
        $this->name = $customer->name;
        $this->email = $customer->email;
        $this->phone_number = $customer->phone_number;
        $this->address = $customer->address;
        $this->type = $customer->type->value;
        $this->showForm = true;
    }

    public function save()
    {
        $this->validate();

        $data = [
            'name' => $this->name,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'address' => $this->address,
            'type' => $this->type,
            'company_id' => Auth::user()->company_id,
        ];

        if ($this->editingCustomer->exists) {
            $this->editingCustomer->update($data);
            $this->dispatch('notify', message: 'Client mis à jour.');
        } else {
            Customer::create($data);
            $this->dispatch('notify', message: 'Client créé.');
        }
        
        $this->showForm = false;
    }

    public function delete(Customer $customer)
    {
        // Add logic to check for invoices before deleting
        $customer->delete();
        $this->dispatch('notify', message: 'Client supprimé.');
    }

    private function resetForm()
    {
        $this->editingCustomer = new Customer();
        $this->reset(['name', 'email', 'phone_number', 'address', 'type']);
        $this->resetErrorBag();
    }

    public function render()
    {
        $customers = Customer::where('company_id', Auth::user()->company_id)
            ->where('name', 'like', '%' . $this->search . '%')
            ->latest()
            ->paginate(10);

        return view('livewire.customers.index', [
            'customers' => $customers,
        ]);
    }
}