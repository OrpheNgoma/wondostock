<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('delivery_expenses');
        Schema::create('delivery_expenses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('trip_id')->constrained('delivery_trips')->cascadeOnDelete();
            $table->foreignId('category_id')
                ->nullable()
                ->constrained('delivery_expense_categories')
                ->nullOnDelete();
            $table->string('label');       // Ex: "Carburant KW 516 AA"
            $table->integer('amount');     // Montant en FCFA
            $table->timestamps();

            $table->index('trip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_expenses');
    }
};
