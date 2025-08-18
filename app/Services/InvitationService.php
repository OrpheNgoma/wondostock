<?php

namespace App\Services;

use App\Models\Invitation;
use App\Models\User;
use App\Notifications\UserInvitationNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Validation\ValidationException;

class InvitationService
{
    /**
     * Crée et envoie une invitation
     */
    public function createInvitation(array $data): Invitation
    {
        // Vérifier si l'utilisateur existe déjà dans l'entreprise
        $existingUser = User::where('email', $data['email'])
            ->where('company_id', Auth::user()->company_id)
            ->first();

        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => 'Un utilisateur avec cet email existe déjà dans votre entreprise.',
            ]);
        }

        // Vérifier s'il y a déjà une invitation en attente
        $existingInvitation = Invitation::where('email', $data['email'])
            ->where('company_id', Auth::user()->company_id)
            ->where('status', 'pending')
            ->first();

        if ($existingInvitation) {
            if ($existingInvitation->isValid()) {
                throw ValidationException::withMessages([
                    'email' => 'Une invitation est déjà en attente pour cet email.',
                ]);
            } else {
                // Supprimer l'ancienne invitation expirée
                $existingInvitation->delete();
            }
        }

        // Créer l'invitation
        $invitation = Invitation::create([
            'company_id' => Auth::user()->company_id,
            'invited_by' => Auth::id(),
            'email' => $data['email'],
            'role_id' => $data['role_id'] ?? null,
            'store_id' => $data['store_id'] ?? null,
            'message' => $data['message'] ?? null,
        ]);

        // Envoyer l'email d'invitation
        $this->sendInvitationEmail($invitation);

        return $invitation;
    }

    /**
     * Crée plusieurs invitations en lot
     */
    public function createBulkInvitations(array $invitations): array
    {
        $results = [];
        $errors = [];

        foreach ($invitations as $index => $invitationData) {
            try {
                $invitation = $this->createInvitation($invitationData);
                $results[] = $invitation;
            } catch (ValidationException $e) {
                $errors[$index] = $e->errors();
            }
        }

        return [
            'invitations' => $results,
            'errors' => $errors,
        ];
    }

    /**
     * Envoie l'email d'invitation
     */
    public function sendInvitationEmail(Invitation $invitation): void
    {
        $anonymousNotifiable = new AnonymousNotifiable;
        $anonymousNotifiable->route('mail', $invitation->email);

        Notification::send($anonymousNotifiable, new UserInvitationNotification($invitation));
    }

    /**
     * Renvoie une invitation
     */
    public function resendInvitation(Invitation $invitation): void
    {
        if ($invitation->status !== 'pending') {
            throw ValidationException::withMessages([
                'invitation' => 'Cette invitation ne peut pas être renvoyée.',
            ]);
        }

        // Régénérer le token et prolonger l'expiration
        $invitation->regenerateToken();

        // Renvoyer l'email
        $this->sendInvitationEmail($invitation);
    }

    /**
     * Accepte une invitation et crée l'utilisateur
     */
    public function acceptInvitation(string $token, array $userData): User
    {
        $invitation = Invitation::where('token', $token)->first();

        if (! $invitation) {
            throw ValidationException::withMessages([
                'token' => 'Invitation introuvable.',
            ]);
        }

        if (! $invitation->isValid()) {
            throw ValidationException::withMessages([
                'token' => 'Cette invitation a expiré ou n\'est plus valide.',
            ]);
        }

        // Vérifier si l'utilisateur existe déjà
        $existingUser = User::where('email', $invitation->email)
            ->where('company_id', $invitation->company_id)
            ->first();

        if ($existingUser) {
            throw ValidationException::withMessages([
                'email' => 'Un compte existe déjà avec cet email.',
            ]);
        }

        return DB::transaction(function () use ($invitation, $userData) {
            // Créer l'utilisateur
            $user = User::create([
                'name' => $userData['name'],
                'email' => $invitation->email,
                'password' => Hash::make($userData['password']),
                'company_id' => $invitation->company_id,
                'store_id' => $invitation->store_id,
            ]);

            // Assigner le rôle
            if ($invitation->role) {
                $user->assignRole($invitation->role);
            }

            // Marquer l'invitation comme acceptée
            $invitation->markAsAccepted($user);

            return $user;
        });
    }

    /**
     * Annule une invitation
     */
    public function cancelInvitation(Invitation $invitation): void
    {
        if ($invitation->status !== 'pending') {
            throw ValidationException::withMessages([
                'invitation' => 'Cette invitation ne peut pas être annulée.',
            ]);
        }

        $invitation->markAsCancelled();
    }

    /**
     * Nettoie les invitations expirées
     */
    public function cleanupExpiredInvitations(): int
    {
        return Invitation::expired()->delete();
    }

    /**
     * Récupère les statistiques des invitations pour une entreprise
     */
    public function getInvitationStats(int $companyId): array
    {
        $total = Invitation::where('company_id', $companyId)->count();
        $pending = Invitation::where('company_id', $companyId)->where('status', 'pending')->count();
        $accepted = Invitation::where('company_id', $companyId)->where('status', 'accepted')->count();
        $expired = Invitation::where('company_id', $companyId)->expired()->count();

        return [
            'total' => $total,
            'pending' => $pending,
            'accepted' => $accepted,
            'expired' => $expired,
            'success_rate' => $total > 0 ? round(($accepted / $total) * 100, 1) : 0,
        ];
    }

    /**
     * Valide les données d'invitation
     */
    public function validateInvitationData(array $data): array
    {
        return validator($data, [
            'email' => 'required|email',
            'role_id' => 'nullable|exists:roles,id',
            'store_id' => 'nullable|exists:stores,id',
            'message' => 'nullable|string|max:1000',
        ])->validate();
    }

    /**
     * Valide les données d'acceptation d'invitation
     */
    public function validateAcceptanceData(array $data): array
    {
        return validator($data, [
            'name' => 'required|string|max:255',
            'password' => 'required|string|min:8|confirmed',
        ])->validate();
    }
}
