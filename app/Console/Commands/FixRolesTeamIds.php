<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;

class FixRolesTeamIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fix:roles-team-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix team_id values in model_has_roles table for proper multi-tenant isolation';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing roles team IDs for multi-tenant isolation...');

        // 1. Mettre à jour les rôles sans company_id
        $this->info('Step 1: Updating roles without company_id');
        $rolesWithoutCompany = Role::whereNull('company_id')->get();
        
        foreach ($rolesWithoutCompany as $role) {
            // Trouver le company_id basé sur les utilisateurs qui ont ce rôle
            $user = $role->users()->first();
            if ($user && $user->company_id) {
                $role->update(['company_id' => $user->company_id]);
                $this->info("Updated role '{$role->name}' with company_id: {$user->company_id}");
            }
        }

        // 2. Mettre à jour model_has_roles avec les bons team_id
        $this->info('Step 2: Updating model_has_roles team_id values');
        
        $modelRoles = DB::table('model_has_roles')
            ->where('model_type', 'App\\Models\\User')
            ->whereNull('team_id')
            ->get();

        foreach ($modelRoles as $modelRole) {
            $user = User::find($modelRole->model_id);
            if ($user && $user->company_id) {
                DB::table('model_has_roles')
                    ->where('model_id', $modelRole->model_id)
                    ->where('role_id', $modelRole->role_id)
                    ->where('model_type', 'App\\Models\\User')
                    ->update(['team_id' => $user->company_id]);
                
                $this->info("Updated model_has_roles for user {$user->name} (ID: {$user->id}) with team_id: {$user->company_id}");
            }
        }

        // 3. Vérifier et afficher les résultats
        $this->info('Step 3: Verification');
        
        $rolesCount = Role::whereNotNull('company_id')->count();
        $modelRolesCount = DB::table('model_has_roles')->whereNotNull('team_id')->count();
        
        $this->info("Roles with company_id: {$rolesCount}");
        $this->info("Model roles with team_id: {$modelRolesCount}");
        
        $this->info('✅ Roles team IDs fixed successfully!');
        
        return 0;
    }
}