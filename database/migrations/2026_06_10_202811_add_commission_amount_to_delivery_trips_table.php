<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasColumn('delivery_trips', 'commission_amount')) {
            return;
        }

        Schema::table('delivery_trips', function (Blueprint $table) {
            $table->integer('commission_amount')->nullable()->after('mission_allowance_amount'); // 15% de la recette, versé au chauffeur
        });
    }

    public function down(): void
    {
        if (! Schema::hasColumn('delivery_trips', 'commission_amount')) {
            return;
        }

        Schema::table('delivery_trips', function (Blueprint $table) {
            $table->dropColumn('commission_amount');
        });
    }
};
