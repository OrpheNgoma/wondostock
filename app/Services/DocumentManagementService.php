<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\StockMovementType;
use App\Models\Document;
use App\Models\DocumentItem;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class DocumentManagementService
{
    public function __construct(
        private DocumentNumberService $documentNumberService,
        private InventoryService $inventoryService,
        private DashboardCacheService $dashboardCacheService
    ) {}

    /**
     * Create a new document with items
     */
    public function createDocument(array $documentData, array $items): Document
    {
        return DB::transaction(function () use ($documentData, $items) {
            // Generate document number
            $documentData['document_number'] = $this->documentNumberService->generate(
                $documentData['company_id'],
                DocumentType::from($documentData['type'])
            );

            // Create the document
            $document = Document::create($documentData);

            // Add items to the document
            $this->addItemsToDocument($document, $items);

            // Update stock if it's an invoice
            if ($document->type === DocumentType::Invoice && $document->status === DocumentStatus::Validated) {
                $this->updateStockForDocument($document);
            }

            // Calculate totals
            $this->recalculateDocumentTotals($document);

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($document->company_id);

            return $document->fresh(['items.product', 'customer']);
        });
    }

    /**
     * Update document status
     */
    public function updateDocumentStatus(Document $document, DocumentStatus $newStatus): Document
    {
        return DB::transaction(function () use ($document, $newStatus) {
            $oldStatus = $document->status;

            // Update status
            $document->update(['status' => $newStatus]);

            // Handle stock movements based on status change
            if ($document->type === DocumentType::Invoice) {
                $this->handleInvoiceStatusChange($document, $oldStatus, $newStatus);
            }

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($document->company_id);

            return $document->fresh();
        });
    }

    /**
     * Add items to a document
     */
    public function addItemsToDocument(Document $document, array $items): void
    {
        foreach ($items as $itemData) {
            $product = Product::findOrFail($itemData['product_id']);

            // Calculate totals
            $unitPrice = $itemData['unit_price'] ?? $product->selling_price;
            $quantity = $itemData['quantity'];
            $totalAmount = $unitPrice * $quantity;

            DocumentItem::create([
                'document_id' => $document->id,
                'product_id' => $product->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'total_amount' => $totalAmount,
                'description' => $itemData['description'] ?? $product->name,
            ]);
        }
    }

    /**
     * Update item in document
     */
    public function updateDocumentItem(DocumentItem $item, array $data): DocumentItem
    {
        return DB::transaction(function () use ($item, $data) {
            $document = $item->document;

            // Update the item
            $item->update($data);

            // Recalculate item total if price or quantity changed
            if (isset($data['unit_price']) || isset($data['quantity'])) {
                $item->update([
                    'total_amount' => $item->unit_price * $item->quantity,
                ]);
            }

            // Recalculate document totals
            $this->recalculateDocumentTotals($document);

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($document->company_id);

            return $item->fresh();
        });
    }

    /**
     * Remove item from document
     */
    public function removeDocumentItem(DocumentItem $item): void
    {
        DB::transaction(function () use ($item) {
            $document = $item->document;

            $item->delete();

            // Recalculate document totals
            $this->recalculateDocumentTotals($document);

            // Invalidate dashboard cache
            $this->dashboardCacheService->invalidateKPIs($document->company_id);
        });
    }

    /**
     * Convert document to another type (e.g., quote to invoice)
     */
    public function convertDocument(Document $sourceDocument, DocumentType $targetType): Document
    {
        return DB::transaction(function () use ($sourceDocument, $targetType) {
            // Create new document based on source
            $newDocumentData = $sourceDocument->toArray();
            unset($newDocumentData['id'], $newDocumentData['created_at'], $newDocumentData['updated_at']);

            $newDocumentData['type'] = $targetType;
            $newDocumentData['status'] = DocumentStatus::Draft;
            $newDocumentData['document_number'] = $this->documentNumberService->generate(
                $sourceDocument->company_id,
                $targetType
            );
            $newDocumentData['converted_from_id'] = $sourceDocument->id;

            $newDocument = Document::create($newDocumentData);

            // Copy items
            foreach ($sourceDocument->items as $item) {
                DocumentItem::create([
                    'document_id' => $newDocument->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->unit_price,
                    'total_amount' => $item->total_amount,
                    'description' => $item->description,
                ]);
            }

            // Recalculate totals
            $this->recalculateDocumentTotals($newDocument);

            return $newDocument->fresh(['items.product', 'customer']);
        });
    }

    /**
     * Get document statistics for a company
     */
    public function getDocumentStatistics(int $companyId, ?\Carbon\Carbon $startDate = null, ?\Carbon\Carbon $endDate = null): array
    {
        $query = Document::where('company_id', $companyId);

        if ($startDate) {
            $query->where('document_date', '>=', $startDate);
        }

        if ($endDate) {
            $query->where('document_date', '<=', $endDate);
        }

        $stats = $query->selectRaw('
            type,
            status,
            COUNT(*) as count,
            SUM(total_amount) as total_amount,
            AVG(total_amount) as average_amount
        ')
            ->groupBy('type', 'status')
            ->get()
            ->groupBy('type');

        $result = [];
        foreach (DocumentType::cases() as $type) {
            $typeStats = $stats->get($type->value, collect());
            $result[$type->value] = [
                'total_count' => $typeStats->sum('count'),
                'total_amount' => $typeStats->sum('total_amount'),
                'average_amount' => $typeStats->avg('average_amount'),
                'by_status' => $typeStats->keyBy('status')->toArray(),
            ];
        }

        return $result;
    }

    private function recalculateDocumentTotals(Document $document): void
    {
        $subtotal = $document->items()->sum(DB::raw('quantity * unit_price'));
        $taxAmount = $subtotal * ($document->tax_rate / 100);
        $discountAmount = $subtotal * ($document->discount_rate / 100);
        $total = $subtotal + $taxAmount - $discountAmount;

        $document->update([
            'subtotal_amount' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $discountAmount,
            'total_amount' => $total,
        ]);
    }

    private function updateStockForDocument(Document $document): void
    {
        if (! $document->store_id) {
            return; // No store specified, can't update stock
        }

        foreach ($document->items as $item) {
            StockMovement::create([
                'company_id' => $document->company_id,
                'product_id' => $item->product_id,
                'store_id' => $document->store_id,
                'type' => StockMovementType::Sale,
                'quantity' => -$item->quantity, // Negative because it's a sale
                'unit_cost' => $item->product->purchase_price,
                'reason' => "Sale - Document {$document->document_number}",
                'reference' => $document->document_number,
            ]);

            // Update product stock in store
            $this->inventoryService->removeStock(
                $item->product,
                $document->store,
                $item->quantity,
                "Sale - Document {$document->document_number}"
            );
        }
    }

    private function handleInvoiceStatusChange(Document $document, DocumentStatus $oldStatus, DocumentStatus $newStatus): void
    {
        // If invoice is being validated and wasn't validated before
        if ($newStatus === DocumentStatus::Validated && $oldStatus !== DocumentStatus::Validated) {
            $this->updateStockForDocument($document);
        }

        // If invoice is being cancelled/reverted from validated
        if ($oldStatus === DocumentStatus::Validated && $newStatus !== DocumentStatus::Validated) {
            $this->revertStockForDocument($document);
        }
    }

    private function revertStockForDocument(Document $document): void
    {
        if (! $document->store_id) {
            return;
        }

        foreach ($document->items as $item) {
            // Add stock back
            $this->inventoryService->addStock(
                $item->product,
                $document->store,
                $item->quantity,
                "Reverted sale - Document {$document->document_number}",
                $item->product->purchase_price
            );
        }
    }
}
