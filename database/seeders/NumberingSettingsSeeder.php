<?php

namespace Database\Seeders;

use App\Models\Company;
use App\Models\Setting;
use Illuminate\Database\Seeder;

class NumberingSettingsSeeder extends Seeder
{
    public function run(): void
    {
        $company = Company::first();
        if (! $company) {
            return;
        }

        $documentTypes = ['invoice', 'quote', 'credit_note', 'purchase_order'];

        foreach ($documentTypes as $type) {
            Setting::updateOrCreate(
                ['company_id' => $company->id, 'key' => "{$type}_prefix"],
                ['value' => strtoupper(substr($type, 0, 4)).'-']
            );
            Setting::updateOrCreate(
                ['company_id' => $company->id, 'key' => "{$type}_last_number"],
                ['value' => 0]
            );
        }
    }
}
