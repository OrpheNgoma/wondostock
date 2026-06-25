<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('stock_purchase_trips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('driver_id')->constrained('drivers')->cascadeOnDelete();
            $table->foreignId('vehicle_id')->nullable()->constrained('vehicles')->nullOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete(); // Dépôt de destination du stock acheté
            $table->foreignId('supplier_id')->nullable()->constrained('suppliers')->nullOnDelete();
            $table->date('trip_date');
            $table->string('status')->default('draft');

            // Casiers : vides au départ, pleins au retour
            $table->integer('empty_crates_out')->nullable();
            $table->integer('full_crates_in')->nullable();

            // Financier — calculé à la clôture
            $table->integer('total_purchase_cost')->nullable();      // Σ (qté × coût unitaire) sur les items
            $table->integer('total_expenses')->nullable();           // Σ dépenses du voyage
            $table->integer('mission_allowance_amount')->nullable(); // Prime de déplacement (défaut 10 000 FCFA)

            $table->text('notes')->nullable();
            $table->timestamp('loaded_at')->nullable();     // Départ (casiers vides)
            $table->timestamp('departed_at')->nullable();
            $table->timestamp('returned_at')->nullable();   // Retour (casiers pleins)
            $table->timestamp('closed_at')->nullable();
            $table->timestamp('stock_applied_at')->nullable(); // Entrée en inventaire confirmée
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
        Schema::dropIfExists('stock_purchase_trips');
    }
};
