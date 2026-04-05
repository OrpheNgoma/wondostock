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
        Schema::table('payments', function (Blueprint $table) {
            // Ajouter les colonnes manquantes pour le système SaaS
            $table->string('status')->default('pending')->after('amount');
            $table->string('transaction_id')->unique()->nullable()->after('status');
            $table->string('payment_method')->change(); // S'assurer que c'est une string
            $table->timestamp('paid_at')->nullable()->after('payment_date');
            $table->timestamp('processed_at')->nullable()->after('paid_at');
            $table->text('gateway_response')->nullable()->after('notes');

            // Renommer payment_date en paid_at si nécessaire et ajuster
            // Note: Laravel ne permet pas de renommer et modifier en même temps

            // Ajouter des index pour les performances
            $table->index('status');
            $table->index('paid_at');
            $table->index('transaction_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropColumn(['status', 'transaction_id', 'paid_at', 'processed_at', 'gateway_response']);
            $table->dropIndex(['payments_status_index']);
            $table->dropIndex(['payments_paid_at_index']);
            $table->dropIndex(['payments_transaction_id_index']);
        });
    }
};
