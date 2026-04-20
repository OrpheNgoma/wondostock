<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('salary_advances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained('companies')->cascadeOnDelete();
            $table->foreignId('driver_id')->nullable()->constrained('drivers')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('requested_by')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('amount');
            $table->date('advance_date');
            $table->string('reason', 255)->nullable();
            $table->string('status', 20)->default('pending');
            $table->foreignId('deducted_on_slip_id')->nullable()->constrained('salary_slips')->nullOnDelete();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('company_id');
            $table->index('driver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('salary_advances');
    }
};
