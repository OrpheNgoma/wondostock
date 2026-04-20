<?php

namespace App\Traits;

use Illuminate\Support\Facades\Auth;

/**
 * Trait à inclure dans les composants Livewire pour vérifier les permissions métier.
 * Les Super-Administrateurs et Global-Admin passent tous les checks via Gate::before.
 */
trait AuthorizesLivewireActions
{
    /**
     * Vérifie une permission et retourne false + notifie si refusé.
     * Utiliser dans les méthodes Livewire avant toute action sensible.
     */
    protected function checkPermission(string $permission, string $message = 'Action non autorisée.'): bool
    {
        if (Auth::user()->can($permission)) {
            return true;
        }

        $this->dispatch('notify', message: $message, type: 'error');

        return false;
    }

    /**
     * Lève une exception 403 si l'utilisateur n'a pas la permission.
     * Utiliser dans mount() pour protéger l'accès à toute la page.
     */
    protected function requirePermission(string $permission): void
    {
        abort_unless(Auth::user()->can($permission), 403);
    }
}
