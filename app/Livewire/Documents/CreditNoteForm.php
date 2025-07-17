<?php

namespace App\Livewire\Documents;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.app')]
#[Title('Créer un Avoir - WondoStock')]
class CreditNoteForm extends Component
{
    public Document $sourceDocument;

    public array $items = [];

    public string $notes = '';

    public string $document_date;

    protected function rules()
    {
        return [
            'items' => 'required|array|min:1',
            'items.*.quantity' => 'required|numeric|min:0|lte:items.*.max_quantity',
        ];
    }

    public function mount(Document $invoice)
    {
        // On s'assure que le document source est bien une facture
        if ($invoice->type !== DocumentType::Invoice) {
            abort(404, 'Seules les factures peuvent faire l\'objet d\'un avoir.');
        }

        $this->sourceDocument = $invoice->load('items.product');
        $this->document_date = now()->format('Y-m-d');

        // On pré-remplit le formulaire avec les articles de la facture originale
        foreach ($this->sourceDocument->items as $item) {
            $this->items[] = [
                'product_id' => $item->product_id,
                'name' => $item->description,
                'quantity' => $item->quantity,
                'max_quantity' => $item->quantity, // Quantité max pour le retour
                'unit_price' => $item->unit_price,
                'tax_rate' => $item->tax_rate,
            ];
        }
    }

    public function save()
    {
        $this->validate();

        $creditNote = null;

        DB::transaction(function () use (&$creditNote) {
            // Calcul des totaux
            $sub_total = 0;
            $tax_amount = 0;
            foreach ($this->items as $item) {
                $lineTotal = $item['quantity'] * $item['unit_price'];
                $sub_total += $lineTotal;
                $tax_amount += $lineTotal * ($item['tax_rate'] / 100);
            }
            $total_amount = $sub_total + $tax_amount;

            // Création de l'avoir
            $creditNote = Document::create([
                'company_id' => $this->sourceDocument->company_id,
                'customer_id' => $this->sourceDocument->customer_id,
                'store_id' => $this->sourceDocument->store_id,
                'user_id' => Auth::id(),
                'source_document_id' => $this->sourceDocument->id,
                'type' => DocumentType::CreditNote,
                'status' => DocumentStatus::Draft,
                'document_number' => 'AVOIR-'.now()->timestamp,
                'document_date' => $this->document_date,
                'sub_total' => $sub_total,
                'tax_amount' => $tax_amount,
                'total_amount' => $total_amount,
                'notes' => $this->notes,
            ]);

            // Ajout des articles à l'avoir
            foreach ($this->items as $item) {
                if ($item['quantity'] > 0) {
                    $dataToSave = [
                        'product_id' => $item['product_id'],
                        'description' => $item['name'], // On utilise 'name' qui contient la description originale
                        'quantity' => $item['quantity'],
                        'unit_price' => $item['unit_price'],
                        'tax_rate' => $item['tax_rate'],
                        'total_amount' => $item['quantity'] * $item['unit_price'],
                    ];
                    $creditNote->items()->create($dataToSave);
                }
            }
        });

        $this->dispatch('notify', message: 'Avoir créé avec succès.');
        $this->redirectRoute('documents.show', $creditNote);
    }

    public function render()
    {
        return view('livewire.documents.credit-note-form');
    }
}
