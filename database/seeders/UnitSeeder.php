<?php

namespace Database\Seeders;

use App\Models\Unit;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class UnitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $units = [
            ['name' => 'Pièce', 'symbol' => 'pce'],
            ['name' => 'Kilogramme', 'symbol' => 'kg'],
            ['name' => 'Gramme', 'symbol' => 'g'],
            ['name' => 'Litre', 'symbol' => 'L'],
            ['name' => 'Mètre', 'symbol' => 'm'],
            ['name' => 'Carton', 'symbol' => 'ctn'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['symbol' => $unit['symbol']], $unit);
        }
    }
}
