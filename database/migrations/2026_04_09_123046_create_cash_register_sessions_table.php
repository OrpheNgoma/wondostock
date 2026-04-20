<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cash_register_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('store_id')->constrained('stores')->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->date('session_date');
            $table->integer('opening_balance')->default(0);
            $table->integer('cash_in')->default(0);
            $table->integer('cash_out')->default(0);
            $table->integer('remittances')->default(0);
            $table->integer('closing_balance')->default(0);
            $table->string('status', 20)->default('open');
            $table->text('notes')->nullable();
            $table->timestamp('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['store_id', 'session_date']);
            $table->index('company_id');
            $table->index('session_date');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cash_register_sessions');
    }
};
