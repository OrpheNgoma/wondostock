<?php

namespace App\Services;

use App\Models\CashRegisterSession;
use App\Models\CashRemittance;
use Illuminate\Support\Facades\Auth;

class CashRemittanceService
{
    public function create(array $data, int $companyId): CashRemittance
    {
        $remittance = CashRemittance::create(array_merge($data, [
            'company_id' => $companyId,
            'user_id' => $data['user_id'] ?? Auth::id(),
        ]));

        if (! empty($data['store_id']) && ! empty($data['remittance_date'])) {
            $session = CashRegisterSession::withoutGlobalScopes()
                ->where('company_id', $companyId)
                ->where('store_id', $data['store_id'])
                ->whereDate('session_date', $data['remittance_date'])
                ->first();

            if ($session) {
                $session->increment('remittances', $data['amount']);
                $session->refresh();
                $session->update(['closing_balance' => $session->computeClosingBalance()]);
            }
        }

        return $remittance;
    }

    public function getMonthlyTotal(int $companyId, int $storeId, int $month, int $year): int
    {
        return (int) CashRemittance::withoutGlobalScopes()
            ->where('company_id', $companyId)
            ->where('store_id', $storeId)
            ->whereMonth('remittance_date', $month)
            ->whereYear('remittance_date', $year)
            ->sum('amount');
    }
}
