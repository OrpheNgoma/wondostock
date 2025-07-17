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
        // Add company_id to roles table
        if (Schema::hasTable('roles') && ! Schema::hasColumn('roles', 'company_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index(['company_id']);
            });
        }

        // Add company_id to model_has_roles table
        if (Schema::hasTable('model_has_roles') && ! Schema::hasColumn('model_has_roles', 'company_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index(['company_id']);
            });
        }

        // Add company_id to model_has_permissions table
        if (Schema::hasTable('model_has_permissions') && ! Schema::hasColumn('model_has_permissions', 'company_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable();
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index(['company_id']);
            });
        }

        // Add company_id to permissions table
        if (Schema::hasTable('permissions') && ! Schema::hasColumn('permissions', 'company_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('id');
                $table->foreign('company_id')->references('id')->on('companies')->onDelete('cascade');
                $table->index(['company_id']);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Remove foreign keys and columns in reverse order
        if (Schema::hasTable('permissions') && Schema::hasColumn('permissions', 'company_id')) {
            Schema::table('permissions', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('model_has_permissions') && Schema::hasColumn('model_has_permissions', 'company_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('model_has_roles') && Schema::hasColumn('model_has_roles', 'company_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }

        if (Schema::hasTable('roles') && Schema::hasColumn('roles', 'company_id')) {
            Schema::table('roles', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
