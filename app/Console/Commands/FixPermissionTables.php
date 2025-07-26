<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class FixPermissionTables extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:permission-tables';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix permission tables to work with company_id as team_foreign_key';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing permission tables for company_id team isolation...');

        // 1. Fix model_has_roles table - copy team_id to company_id where company_id is null
        $this->info('Step 1: Updating model_has_roles table');
        $beforeCount = DB::table('model_has_roles')->whereNull('company_id')->count();
        DB::statement('UPDATE model_has_roles SET company_id = team_id WHERE company_id IS NULL AND team_id IS NOT NULL');
        $afterCount = DB::table('model_has_roles')->whereNull('company_id')->count();
        $this->info("Updated " . ($beforeCount - $afterCount) . " rows in model_has_roles table");

        // 2. Fix model_has_permissions table if it exists and has similar issues
        if (DB::getSchemaBuilder()->hasTable('model_has_permissions')) {
            $this->info('Step 2: Updating model_has_permissions table');
            $beforePermCount = DB::table('model_has_permissions')->whereNull('company_id')->count();
            DB::statement('UPDATE model_has_permissions SET company_id = team_id WHERE company_id IS NULL AND team_id IS NOT NULL');
            $afterPermCount = DB::table('model_has_permissions')->whereNull('company_id')->count();
            $this->info("Updated " . ($beforePermCount - $afterPermCount) . " rows in model_has_permissions table");
        }

        // 3. Fix roles table - ensure all roles have company_id
        $this->info('Step 3: Fixing roles without company_id');
        $rolesWithoutCompany = Role::whereNull('company_id')->get();
        
        foreach ($rolesWithoutCompany as $role) {
            // Find company_id based on users who have this role
            $user = $role->users()->first();
            if ($user && $user->company_id) {
                $role->update(['company_id' => $user->company_id]);
                $this->info("Updated role '{$role->name}' with company_id: {$user->company_id}");
            } else {
                // If no users have this role, we might need to assign it to a default company
                $this->warn("Role '{$role->name}' has no users assigned. Skipping.");
            }
        }

        // 4. Verification
        $this->info('Step 4: Verification');
        
        $modelRolesWithCompany = DB::table('model_has_roles')->whereNotNull('company_id')->count();
        $rolesWithCompany = Role::whereNotNull('company_id')->count();
        
        $this->info("Model roles with company_id: {$modelRolesWithCompany}");
        $this->info("Roles with company_id: {$rolesWithCompany}");
        
        // 5. Test specific user
        $testUser = User::find(4);
        if ($testUser) {
            $this->info("Testing user: {$testUser->name} (Company: {$testUser->company_id})");
            
            // Clear permission cache and test
            app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();
            
            setPermissionsTeamId($testUser->company_id);
            $roles = $testUser->roles;
            $this->info("User roles found: {$roles->count()}");
            
            foreach ($roles as $role) {
                $this->info("  - {$role->name} (Company: {$role->company_id})");
            }
        }
        
        $this->info('✅ Permission tables fixed successfully!');
        
        return 0;
    }
}