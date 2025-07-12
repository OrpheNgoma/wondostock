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
        Schema::create('metrics', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('company_id')->nullable();
            $table->string('type'); // 'login', 'product_created', 'document_created', etc.
            $table->string('category'); // 'user_activity', 'business_activity', 'system'
            $table->json('data')->nullable(); // données additionnelles
            $table->decimal('value', 15, 2)->nullable(); // valeur numérique si applicable
            $table->timestamp('recorded_at');
            $table->timestamps();

            $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
            $table->index(['company_id', 'type', 'recorded_at']);
            $table->index(['category', 'recorded_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('metrics');
    }
};
