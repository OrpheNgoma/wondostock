<?php

namespace App\Services;

use App\Enums\DeliveryTripStatus;
use App\Enums\StockMovementType;
use App\Models\Product;
use App\Models\StockMovement;
use App\Models\StockPurchaseTrip;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class StockPurchaseService
{
    /**
     * Enregistre le départ du chauffeur avec les casiers vides.
     * Passe le statut : draft → in_progress
     */
    public function startTrip(StockPurchaseTrip $trip, int $emptyCrates): StockPurchaseTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::Draft, 'valider le départ');

        $trip->update([
            'empty_crates_out' => $emptyCrates,
            'status' => DeliveryTripStatus::InProgress,
            'loaded_at' => now(),
            'departed_at' => now(),
        ]);

        return $trip->fresh();
    }

    /**
     * Enregistre le retour du chauffeur avec les produits achetés (casiers pleins).
     * Crée les StockPurchaseItem et passe in_progress → completed.
     * L'inventaire n'est PAS encore impacté : il le sera après confirmation, à la clôture.
     *
     * @param  array<int, array{product_id: int|null, name: string, sku: string|null, unit_cost: int, qty: int}>  $rows
     */
    public function recordReturnWithItems(StockPurchaseTrip $trip, array $rows, int $fullCrates): StockPurchaseTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::InProgress, 'enregistrer le retour');

        $validRows = array_filter($rows, fn (array $r): bool => (int) ($r['qty'] ?? 0) >= 1);

        if (empty($validRows)) {
            throw ValidationException::withMessages([
                'returnRows' => 'Ajoutez au moins un produit acheté avec une quantité ≥ 1 avant de valider le retour.',
            ]);
        }

        DB::transaction(function () use ($trip, $validRows, $fullCrates): void {
            $trip->items()->delete();

            foreach ($validRows as $row) {
                $trip->items()->create([
                    'product_id' => $row['product_id'] ?? null,
                    'product_ref' => $row['sku'] ?? null,
                    'product_designation' => $row['name'],
                    'qty_purchased' => (int) $row['qty'],
                    'unit_cost' => (int) ($row['unit_cost'] ?? 0),
                ]);
            }

            $trip->update([
                'full_crates_in' => $fullCrates,
                'total_purchase_cost' => array_sum(array_map(
                    fn (array $r): int => (int) $r['qty'] * (int) ($r['unit_cost'] ?? 0),
                    $validRows
                )),
                'status' => DeliveryTripStatus::Completed,
                'returned_at' => now(),
            ]);
        });

        return $trip->fresh(['items']);
    }

    /**
     * Clôture le voyage après confirmation manuelle.
     *
     * À la clôture :
     *  - chaque produit acheté est ajouté à l'inventaire du dépôt de destination
     *    (mise à jour de product_store + StockMovement de type « Achat » pour la traçabilité) ;
     *  - total_purchase_cost et total_expenses sont recalculés et figés ;
     *  - la prime de mission reste figée à sa valeur enregistrée à la création.
     *
     * Passe le statut : completed → closed
     */
    public function close(StockPurchaseTrip $trip): StockPurchaseTrip
    {
        $this->assertStatus($trip, DeliveryTripStatus::Completed, 'clôturer');

        $trip->load(['items', 'expenses']);

        DB::transaction(function () use ($trip): void {
            $this->applyStockEntry($trip);

            $trip->update([
                'total_purchase_cost' => $trip->items->sum(fn ($item) => $item->qty_purchased * $item->unit_cost),
                'total_expenses' => $trip->expenses->sum('amount'),
                'status' => DeliveryTripStatus::Closed,
                'closed_at' => now(),
                'closed_by' => Auth::id(),
                'stock_applied_at' => now(),
            ]);
        });

        return $trip->fresh(['items']);
    }

    /**
     * Applique l'entrée en stock dans le dépôt de destination du voyage.
     * Met à jour le pivot product_store et trace un mouvement de stock par produit.
     */
    private function applyStockEntry(StockPurchaseTrip $trip): void
    {
        foreach ($trip->items as $item) {
            if (! $item->product_id) {
                continue;
            }

            $product = Product::where('company_id', $trip->company_id)->find($item->product_id);

            if (! $product) {
                continue;
            }

            $pivot = $product->stores()->where('store_id', $trip->store_id)->first();

            if ($pivot) {
                $product->stores()->updateExistingPivot($trip->store_id, [
                    'quantity' => $pivot->pivot->quantity + $item->qty_purchased,
                ]);
            } else {
                $product->stores()->attach($trip->store_id, ['quantity' => $item->qty_purchased]);
            }

            StockMovement::create([
                'company_id' => $trip->company_id,
                'product_id' => $product->id,
                'store_id' => $trip->store_id,
                'user_id' => Auth::id(),
                'source_type' => StockPurchaseTrip::class,
                'source_id' => $trip->id,
                'type' => StockMovementType::Purchase,
                'quantity' => $item->qty_purchased,
            ]);
        }
    }

    private function assertStatus(StockPurchaseTrip $trip, DeliveryTripStatus $expected, string $action): void
    {
        if ($trip->status !== $expected) {
            throw ValidationException::withMessages([
                'status' => "Impossible de {$action} un voyage en statut « {$trip->status->label()} ».",
            ]);
        }
    }
}
