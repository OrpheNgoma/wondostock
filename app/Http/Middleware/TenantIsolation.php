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

        // Exclure complètement les routes d'administration globale
        if ($request->is('admin/*')) {
            return $next($request);
        }

        // Pour les admins globaux qui accèdent aux routes normales (sauf dashboard qui a son propre middleware)
        if ($user->is_global_admin && !$request->is('admin/*') && !$request->is('dashboard')) {
            return redirect()->route('admin.dashboard');
        }

        // Vérifier si l'utilisateur a une company_id
        if (! $user->company_id) {
            // Si pas de company_id, rediriger vers une page d'erreur ou setup
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

        // Définir la company dans le contexte global pour Spatie Permission
        setPermissionsTeamId($user->company_id);

        // Ajouter la company_id au contexte de la requête
        $request->attributes->set('company_id', $user->company_id);
        $request->attributes->set('company', $company);

        return $next($request);
    }
}
