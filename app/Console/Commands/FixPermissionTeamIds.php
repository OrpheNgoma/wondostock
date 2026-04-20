<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\PermissionRegistrar;

class FixPermissionTeamIds extends Command
{
    protected $signature = 'permissions:fix-team-ids
                            {--dry-run : Affiche ce qui serait corrigé sans modifier la base}';

    protected $description = 'Corrige les entrées model_has_permissions et model_has_roles dont le company_id est NULL.';

    public function handle(): int
    {
        $dryRun = $this->option('dry-run');

        $this->info($dryRun ? '[Dry-run] Analyse en cours...' : 'Correction des company_id NULL...');

        $totalPerms = 0;
        $totalRoles = 0;

        User::whereNotNull('company_id')->each(function (User $user) use ($dryRun, &$totalPerms, &$totalRoles) {
            $permsToFix = DB::table('model_has_permissions')
                ->where('model_id', $user->id)
                ->where('model_type', User::class)
                ->whereNull('company_id')
                ->count();

            $rolesToFix = DB::table('model_has_roles')
                ->where('model_id', $user->id)
                ->where('model_type', User::class)
                ->whereNull('company_id')
                ->count();

            if ($permsToFix === 0 && $rolesToFix === 0) {
                return;
            }

            $this->line(sprintf(
                '  %s (company %d) → %d perm(s), %d rôle(s) à corriger',
                $user->email,
                $user->company_id,
                $permsToFix,
                $rolesToFix,
            ));

            if (! $dryRun) {
                if ($permsToFix > 0) {
                    DB::table('model_has_permissions')
                        ->where('model_id', $user->id)
                        ->where('model_type', User::class)
                        ->whereNull('company_id')
                        ->update(['company_id' => $user->company_id]);
                }

                if ($rolesToFix > 0) {
                    DB::table('model_has_roles')
                        ->where('model_id', $user->id)
                        ->where('model_type', User::class)
                        ->whereNull('company_id')
                        ->update(['company_id' => $user->company_id]);
                }
            }

            $totalPerms += $permsToFix;
            $totalRoles += $rolesToFix;
        });

        app()[PermissionRegistrar::class]->forgetCachedPermissions();

        $this->newLine();

        if ($totalPerms === 0 && $totalRoles === 0) {
            $this->info('Aucune entrée à corriger. Tout est propre.');

            return self::SUCCESS;
        }

        if ($dryRun) {
            $this->warn(sprintf('[Dry-run] %d permission(s) et %d rôle(s) seraient corrigés.', $totalPerms, $totalRoles));
        } else {
            $this->info(sprintf('%d permission(s) et %d rôle(s) corrigés. Cache vidé.', $totalPerms, $totalRoles));
        }

        return self::SUCCESS;
    }
}
