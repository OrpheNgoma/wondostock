<?php

namespace App\Livewire\Documents;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\StockMovementType;
use App\Models\Document;
use App\Models\Payment;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('components.layouts.saas')]
#[Title('Détail du Document - KaziFlow')]
class Show extends Component
{
    public Document $document;

    // --- Payment Form State ---
    public bool $showPaymentForm = false;

    public $payment_amount;

    public $payment_date;

    public $payment_method = 'cash';

    public $payment_reference = '';

    public $payment_notes = '';

    protected function rules()
    {
        // On calcule le montant maximum pour le paiement
        $maxAmount = $this->document->total_amount - $this->document->paid_amount;

        return [
            'payment_amount' => "required|numeric|min:0.01|max:{$maxAmount}",
            'payment_date' => 'required|date',
            'payment_method' => 'required',
            'payment_reference' => 'nullable|string|max:255',
            'payment_notes' => 'nullable|string',
        ];
    }

    public function mount(Document $document)
    {
        $this->loadDocumentData($document->id);
    }

    /**
     * Génère et télécharge le document au format PDF.
     *
     * @param  string  $printMode  Mode d'impression ('standard' ou 'content_only')
     */
    public function downloadPdf($printMode = 'standard')
    {
        // Détermine si on utilise les données du store (branche pays) ou de la company
        $useStoreData = $this->document->store->is_country_branch;

        // Gestion du logo selon le type de store
        $logoBase64 = null;
        $headerImageBase64 = null;
        $footerImageBase64 = null;

        if ($useStoreData) {
            // Utilise les images personnalisées du store (branche pays)
            $headerImageBase64 = $this->document->store->getInvoiceHeaderImageBase64();
            $footerImageBase64 = $this->document->store->getInvoiceFooterImageBase64();
        } else {
            // Utilise le logo de la company (logique existante)
            $logo = $this->document->company->getFirstMedia('logo');
            $logoPath = $logo ? $logo->getPath() : null;

            if ($logoPath && file_exists($logoPath)) {
                $logoData = file_get_contents($logoPath);
                $logoBase64 = 'data:image/'.pathinfo($logoPath, PATHINFO_EXTENSION).';base64,'.base64_encode($logoData);
            }
        }

        // On passe les données du document à la vue PDF
        $pdf = Pdf::loadView('pdfs.document', [
            'document' => $this->document,
            'logoBase64' => $logoBase64,
            'headerImageBase64' => $headerImageBase64,
            'footerImageBase64' => $footerImageBase64,
            'useStoreData' => $useStoreData,
            'printMode' => $printMode,
        ]);

        // Configuration du PDF selon le mode d'impression
        if ($printMode === 'content_only') {
            $pdf->setPaper('A4', 'portrait')
                ->setOptions([
                    'defaultFont' => 'sans-serif',
                    'isPhpEnabled' => true,
                ]);
        }

        // Nom du fichier selon le mode d'impression
        $fileName = $this->document->document_number;
        if ($printMode === 'content_only') {
            $fileName .= '_contenu_seul';
        }
        $fileName .= '.pdf';

        // On retourne le PDF en téléchargement au navigateur
        return response()->streamDownload(function () use ($pdf) {
            echo $pdf->stream();
        }, $fileName);
    }

    /**
     * Nouvelle méthode pour convertir un document (ex: devis) en facture.
     */
    public function convertToInvoice()
    {
        // On ne peut convertir qu'un devis ou un bon de commande
        if (! in_array($this->document->type, [DocumentType::Quote, DocumentType::Order])) {
            $this->dispatch('notify', message: 'Seuls les devis et bons de commande peuvent être convertis en facture.', type: 'error');

            return;
        }

        // On vérifie si une facture n'a pas déjà été générée
        if ($this->document->convertedToDocument) {
            $this->dispatch('notify', message: 'Ce document a déjà été converti en facture.', type: 'error');

            return;
        }

        $newInvoice = null;

        try {
            DB::transaction(function () use (&$newInvoice) {
                // 1. Créer la nouvelle facture en copiant les données
                $newInvoice = Document::create([
                    'company_id' => $this->document->company_id,
                    'customer_id' => $this->document->customer_id,
                    'store_id' => $this->document->store_id,
                    'user_id' => Auth::id(),
                    'source_document_id' => $this->document->id,
                    'type' => DocumentType::Invoice,
                    'status' => DocumentStatus::Draft, // La nouvelle facture est un brouillon
                    'document_number' => 'FACT-'.now()->timestamp, // Logique à améliorer
                    'document_date' => now(),
                    'sub_total' => $this->document->sub_total,
                    'tax_amount' => $this->document->tax_amount,
                    'total_amount' => $this->document->total_amount,
                    'notes' => $this->document->notes,
                ]);

                // 2. Copier les lignes d'articles
                foreach ($this->document->items as $item) {
                    $newInvoice->items()->create($item->toArray());
                }

                // 3. Mettre à jour le statut du document source (optionnel)
                $this->document->status = DocumentStatus::Validated; // Ou un statut "Converti"
                $this->document->save();
            });
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Une erreur est survenue lors de la conversion.', type: 'error');

            return;
        }

        $this->dispatch('notify', message: 'Document converti en facture avec succès !');
        $this->redirectRoute('documents.show', $newInvoice);
    }

