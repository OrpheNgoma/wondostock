<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasColumn('salary_slips', 'employee_id')) {
            Schema::table('salary_slips', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->after('driver_id')->constrained('employees')->nullOnDelete();
            });
        }

        if (!Schema::hasColumn('salary_advances', 'employee_id')) {
            Schema::table('salary_advances', function (Blueprint $table) {
                $table->foreignId('employee_id')->nullable()->after('driver_id')->constrained('employees')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        Schema::table('salary_slips', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Employee::class);
            $table->dropColumn('employee_id');
        });

        Schema::table('salary_advances', function (Blueprint $table) {
            $table->dropForeignIdFor(\App\Models\Employee::class);
            $table->dropColumn('employee_id');
        });
    }
};
