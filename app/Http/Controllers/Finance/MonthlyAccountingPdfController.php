<?php

namespace App\Http\Controllers\Finance;

use App\Http\Controllers\Controller;
use App\Models\Store;
use App\Services\MonthlyAccountingPdfService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MonthlyAccountingPdfController extends Controller
{
    public function download(Request $request, MonthlyAccountingPdfService $pdfService): StreamedResponse
    {
        $request->validate([
            'store_id' => ['required', 'integer'],
            'date_from' => ['required', 'date'],
            'date_to' => ['required', 'date'],
        ]);

        $companyId = auth()->user()->company_id;

        abort_unless(
            Store::where('company_id', $companyId)->where('id', $request->store_id)->exists(),
            403
        );

        return $pdfService->download(
            $companyId,
            (int) $request->store_id,
            Carbon::parse($request->date_from),
            Carbon::parse($request->date_to)
        );
    }
}
