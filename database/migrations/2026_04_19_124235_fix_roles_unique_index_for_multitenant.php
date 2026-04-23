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
        $indexes = collect(Schema::getIndexes('roles'))->pluck('name');

        Schema::table('roles', function (Blueprint $table) use ($indexes): void {
            if ($indexes->contains('roles_name_guard_name_unique')) {
                $table->dropUnique('roles_name_guard_name_unique');
            }
            if (!$indexes->contains('roles_name_guard_name_company_unique')) {
                $table->unique(['name', 'guard_name', 'company_id'], 'roles_name_guard_name_company_unique');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('roles', function (Blueprint $table): void {
            $table->dropUnique('roles_name_guard_name_company_unique');
            $table->unique(['name', 'guard_name'], 'roles_name_guard_name_unique');
        });
    }
};
