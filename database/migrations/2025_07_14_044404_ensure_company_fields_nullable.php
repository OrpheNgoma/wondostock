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
        Schema::table('companies', function (Blueprint $table) {
            // S'assurer que tous les champs optionnels sont nullable
            $table->string('legal_name')->nullable()->change();
            $table->string('email')->nullable()->change();
            $table->string('phone_number')->nullable()->change();
            $table->text('address')->nullable()->change();
            $table->string('rccm')->nullable()->change();
            $table->string('nif')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            // Remettre les champs comme non-nullable (si besoin de rollback)
            $table->string('legal_name')->nullable(false)->change();
            $table->string('email')->nullable(false)->change();
            $table->string('phone_number')->nullable(false)->change();
            $table->text('address')->nullable(false)->change();
            $table->string('rccm')->nullable(false)->change();
            $table->string('nif')->nullable(false)->change();
        });
    }
};
