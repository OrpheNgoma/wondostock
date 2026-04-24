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
        $isGlobalAdmin = $user->is_global_admin || $user->hasRole('Global-Admin');

        // Les routes Filament /admin/* sont réservées aux admins globaux uniquement
        if ($request->is('admin/*') || $request->is('admin')) {
            if ($isGlobalAdmin) {
                return $next($request);
            }

            abort(403, 'Accès réservé aux administrateurs globaux.');
        }

        // Les admins globaux n'ont pas de company_id : on les laisse passer librement.
        // La redirection vers /admin est gérée à la connexion (Login.php).
        // Ne jamais rediriger ici : les requêtes AJAX Livewire (/livewire/update)
        // passeraient aussi par ce middleware et seraient cassées par un redirect.
        if ($isGlobalAdmin) {
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
