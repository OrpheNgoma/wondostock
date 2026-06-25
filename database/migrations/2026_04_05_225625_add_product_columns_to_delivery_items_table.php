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
        if (Schema::hasColumn('delivery_items', 'product_id')) {
            return;
        }

        Schema::table('delivery_items', function (Blueprint $table) {
            $table->foreignId('product_id')->nullable()->after('customer_id')->constrained('products')->nullOnDelete();
            $table->string('product_ref')->nullable()->after('product_id');
            $table->string('product_designation')->default('')->after('product_ref');
            $table->integer('margin_per_unit')->default(0)->after('unit_price');

            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_items', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropIndex(['product_id']);
            $table->dropColumn(['product_id', 'product_ref', 'product_designation', 'margin_per_unit']);
        });
    }
};
