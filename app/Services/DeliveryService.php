<?php

namespace App\Services;

use App\Enums\DeliveryTripStatus;
use App\Enums\StockMovementType;
use App\Models\DeliveryTrip;
use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DeliveryService
{
    /**
     * Charge les cassiers au départ du dépôt.
     * Passe le statut : draft → in_progress
     */
    public function load(DeliveryTrip $trip, int $loadedCrates): DeliveryTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::Draft, 'charger');

        $trip->update([
            'loaded_crates' => $loadedCrates,
            'status' => DeliveryTripStatus::InProgress,
            'loaded_at' => now(),
        ]);

        return $trip->fresh();
    }

    /**
     * Enregistre le retour du chauffeur.
     * Saisie : cassiers retournés + recette globale (si pas de détail par item).
     * Passe le statut : in_progress → completed
     */
    public function recordReturn(DeliveryTrip $trip, int $returnedCrates, int $totalRevenue): DeliveryTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::InProgress, 'enregistrer le retour');

        if ($returnedCrates > $trip->loaded_crates) {
            throw ValidationException::withMessages([
                'returned_crates' => 'Les cassiers retournés ne peuvent pas dépasser les cassiers chargés ('.$trip->loaded_crates.').',
            ]);
        }

        $trip->update([
            'returned_crates' => $returnedCrates,
            'total_revenue' => $totalRevenue,
            'status' => DeliveryTripStatus::Completed,
            'returned_at' => now(),
        ]);

        return $trip->fresh();
    }

    /**
     * Clôture la tournée.
     *
     * Calculs automatiques :
     *  - total_margin   = Σ (net_qty × margin_per_unit) sur les items
     *  - total_expenses = Σ amount sur les dépenses de tournée
     *  - bank_amount      = bank_percentage % × total_margin
     *  - cash_amount      = total_margin − bank_amount
     *  - commission_amount = 15 % × total_revenue (rémunération chauffeur)
     *  - funds_amount     = total_revenue − total_margin − total_expenses − commission_amount
     *
     * Si aucun item n'est saisi, total_margin reste 0 (calcul manuel possible).
     * Passe le statut : completed → closed
     */
    public function close(DeliveryTrip $trip): DeliveryTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::Completed, 'clôturer');

        $trip->load(['items', 'expenses']);

        // Marge calculée depuis les lignes de livraison
        $totalMargin = $trip->items->sum(fn ($item) => $item->net_qty * $item->margin_per_unit);

        // Dépenses de tournée
        $totalExpenses = $trip->expenses->sum('amount');

        // Répartition banque / caisse sur la marge (configurable via bank_percentage)
        $bankPct = $trip->bank_percentage / 100;
        $bankAmount = (int) round($totalMargin * $bankPct);
        $cashAmount = $totalMargin - $bankAmount;

        $revenue = $trip->total_revenue ?? 0;

        // Commission du chauffeur : 15 % de la recette, quelle que soit la zone
        $commission = (int) round($revenue * DeliveryTrip::COMMISSION_RATE);

        // Fonds = ce qui revient au fournisseur (recettes − marge − dépenses − commission)
        $fundsAmount = max(0, $revenue - $totalMargin - $totalExpenses - $commission);

        $trip->update([
            'total_margin' => $totalMargin,
            'total_expenses' => $totalExpenses,
            'bank_amount' => $bankAmount,
            'cash_amount' => $cashAmount,
            'funds_amount' => $fundsAmount,
            'commission_amount' => $commission,
            'mission_allowance_amount' => 0,
            'status' => DeliveryTripStatus::Closed,
            'closed_at' => now(),
            'closed_by' => Auth::id(),
        ]);

        return $trip->fresh();
    }

    /**
     * Enregistre le chargement depuis une liste de produits (un row par produit).
     * Crée les DeliveryItem et passe draft → in_progress.
     *
     * @param  array<int, array{product_id: int|null, name: string, sku: string|null, unit_price: int, margin_per_unit: int, qty: int}>  $rows
     */
    public function loadWithItems(DeliveryTrip $trip, array $rows): DeliveryTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::Draft, 'charger');

        $validRows = array_filter($rows, fn (array $r): bool => (int) ($r['qty'] ?? 0) >= 1);

        if (empty($validRows)) {
            throw ValidationException::withMessages([
                'loadingRows' => 'Ajoutez au moins un produit avec une quantité ≥ 1 avant de valider le départ.',
            ]);
        }

        DB::transaction(function () use ($trip, $validRows): void {
            $trip->items()->delete();

            foreach ($validRows as $row) {
                $trip->items()->create([
                    'product_id' => $row['product_id'] ?? null,
                    'product_ref' => $row['sku'] ?? null,
                    'product_designation' => $row['name'],
                    'qty_delivered' => (int) $row['qty'],
                    'qty_returned' => 0,
                    'unit_price' => (int) $row['unit_price'],
                    'margin_per_unit' => (int) $row['margin_per_unit'],
                ]);
            }

            $trip->update([
                'loaded_crates' => array_sum(array_column($validRows, 'qty')),
                'status' => DeliveryTripStatus::InProgress,
                'loaded_at' => now(),
            ]);
        });

        return $trip->fresh(['items']);
    }

    /**
     * Enregistre le retour depuis les quantités par item.
     * Met à jour qty_returned, calcule total_revenue et returned_crates automatiquement.
     * Passe in_progress → completed.
     *
     * @param  array<string, int>  $returnQties  item_id (string) => qty_returned
     */
    public function recordReturnFromItems(DeliveryTrip $trip, array $returnQties): DeliveryTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::InProgress, 'enregistrer le retour');

        $trip->load('items');

        DB::transaction(function () use ($trip, $returnQties): void {
            foreach ($returnQties as $itemId => $qtyReturned) {
                $item = $trip->items->firstWhere('id', (int) $itemId);
                if (! $item) {
                    continue;
                }

                $qtyReturned = max(0, (int) $qtyReturned);

                if ($qtyReturned > $item->qty_delivered) {
                    throw ValidationException::withMessages([
                        "returnQties.{$itemId}" => "Retour ({$qtyReturned}) > chargé ({$item->qty_delivered}) pour « {$item->product_designation} ».",
                    ]);
                }

                $item->update(['qty_returned' => $qtyReturned]);
            }

            $trip->load('items');

            $trip->update([
                'returned_crates' => $trip->items->sum('qty_returned'),
                'total_revenue' => $trip->items->sum(fn ($i) => $i->net_qty * $i->unit_price),
                'status' => DeliveryTripStatus::Completed,
                'returned_at' => now(),
            ]);

            // Décrémente le stock du dépôt source : uniquement les quantités vendues
            // (livrées − retournées). Effectif au retour, pas au départ.
            $this->applyStockReduction($trip);
        });

        return $trip->fresh(['items']);
    }

    /**
     * Recalcule le total_revenue depuis les items (si items saisis).
     * Utile pour synchroniser la recette avec le détail produits.
     */
    public function syncRevenueFromItems(DeliveryTrip $trip): DeliveryTrip
    {
        $revenue = $trip->items->sum(fn ($item) => $item->net_qty * $item->unit_price);

        $trip->update(['total_revenue' => $revenue]);

        return $trip->fresh();
    }

    /**
     * Décrémente l'inventaire du dépôt source de la tournée, produit par produit,
     * de la quantité réellement vendue (net = livrée − retournée).
     * Le stock est plafonné à 0 (jamais négatif) et un mouvement de type « Vente »
     * est tracé pour chaque produit. Sans dépôt source, aucune décrémentation.
     */
    private function applyStockReduction(DeliveryTrip $trip): void
    {
        if (! $trip->store_id) {
            return;
        }

        foreach ($trip->items as $item) {
            $sold = $item->net_qty;

            if (! $item->product_id || $sold <= 0) {
                continue;
            }

            $product = Product::where('company_id', $trip->company_id)->find($item->product_id);

            if (! $product) {
                continue;
            }

            $pivot = $product->stores()->where('store_id', $trip->store_id)->first();

            if ($pivot) {
                $product->stores()->updateExistingPivot($trip->store_id, [
                    'quantity' => max(0, $pivot->pivot->quantity - $sold),
                ]);
            }

            StockMovement::create([
                'company_id' => $trip->company_id,
                'product_id' => $product->id,
                'store_id' => $trip->store_id,
                'user_id' => Auth::id(),
                'source_type' => DeliveryTrip::class,
                'source_id' => $trip->id,
                'type' => StockMovementType::Sale,
                'quantity' => -$sold,
            ]);
        }
    }

    private function assertStatus(DeliveryTrip $trip, DeliveryTripStatus $expected, string $action): void
    {
        if ($trip->status !== $expected) {
            throw ValidationException::withMessages([
                'status' => "Impossible de {$action} une tournée en statut « {$trip->status->label()} ».",
            ]);
        }
    }
}
