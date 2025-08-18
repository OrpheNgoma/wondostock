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
        Schema::table('products', function (Blueprint $table) {
            // Ajouter la colonne unit_id avec clé étrangère
            $table->unsignedBigInteger('unit_id')->nullable()->after('tax_id');
            $table->foreign('unit_id')->references('id')->on('units')->onDelete('set null');

            // Optionnel: garder l'ancienne colonne unit pour compatibilité temporaire
            // La colonne unit existante sera supprimée dans une migration future si nécessaire
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropForeign(['unit_id']);
            $table->dropColumn('unit_id');
        });
    }
};
