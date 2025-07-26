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
        Schema::table('plans', function (Blueprint $table) {
            // Ajouter les colonnes manquantes du modèle Plan
            $table->text('description')->nullable()->after('slug');
            $table->decimal('price', 10, 2)->default(0)->after('description');
            
            // Ajouter les colonnes pour les limites d'utilisateurs SaaS
            $table->integer('user_limit')->default(1)->after('price');
            $table->boolean('unlimited_users')->default(false)->after('user_limit');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            // Supprimer les colonnes dans l'ordre inverse
            $table->dropColumn([
                'unlimited_users',
                'user_limit',
                'price',
                'description'
            ]);
        });
    }
};
