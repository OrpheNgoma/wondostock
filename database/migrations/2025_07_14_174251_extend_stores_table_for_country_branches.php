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
        Schema::table('stores', function (Blueprint $table) {
            // Indicateur si ce magasin est une branche pays
            $table->boolean('is_country_branch')->default(false);

            // Informations du pays
            $table->string('country_code', 3)->nullable(); // GAB, CMR, CG, CD
            $table->string('country_name')->nullable();

            // Informations légales spécifiques au pays
            $table->string('nif')->nullable();
            $table->string('rccm')->nullable();
            $table->string('business_permit')->nullable();
            $table->string('tax_id')->nullable();

            // Contact spécifique au pays
            $table->string('email')->nullable();
            $table->string('website')->nullable();
            $table->string('postal_box')->nullable(); // BP

            // Champs pour les images d'en-tête et pied de page
            $table->string('invoice_header_image')->nullable();
            $table->string('invoice_footer_image')->nullable();

            // Indexation pour les requêtes
            $table->index('is_country_branch');
            $table->index('country_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stores', function (Blueprint $table) {
            $table->dropIndex(['is_country_branch']);
            $table->dropIndex(['country_code']);

            $table->dropColumn([
                'is_country_branch',
                'country_code',
                'country_name',
                'nif',
                'rccm',
                'business_permit',
                'tax_id',
                'email',
                'website',
                'postal_box',
                'invoice_header_image',
                'invoice_footer_image',
            ]);
        });
    }
};
