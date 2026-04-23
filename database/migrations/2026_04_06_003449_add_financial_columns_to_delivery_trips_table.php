<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('delivery_trips', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_trips', 'total_margin')) {
                $table->integer('total_margin')->default(0)->after('total_revenue');
            }
            if (!Schema::hasColumn('delivery_trips', 'total_expenses')) {
                $table->integer('total_expenses')->default(0)->after('total_margin');
            }
            if (!Schema::hasColumn('delivery_trips', 'bank_percentage')) {
                $table->integer('bank_percentage')->default(80)->after('total_expenses');
            }
            if (!Schema::hasColumn('delivery_trips', 'bank_amount')) {
                $table->integer('bank_amount')->default(0)->after('bank_percentage');
            }
            if (!Schema::hasColumn('delivery_trips', 'cash_amount')) {
                $table->integer('cash_amount')->default(0)->after('bank_amount');
            }
            if (!Schema::hasColumn('delivery_trips', 'funds_amount')) {
                $table->integer('funds_amount')->default(0)->after('cash_amount');
            }
        });
    }

    public function down(): void
    {
        Schema::table('delivery_trips', function (Blueprint $table) {
            $table->dropColumn([
                'total_margin',
                'total_expenses',
                'bank_percentage',
                'bank_amount',
                'cash_amount',
                'funds_amount',
            ]);
        });
    }
};
