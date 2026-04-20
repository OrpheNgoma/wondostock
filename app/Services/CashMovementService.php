<?php

namespace App\Services;

use App\Enums\CashMovementType;
use App\Models\CashMovement;
use App\Models\CashRegisterSession;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CashMovementService
{
    /**
     * Trouve la session ouverte pour un magasin à une date donnée.
     */
    public function findOpenSession(int $companyId, int $storeId, string $date): ?CashRegisterSession
    {
        return CashRegisterSession::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->where('session_date', $date)
            ->where('status', 'open')
            ->first();
    }

    /**
     * Ajoute un mouvement de caisse et met à jour les totaux de la session.
     */
    public function addMovement(
        CashRegisterSession $session,
        CashMovementType $type,
        int $amount,
        string $label,
        ?int $expenseId = null,
        ?int $userId = null
    ): CashMovement {
        return DB::transaction(function () use ($session, $type, $amount, $label, $expenseId, $userId) {
            $movement = CashMovement::create([
                'company_id' => $session->company_id,
                'session_id' => $session->id,
                'store_id' => $session->store_id,
                'user_id' => $userId ?? Auth::id(),
                'expense_id' => $expenseId,
                'type' => $type,
                'amount' => $amount,
                'label' => $label,
                'movement_date' => $session->session_date,
            ]);

            $this->syncSessionTotals($session);

            return $movement;
        });
    }

    /**
     * Supprime un mouvement et resynchronise les totaux de la session.
     */
    public function deleteMovement(CashMovement $movement): void
    {
        DB::transaction(function () use ($movement) {
            $session = $movement->session;
            $movement->delete();
            $this->syncSessionTotals($session);
        });
    }

    /**
     * Recalcule cash_in, cash_out, remittances et closing_balance depuis les mouvements.
     */
    public function syncSessionTotals(CashRegisterSession $session): void
    {
        $movements = CashMovement::withoutGlobalScopes()
            ->where('session_id', $session->id)
            ->get();

        $cashIn = (int) $movements->where('type', CashMovementType::CashIn)->sum('amount');
        $cashOut = (int) $movements->where('type', CashMovementType::CashOut)->sum('amount');
        $remittances = (int) $movements->where('type', CashMovementType::Remittance)->sum('amount');

        $closingBalance = $session->opening_balance + $cashIn - $cashOut - $remittances;

        $session->update([
            'cash_in' => $cashIn,
            'cash_out' => $cashOut,
            'remittances' => $remittances,
            'closing_balance' => $closingBalance,
        ]);
    }
}
