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
        Schema::table('delivery_items', function (Blueprint $table) {
            if (!Schema::hasColumn('delivery_items', 'product_id')) {
                $table->foreignId('product_id')->nullable()->after('customer_id')->constrained('products')->nullOnDelete();
                $table->index('product_id');
            }
            if (!Schema::hasColumn('delivery_items', 'product_ref')) {
                $table->string('product_ref')->nullable()->after('product_id');
            }
            if (!Schema::hasColumn('delivery_items', 'product_designation')) {
                $table->string('product_designation')->default('')->after('product_ref');
            }
            if (!Schema::hasColumn('delivery_items', 'margin_per_unit')) {
                $table->integer('margin_per_unit')->default(0)->after('unit_price');
            }
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
