<?php

namespace App\Http\Controllers\Salaries;

use App\Http\Controllers\Controller;
use App\Models\SalarySlip;
use App\Services\SalaryPdfService;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalaryPdfController extends Controller
{
    public function download(SalarySlip $slip): StreamedResponse
    {
        if ($slip->company_id !== Auth::user()->company_id) {
            abort(403);
        }

        return app(SalaryPdfService::class)->downloadBulletin($slip);
    }
}
