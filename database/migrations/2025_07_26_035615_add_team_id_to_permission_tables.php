<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Ajouter team_id à model_has_roles si elle n'existe pas
        if (Schema::hasTable('model_has_roles') && ! Schema::hasColumn('model_has_roles', 'team_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->nullable()->after('model_id');
                $table->index(['team_id']);
            });

            // Copier les valeurs de company_id vers team_id
            if (Schema::hasColumn('model_has_roles', 'company_id')) {
                DB::statement('UPDATE model_has_roles SET team_id = company_id WHERE company_id IS NOT NULL');
            }
        }

        // Ajouter team_id à model_has_permissions si elle n'existe pas
        if (Schema::hasTable('model_has_permissions') && ! Schema::hasColumn('model_has_permissions', 'team_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->unsignedBigInteger('team_id')->nullable()->after('model_id');
                $table->index(['team_id']);
            });

            // Copier les valeurs de company_id vers team_id
            if (Schema::hasColumn('model_has_permissions', 'company_id')) {
                DB::statement('UPDATE model_has_permissions SET team_id = company_id WHERE company_id IS NOT NULL');
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('model_has_roles') && Schema::hasColumn('model_has_roles', 'team_id')) {
            Schema::table('model_has_roles', function (Blueprint $table) {
                $table->dropIndex(['team_id']);
                $table->dropColumn('team_id');
            });
        }

        if (Schema::hasTable('model_has_permissions') && Schema::hasColumn('model_has_permissions', 'team_id')) {
            Schema::table('model_has_permissions', function (Blueprint $table) {
                $table->dropIndex(['team_id']);
                $table->dropColumn('team_id');
            });
        }
    }
};