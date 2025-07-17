<?php

namespace App\Livewire\Purchases;

use App\Enums\DocumentStatus;
use App\Enums\StockMovementType;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Détail Commande Fournisseur - WondoStock')]
class Show extends Component
{
    public Document $document;

    public function mount(Document $document)
    {
        $this->loadDocumentData($document->id);
    }

    public function markAsOrdered()
    {
        if ($this->document->status === DocumentStatus::Draft) {
            $this->document->status = DocumentStatus::Ordered;
            $this->document->save();
            $this->dispatch('notify', message: 'Commande marquée comme envoyée.');
            $this->loadDocumentData($this->document->id);
        }
    }

    public function receiveStock()
    {
        if ($this->document->status !== DocumentStatus::Ordered) {
            $this->dispatch('notify', message: 'Seule une commande envoyée peut être réceptionnée.', type: 'error');

            return;
        }

        DB::transaction(function () {
            foreach ($this->document->items as $item) {
                // Mise à jour du stock
                DB::table('product_store')->updateOrInsert(
                    ['product_id' => $item->product_id, 'store_id' => $this->document->store_id],
                    ['quantity' => DB::raw("quantity + {$item->quantity}")]
                );
                // Enregistrement du mouvement
                $this->document->store->stockMovements()->create([
                    'company_id' => $this->document->company_id, 'product_id' => $item->product_id, 'user_id' => Auth::id(),
                    'type' => StockMovementType::Purchase, 'quantity' => $item->quantity,
                    'source_id' => $this->document->id, 'source_type' => Document::class,
                ]);
            }
            // Mise à jour du statut
            $this->document->status = DocumentStatus::Completed;
            $this->document->save();
        });

        $this->dispatch('notify', message: 'Stock réceptionné avec succès !');
        $this->loadDocumentData($this->document->id);
    }

    private function loadDocumentData($documentId)
    {
        $this->document = Document::with(['company', 'supplier', 'store', 'items.product'])->findOrFail($documentId);
    }

    public function render()
    {
        return view('livewire.purchases.show');
    }
}
