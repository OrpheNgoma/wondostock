<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('slip_id')->constrained('salary_slips')->cascadeOnDelete();
            $table->string('label', 150);
            $table->string('type', 20); // deduction|bonus
            $table->integer('amount');
            $table->timestamps();

            $table->index('slip_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_deductions');
    }
};
