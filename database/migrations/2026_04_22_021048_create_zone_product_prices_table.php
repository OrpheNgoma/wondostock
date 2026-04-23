<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('zone_product_prices', function (Blueprint $table): void {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('zone_id')->constrained('zones')->cascadeOnDelete();
            $table->foreignId('product_id')->constrained('products')->cascadeOnDelete();
            $table->unsignedInteger('selling_price')->default(0);
            // null = marge auto (selling_price − purchase_price), valeur = override manuel
            $table->unsignedInteger('margin_override')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // company_id inclus : contrainte explicitement scopée au tenant
            $table->unique(['company_id', 'zone_id', 'product_id'], 'zone_product_company_unique');
            $table->index(['zone_id', 'product_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('zone_product_prices');
    }
};
