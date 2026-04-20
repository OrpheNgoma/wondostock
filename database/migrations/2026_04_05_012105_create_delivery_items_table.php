<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('delivery_items');
        Schema::create('delivery_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('delivery_trips')->cascadeOnDelete();
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_ref')->nullable();          // Référence conservée si produit supprimé
            $table->string('product_designation');              // Nom affiché (REGAB, CASTEL…)
            $table->integer('qty_delivered')->default(0);
            $table->integer('qty_returned')->default(0);
            $table->integer('unit_price')->default(0);         // Prix de vente par cassier
            $table->integer('margin_per_unit')->default(0);    // Marge par cassier vendu
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('trip_id');
            $table->index('product_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_items');
    }
};
