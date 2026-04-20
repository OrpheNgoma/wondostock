<?php

namespace App\Services;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use App\Enums\StockMovementType;
use App\Models\CashRegisterSession;
use App\Models\CashRemittance;
use App\Models\Document;
use App\Models\DocumentItem;
use App\Models\Expense;
use App\Models\Product;
use App\Models\Store;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class MonthlyAccountingService
{
    /**
     * @return array{
     *   store: Store,
     *   period: array{from: Carbon, to: Carbon},
     *   inventory_snapshot: Collection,
     *   sales: Collection,
     *   pending_invoices: Collection,
     *   expenses: Collection,
     *   remittances_total: int,
     *   expenses_total: int,
     *   sales_total: int,
     *   pending_total: int,
     *   closing_balance: int
     * }
     */
    public function getReport(int $companyId, int $storeId, Carbon $from, Carbon $to): array
    {
        $store = Store::where('company_id', $companyId)->findOrFail($storeId);

        return [
            'store' => $store,
            'period' => ['from' => $from, 'to' => $to],
            'inventory_snapshot' => $this->buildInventorySnapshot($companyId, $storeId, $from, $to),
            'sales' => $this->getSales($companyId, $storeId, $from, $to),
            'pending_invoices' => $this->getPendingInvoices($companyId, $storeId),
            'expenses' => $this->getExpenses($companyId, $storeId, $from, $to),
            'remittances_total' => $this->getRemittancesTotal($companyId, $storeId, $from, $to),
            'expenses_total' => $this->getExpensesTotal($companyId, $storeId, $from, $to),
            'sales_total' => $this->getSalesTotal($companyId, $storeId, $from, $to),
            'pending_total' => $this->getPendingTotal($companyId, $storeId),
            'closing_balance' => $this->getClosingBalance($companyId, $storeId, $to),
        ];
    }

    private function buildInventorySnapshot(int $companyId, int $storeId, Carbon $from, Carbon $to): Collection
    {
        // La relation est Product->stores(), on requête depuis Product avec un whereHas sur le store
        $products = Product::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('is_active', true)
            ->whereHas('stores', fn ($q) => $q->where('stores.id', $storeId))
            ->with([
                'category',
                'unit',
                'stores' => fn ($q) => $q->where('stores.id', $storeId),
            ])
            ->orderBy('name')
            ->get();

        return $products->map(function ($product) use ($storeId, $from, $to) {
            $stockPivot = (int) ($product->stores->first()?->pivot->quantity ?? 0);

            // Quantité vendue sur la période : depuis les lignes de documents finalisés
            $vendu = (int) DocumentItem::withoutGlobalScopes()
                ->where('product_id', $product->id)
                ->whereHas('document', function ($q) use ($storeId, $from, $to) {
                    $q->where('store_id', $storeId)
                        ->whereIn('type', [DocumentType::Invoice->value, DocumentType::DeliveryNote->value])
                        ->whereIn('status', [
                            DocumentStatus::Validated->value,
                            DocumentStatus::Paid->value,
                            DocumentStatus::PartiallyPaid->value,
                        ])
                        ->whereBetween('document_date', [$from->toDateString(), $to->toDateString()]);
                })
                ->sum('quantity');

            // Ventes déjà reflétées dans stock_movements (donc déjà déduites du pivot)
            $venduDansSM = abs((int) \App\Models\StockMovement::withoutGlobalScopes()
                ->where('product_id', $product->id)
                ->where('store_id', $storeId)
                ->where('type', StockMovementType::Sale->value)
                ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                ->sum('quantity'));

            // Ventes non encore déduites du pivot (cas de factures sans stock_movement généré)
            $venduNonReflete = max(0, $vendu - $venduDansSM);

            // Reste en stock = pivot corrigé des ventes non encore reflétées
            $stockActuel = $stockPivot - $venduNonReflete;

            $ravitaillement = (int) \App\Models\StockMovement::withoutGlobalScopes()
                ->where('product_id', $product->id)
                ->where('store_id', $storeId)
                ->whereIn('type', [
                    StockMovementType::Purchase->value,
                    StockMovementType::TransferIn->value,
                    StockMovementType::Production->value,
                ])
                ->whereBetween('created_at', [$from->startOfDay(), $to->endOfDay()])
                ->sum('quantity');

            // Qté avant = stock de fin de période + vendu - ravitaillement
            $qteBefore = $stockActuel - $ravitaillement + $vendu;

            $unit = $product->getRelationValue('unit');
            $unitLabel = ($unit instanceof \App\Models\Unit)
                ? ($unit->symbol ?: $unit->name)
                : '';

            return (object) [
                'product_name' => $product->name,
                'product_sku' => $product->sku,
                'category_name' => $product->category?->name ?? 'Sans catégorie',
                'unit_label' => $unitLabel,
                'tarif' => $product->selling_price,
                'qte_avant' => $qteBefore,
                'ravitaillement' => $ravitaillement,
                'vendu' => $vendu,
                'reste' => $stockActuel,
            ];
        });
    }

    private function getSales(int $companyId, int $storeId, Carbon $from, Carbon $to): Collection
    {
        return Document::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereIn('type', [DocumentType::Invoice->value, DocumentType::DeliveryNote->value])
            ->whereIn('status', [
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
                DocumentStatus::Validated->value,
            ])
            ->whereBetween('document_date', [$from->toDateString(), $to->toDateString()])
            ->with(['customer', 'payments', 'items.product'])
            ->orderBy('document_date')
            ->get();
    }

    private function getPendingInvoices(int $companyId, int $storeId): Collection
    {
        return Document::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereIn('type', [DocumentType::Invoice->value])
            ->whereIn('status', [
                DocumentStatus::Sent->value,
                DocumentStatus::Validated->value,
                DocumentStatus::PartiallyPaid->value,
                DocumentStatus::Overdue->value,
            ])
            ->whereRaw('total_amount > paid_amount')
            ->with(['customer', 'items.product'])
            ->orderBy('document_date')
            ->get();
    }

    private function getExpenses(int $companyId, int $storeId, Carbon $from, Carbon $to): Collection
    {
        return Expense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->with('category')
            ->orderBy('expense_date')
            ->get();
    }

    private function getRemittancesTotal(int $companyId, int $storeId, Carbon $from, Carbon $to): int
    {
        return (int) CashRemittance::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereBetween('remittance_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');
    }

    private function getExpensesTotal(int $companyId, int $storeId, Carbon $from, Carbon $to): int
    {
        return (int) Expense::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereBetween('expense_date', [$from->toDateString(), $to->toDateString()])
            ->sum('amount');
    }

    private function getSalesTotal(int $companyId, int $storeId, Carbon $from, Carbon $to): int
    {
        return (int) Document::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereIn('type', [DocumentType::Invoice->value, DocumentType::DeliveryNote->value])
            ->whereIn('status', [
                DocumentStatus::Paid->value,
                DocumentStatus::PartiallyPaid->value,
                DocumentStatus::Validated->value,
            ])
            ->whereBetween('document_date', [$from->toDateString(), $to->toDateString()])
            ->sum('paid_amount');
    }

    private function getPendingTotal(int $companyId, int $storeId): int
    {
        return (int) Document::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereIn('type', [DocumentType::Invoice->value])
            ->whereIn('status', [
                DocumentStatus::Sent->value,
                DocumentStatus::Validated->value,
                DocumentStatus::PartiallyPaid->value,
                DocumentStatus::Overdue->value,
            ])
            ->whereRaw('total_amount > paid_amount')
            ->sum(\Illuminate\Support\Facades\DB::raw('total_amount - paid_amount'));
    }

    private function getClosingBalance(int $companyId, int $storeId, Carbon $to): int
    {
        $session = CashRegisterSession::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereDate('session_date', '<=', $to->toDateString())
            ->orderByDesc('session_date')
            ->first();

        return $session ? $session->closing_balance : 0;
    }
}
