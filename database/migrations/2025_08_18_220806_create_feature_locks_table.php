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
        Schema::create('feature_locks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')
                ->constrained('companies')
                ->onDelete('cascade')
                ->comment('Entreprise concernée par le verrouillage');

            $table->string('feature_key', 100)
                ->comment('Clé unique de la fonctionnalité (ex: products.manage)');

            $table->boolean('is_locked')
                ->default(false)
                ->comment('État du verrouillage de la fonctionnalité');

            $table->text('reason')
                ->nullable()
                ->comment('Raison du verrouillage (visible par l\'entreprise)');

            $table->timestamp('locked_at')
                ->nullable()
                ->comment('Date/heure du verrouillage');

            $table->timestamp('expires_at')
                ->nullable()
                ->comment('Date d\'expiration automatique (optionnel)');

            $table->foreignId('locked_by')
                ->nullable()
                ->constrained('users')
                ->onDelete('set null')
                ->comment('Admin global qui a effectué le verrouillage');

            $table->json('metadata')
                ->nullable()
                ->comment('Métadonnées additionnelles (notifications, etc.)');

            $table->timestamps();

            // Contraintes d'intégrité
            $table->unique(['company_id', 'feature_key'], 'unique_company_feature');

            // Index pour les requêtes fréquentes
            $table->index(['feature_key', 'is_locked'], 'idx_feature_status');
            $table->index(['company_id', 'is_locked'], 'idx_company_locked');
            $table->index('expires_at', 'idx_expiration');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feature_locks');
    }
};
