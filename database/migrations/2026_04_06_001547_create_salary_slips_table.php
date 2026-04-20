<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_slips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('period_id')->constrained('salary_periods')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('employee_name', 255);
            $table->integer('base_salary');
            $table->integer('gross_salary');
            $table->integer('total_deductions')->default(0);
            $table->integer('total_advances')->default(0);
            $table->integer('net_salary');
            $table->integer('trips_count')->default(0);
            $table->integer('total_commissions')->default(0);
            $table->integer('mission_allowances')->default(0);
            $table->string('status', 20)->default('draft');
            $table->timestamp('paid_at')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('period_id');
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_slips');
    }
};
