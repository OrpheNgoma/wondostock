<?php

namespace App\Livewire\Documents;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('components.layouts.saas')]
#[Title('Documents de Vente - KaziFlow')]
class Index extends Component
{
    use WithPagination;

    // --- Filters ---
    public string $search = '';

    public string $typeFilter = '';

    public string $statusFilter = '';

    public bool $showOverdueOnly = false;

    // --- Actions ---

    public function delete(Document $document)
    {
        try {
            // Vérifier que le document appartient à la même entreprise
            if ($document->company_id !== Auth::user()->company_id) {
                $this->dispatch('notify', message: 'Accès non autorisé.', type: 'error');

                return;
            }

            // On ne peut pas supprimer un document qui n'est plus un brouillon
            if ($document->status !== DocumentStatus::Draft) {
                $this->dispatch('notify', message: 'Seuls les documents en brouillon peuvent être supprimés.', type: 'error');

                return;
            }

            // Vérifier s'il y a des paiements liés
            if ($document->payments()->exists()) {
                $this->dispatch('notify', message: 'Ce document ne peut pas être supprimé car il a des paiements associés.', type: 'error');

                return;
            }

            $document->delete();
            $this->dispatch('notify', message: 'Document supprimé avec succès.');
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la suppression du document: '.$e->getMessage());
            $this->dispatch('notify', message: 'Erreur lors de la suppression du document.', type: 'error');
        }
    }

    public function exportCsv(): \Symfony\Component\HttpFoundation\StreamedResponse
    {
        $user = Auth::user();

        $salesDocumentTypes = [
            DocumentType::Invoice,
            DocumentType::Quote,
            DocumentType::CreditNote,
            DocumentType::DeliveryNote,
            DocumentType::Proforma,
            DocumentType::Order,
        ];

        $documents = Document::where('company_id', $user->company_id)
            ->whereIn('type', $salesDocumentTypes)
            ->with(['customer:id,name', 'store:id,name'])
            ->when($this->search, fn ($query) => $query->where(function ($q) {
                $q->where('document_number', 'like', '%'.$this->search.'%')
                    ->orWhereHas('customer', fn ($sq) => $sq->where('name', 'like', '%'.$this->search.'%'));
            }))
            ->when($this->typeFilter, fn ($q) => $q->where('type', $this->typeFilter))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->showOverdueOnly, fn ($q) => $q->where('type', DocumentType::Invoice)
                ->whereNotIn('status', [DocumentStatus::Draft, DocumentStatus::Paid, DocumentStatus::Cancelled])
                ->where('due_date', '<', now()))
            ->latest('document_date')
            ->get();

        $filename = 'documents_'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($documents) {
            $handle = fopen('php://output', 'w');
            fwrite($handle, "\xEF\xBB\xBF");
            fputcsv($handle, ['N° Document', 'Type', 'Statut', 'Client', 'Date', 'Échéance', 'Total (FCFA)', 'Payé (FCFA)', 'Reste (FCFA)', 'Magasin'], ';');

            foreach ($documents as $doc) {
                fputcsv($handle, [
                    $doc->document_number,
                    $doc->type->value,
                    $doc->status->value,
                    $doc->customer?->name ?? '',
                    $doc->document_date->format('d/m/Y'),
                    $doc->due_date?->format('d/m/Y') ?? '',
                    $doc->total_amount,
                    $doc->paid_amount,
                    $doc->total_amount - $doc->paid_amount,
                    $doc->store?->name ?? '',
                ], ';');
            }

            fclose($handle);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /**
     * Marque qu'une relance a été envoyée pour une facture.
     */
    public function markReminderSent(Document $document)
    {
        $document->update(['last_reminder_sent_at' => now()]);
        $this->dispatch('notify', message: 'Relance marquée comme envoyée.');
    }

    // --- Render ---

    public function render()
    {
        $user = Auth::user();

        // Vérification de sécurité
        if (! $user || ! $user->company_id) {
            return view('livewire.saas.documents.index', [
                'documents' => collect()->paginate(15),
                'documentTypes' => [],
                'documentStatuses' => DocumentStatus::cases(),
            ]);
        }

        try {
            // On définit les types de documents qui sont considérés comme des documents de VENTE.
            $salesDocumentTypes = [
                DocumentType::Invoice,
                DocumentType::Quote,
                DocumentType::CreditNote,
                DocumentType::DeliveryNote,
                DocumentType::Proforma,
                DocumentType::Order,
            ];

            $documents = Document::where('company_id', $user->company_id)
                ->whereIn('type', $salesDocumentTypes)
                ->with(['customer:id,name,email', 'store:id,name']) // Optimisation eager loading
                ->when($this->search, function ($query) {
                    $query->where(function ($q) {
                        $q->where('document_number', 'like', '%'.$this->search.'%')
                            ->orWhereHas('customer', function ($subQuery) {
                                $subQuery->where('name', 'like', '%'.$this->search.'%');
                            });
                    });
                })
                ->when($this->typeFilter, function ($query) {
                    $query->where('type', $this->typeFilter);
                })
                ->when($this->statusFilter, function ($query) {
                    $query->where('status', $this->statusFilter);
                })
                ->when($this->showOverdueOnly, function ($query) {
                    $query->where('type', DocumentType::Invoice)
                        ->whereNotIn('status', [DocumentStatus::Paid, DocumentStatus::Cancelled])
                        ->where('due_date', '<', now());
                })
                ->latest('document_date')
                ->paginate(15);

            return view('livewire.saas.documents.index', [
                'documents' => $documents,
                'documentTypes' => $salesDocumentTypes,
                'documentStatuses' => DocumentStatus::cases(),
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur lors du chargement des documents: '.$e->getMessage());

            return view('livewire.saas.documents.index', [
                'documents' => collect()->paginate(15),
                'documentTypes' => $salesDocumentTypes ?? [],
                'documentStatuses' => DocumentStatus::cases(),
            ]);
        }
    }
}
