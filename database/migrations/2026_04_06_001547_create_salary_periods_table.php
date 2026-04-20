<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->string('label', 100);
            $table->tinyInteger('month');
            $table->smallInteger('year');
            $table->string('status', 20)->default('draft');
            $table->integer('total_gross')->default(0);
            $table->integer('total_deductions')->default(0);
            $table->integer('total_net')->default(0);
            $table->integer('total_advances')->default(0);
            $table->timestamp('validated_at')->nullable();
            $table->foreignId('validated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->unique(['company_id', 'month', 'year']);
            $table->index('company_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_periods');
    }
};