    /**
     * Valide le document, ce qui déduit le stock.
     * C'est l'action qui confirme une vente.
     */
    public function validateDocument()
    {
        // On ne peut valider qu'un brouillon
        if ($this->document->status !== DocumentStatus::Draft) {
            $this->dispatch('notify', message: 'Ce document ne peut pas être validé.', type: 'error');

            return;
        }

        try {
            DB::transaction(function () {
                // 1. Vérifier la disponibilité du stock pour chaque article
                foreach ($this->document->items as $item) {
                    $stock = DB::table('product_store')
                        ->where('product_id', $item->product_id)
                        ->where('store_id', $this->document->store_id)
                        ->first();

                    if (! $stock || $stock->quantity < $item->quantity) {
                        throw new \Exception("Stock insuffisant pour le produit : {$item->description}");
                    }
                }

                // 2. Si tout est en stock, on déduit les quantités
                foreach ($this->document->items as $item) {
                    DB::table('product_store')
                        ->where('product_id', $item->product_id)
                        ->where('store_id', $this->document->store_id)
                        ->decrement('quantity', $item->quantity);

                    // 3. On enregistre le mouvement de stock pour la traçabilité
                    $this->document->store->stockMovements()->create([
                        'company_id' => $this->document->company_id,
                        'product_id' => $item->product_id,
                        'user_id' => Auth::id(),
                        'type' => StockMovementType::Sale,
                        'quantity' => -$item->quantity, // Négatif car c'est une sortie
                        'source_id' => $this->document->id,
                        'source_type' => Document::class,
                    ]);
                }

                // 4. Mettre à jour le statut du document
                $this->document->status = DocumentStatus::Validated;
                $this->document->validated_at = now();
                $this->document->save();

                $this->dispatch('notify', message: 'Document validé et stock mis à jour !');
                $this->loadDocumentData($this->document->id);
            });
        } catch (\Exception $e) {
            $this->dispatch('notify', message: $e->getMessage(), type: 'error');
        }
    }

    public function openPaymentForm()
    {
        $this->payment_date = now()->format('d-m-Y');
        $this->payment_amount = $this->document->total_amount - $this->document->paid_amount;
        $this->payment_method = 'cash'; // On réinitialise à 'cash' par défaut
        $this->reset(['payment_reference', 'payment_notes']);
        $this->showPaymentForm = true;
    }

    public function recordPayment()
    {
        $this->validate();

        DB::transaction(function () {
            $this->document->payments()->create([
                'company_id' => $this->document->company_id,
                'user_id' => Auth::id(),
                'amount' => $this->payment_amount,
                'payment_date' => $this->payment_date,
                'payment_method' => $this->payment_method,
                'reference' => $this->payment_reference,
                'notes' => $this->payment_notes,
            ]);

            // Mettre à jour le montant payé et le statut du document
            $totalPaid = $this->document->payments()->sum('amount');
            $this->document->paid_amount = $totalPaid;

            if ($totalPaid >= $this->document->total_amount) {
                $this->document->status = DocumentStatus::Paid;
            } else {
                $this->document->status = DocumentStatus::PartiallyPaid;
            }
            $this->document->save();
        });

        $this->dispatch('notify', message: 'Paiement enregistré avec succès.');
        $this->showPaymentForm = false;
        $this->loadDocumentData($this->document->id); // On recharge les données pour mettre à jour la vue
    }

    private function loadDocumentData($documentId)
    {
        $this->document = Document::with(['company', 'customer', 'store', 'items.product', 'payments.user', 'convertedToDocument'])->findOrFail($documentId);
    }

    public function render()
    {
        return view('livewire.saas.documents.show');
    }
}
