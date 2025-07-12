<?php

namespace App\Livewire\Auth;

use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('livewire.layouts.auth')] // Utilise un layout simple pour les pages d'authentification
#[Title('Créer un compte - WondoStock')]
class Register extends Component
{
    public string $companyName = '';
    public string $userName = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Règles de validation pour le formulaire.
     */
    protected function rules()
    {
        return [
            'companyName' => 'required|string|min:3|max:255',
            'userName' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ];
    }

    /**
     * Messages de validation en français.
     */
    protected $messages = [
        'companyName.required' => 'Le nom de votre entreprise est requis.',
        'companyName.min' => 'Le nom de l\'entreprise doit faire au moins 3 caractères.',
        'email.required' => 'Votre adresse email est requise.',
        'email.email' => 'Veuillez entrer une adresse email valide.',
        'email.unique' => 'Cette adresse email est déjà utilisée.',
        'password.required' => 'Un mot de passe est requis.',
        'password.min' => 'Le mot de passe doit faire au moins 8 caractères.',
        'password.confirmed' => 'La confirmation du mot de passe ne correspond pas.',
    ];

    /**
     * Gère la soumission du formulaire d'inscription.
     */
    public function submit()
    {
        $this->validate();

        // On utilise une transaction pour s'assurer que tout est créé correctement.
        // Si une étape échoue, tout est annulé.
        DB::transaction(function () {
            // 1. On récupère le plan par défaut ("Essentiel")
            $plan = Plan::where('slug', 'essentiel')->firstOrFail();

            // 1. Création de l'entreprise (Company) SANS l'owner_id pour l'instant.
            $company = Company::create([
                'name' => $this->companyName,
            ]);

            // 2. Création de l'utilisateur en lui passant l'ID de sa compagnie
            $user = User::create([
                'name' => $this->userName,
                'email' => $this->email,
                'password' => Hash::make($this->password),
                'company_id' => $company->id, // On assigne l'ID de l'entreprise ici
            ]);
            
            // 3. On met à jour l'entreprise avec l'ID du propriétaire
            $company->owner_id = $user->id;
            $company->save();
            
            // 4. On assigne le rôle de "Super-Administrateur"
            $user->assignRole('Super-Administrateur');

            // 6. Création de l'abonnement
            $company->subscription()->create([
                'plan_id' => $plan->id,
                'starts_at' => now(),
                'ends_at' => now()->addYear(), // Abonnement d'un an par défaut
                'status' => 'active',
            ]);

            // 7. Connexion de l'utilisateur
            Auth::login($user);

            // 8. Redirection vers le tableau de bord
            return $this->redirect('/dashboard', navigate: true);
        });
    }

    public function render()
    {
        return view('livewire.auth.register');
    }
}
