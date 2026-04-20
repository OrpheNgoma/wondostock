<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('delivery_trips');
        Schema::create('delivery_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('zone_id')->nullable()->constrained('zones')->nullOnDelete();
            $table->date('trip_date');
            $table->string('status')->default('draft');

            // Chargement / retour (cassiers)
            $table->integer('loaded_crates')->nullable();
            $table->integer('returned_crates')->nullable();

            // Financier — saisi au retour
            $table->integer('total_revenue')->nullable();       // Recettes brutes

            // Financier — calculé à la clôture
            $table->integer('total_margin')->nullable();        // Σ marge sur items
            $table->integer('total_expenses')->nullable();      // Σ dépenses de tournée
            $table->integer('bank_percentage')->default(80);    // % marge → banque (configurable)
            $table->integer('bank_amount')->nullable();         // bank_percentage % de total_margin
            $table->integer('cash_amount')->nullable();         // reste de total_margin
            $table->integer('funds_amount')->nullable();        // total_revenue - total_margin - total_expenses
            $table->integer('mission_allowance_amount')->nullable(); // Prime de mission (zone)

            $table->text('notes')->nullable();
            $table->timestamp('loaded_at')->nullable();
            $table->timestamp('departed_at')->nullable();
            $table->timestamp('returned_at')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->foreignId('closed_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index('company_id');
            $table->index('driver_id');
            $table->index('trip_date');
            $table->index('status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_trips');
    }
};
