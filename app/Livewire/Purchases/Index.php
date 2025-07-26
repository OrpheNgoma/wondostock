<?php

namespace App\Livewire\Purchases;

use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.saas')]
#[Title('Bons de Commande - WondoStock')]
class Index extends Component
{
    use WithPagination;

    public string $search = '';

    public function render()
    {
        $companyId = Auth::user()->company_id;
        $purchaseOrders = Document::where('company_id', $companyId)
            ->where('type', \App\Enums\DocumentType::PurchaseOrder)
            ->with('supplier')
            ->when($this->search, function ($query) {
                $query->where('document_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('supplier', fn ($q) => $q->where('name', 'like', '%'.$this->search.'%'));
            })
            ->latest('document_date')
            ->paginate(15);

        return view('livewire.saas.purchases.index', ['purchaseOrders' => $purchaseOrders]);
    }
}
