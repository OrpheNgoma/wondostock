<?php

namespace App\Services;

use App\Enums\DeliveryTripStatus;
use App\Models\DeliveryTrip;
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
     *  - bank_amount    = bank_percentage % × total_margin
     *  - cash_amount    = total_margin − bank_amount
     *  - funds_amount   = total_revenue − total_margin − total_expenses
     *
     * Si aucun item n'est saisi, total_margin reste 0 (calcul manuel possible).
     * Passe le statut : completed → closed
     */
    public function close(DeliveryTrip $trip): DeliveryTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::Completed, 'clôturer');

        $trip->load(['items', 'expenses', 'zone']);

        // Marge calculée depuis les lignes de livraison
        $totalMargin = $trip->items->sum(fn ($item) => $item->net_qty * $item->margin_per_unit);

        // Dépenses de tournée
        $totalExpenses = $trip->expenses->sum('amount');

        // Répartition banque / caisse sur la marge (configurable via bank_percentage)
        $bankPct = $trip->bank_percentage / 100;
        $bankAmount = (int) round($totalMargin * $bankPct);
        $cashAmount = $totalMargin - $bankAmount;

        // Fonds = ce qui revient au fournisseur (recettes − marge − dépenses)
        $revenue = $trip->total_revenue ?? 0;
        $fundsAmount = max(0, $revenue - $totalMargin - $totalExpenses);

        // Prime de mission depuis la zone
        $missionAllowance = $trip->zone?->mission_allowance ?? 0;

        $trip->update([
            'total_margin' => $totalMargin,
            'total_expenses' => $totalExpenses,
            'bank_amount' => $bankAmount,
            'cash_amount' => $cashAmount,
            'funds_amount' => $fundsAmount,
            'mission_allowance_amount' => $missionAllowance,
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

    private function assertStatus(DeliveryTrip $trip, DeliveryTripStatus $expected, string $action): void
    {
        if ($trip->status !== $expected) {
            throw ValidationException::withMessages([
                'status' => "Impossible de {$action} une tournée en statut « {$trip->status->label()} ».",
            ]);
        }
    }
}
