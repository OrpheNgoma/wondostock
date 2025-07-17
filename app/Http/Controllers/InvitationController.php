<?php

namespace App\Http\Controllers;

use App\Models\Invitation;
use App\Services\InvitationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class InvitationController extends Controller
{
    public function __construct(
        private InvitationService $invitationService
    ) {}

    /**
     * Affiche le formulaire d'acceptation d'invitation
     */
    public function show(string $token)
    {
        $invitation = Invitation::where('token', $token)->first();

        if (!$invitation) {
            abort(404, 'Invitation introuvable.');
        }

        if (!$invitation->isValid()) {
            return view('invitations.expired', compact('invitation'));
        }

        return view('invitations.accept', compact('invitation'));
    }

    /**
     * Traite l'acceptation d'une invitation
     */
    public function accept(Request $request, string $token)
    {
        try {
            $userData = $this->invitationService->validateAcceptanceData($request->all());
            
            $user = $this->invitationService->acceptInvitation($token, $userData);

            // Connecter automatiquement l'utilisateur
            auth()->login($user);

            return redirect()->route('dashboard')->with('success', 'Bienvenue ! Votre compte a été créé avec succès.');

        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            return back()->with('error', 'Une erreur est survenue lors de l\'acceptation de l\'invitation.');
        }
    }
}