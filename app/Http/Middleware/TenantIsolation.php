<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TenantIsolation
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifier si l'utilisateur est connecté
        if (! Auth::check()) {
            return $next($request);
        }

        $user = Auth::user();

        // Routes Filament /admin/* réservées aux admins globaux.
        // hasRole() n'est acceptable ici que parce que ces routes sont peu fréquentes
        // et que les admins globaux ont leurs rôles sans contexte team (team_id = null).
        if ($request->is('admin/*') || $request->is('admin')) {
            if ($user->is_global_admin || $user->hasRole('Global-Admin')) {
                return $next($request);
            }

            abort(403, 'Accès réservé aux administrateurs globaux.');
        }

        // Pour toutes les autres routes : on utilise UNIQUEMENT la colonne is_global_admin.
        // Ne jamais appeler hasRole() ici — cela chargerait et mettrait en cache la relation
        // roles avec team = null (InitializePermissionsTeam n'a pas encore tourné), ce qui
        // ferait échouer tous les can() suivants pour les utilisateurs tenant normaux.
        if ($user->is_global_admin) {
            return $next($request);
        }

        // Vérifier si l'utilisateur a une company_id
        if (! $user->company_id) {
            abort(403, 'Aucune entreprise associée à votre compte.');
        }

        // Vérifier que la company existe et est active
        $company = Company::find($user->company_id);
        if (! $company) {
            Auth::logout();
            abort(403, 'Entreprise non trouvée.');
        }

        if (! $company->is_active) {
            Auth::logout();
            abort(403, 'Votre abonnement a expiré. Contactez l\'administrateur.');
        }

        // Ajouter la company_id au contexte de la requête
        $request->attributes->set('company_id', $user->company_id);
        $request->attributes->set('company', $company);

        return $next($request);
    }
}
