<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_purchase_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('stock_purchase_trips')->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            $table->string('product_ref')->nullable();
            $table->string('product_designation');
            $table->integer('qty_purchased');   // Quantité achetée (casiers pleins ramenés)
            $table->integer('unit_cost');       // Coût d'achat unitaire en FCFA
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_purchase_items');
    }
};
