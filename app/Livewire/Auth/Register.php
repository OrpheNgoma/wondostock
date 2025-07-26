<?php

namespace App\Livewire\Auth;

use App\Models\Company;
use App\Models\Plan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

#[Layout('livewire.layouts.auth')] // Utilise un layout simple pour les pages d'authentification
#[Title('Créer un compte - WondoStock')]
class Register extends Component
{
    public string $companyName = '';

    public string $userName = '';

    public string $email = '';

    public string $password = '';

    public string $password_confirmation = '';

    public ?int $selectedPlanId = null;

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
            'selectedPlanId' => 'required|exists:plans,id',
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
        'selectedPlanId.required' => 'Veuillez sélectionner un plan d\'abonnement.',
        'selectedPlanId.exists' => 'Le plan sélectionné n\'existe pas.',
    ];

    /**
     * Initialise le composant avec le plan ESSENTIEL sélectionné par défaut.
     */
    public function mount()
    {
        // Sélectionner le plan ESSENTIEL par défaut
        $defaultPlan = Plan::where('slug', 'essentiel')->first();
        if ($defaultPlan) {
            $this->selectedPlanId = $defaultPlan->id;
        }
    }

    /**
     * Gère la soumission du formulaire d'inscription.
     */
    public function submit()
    {
        try {
            $this->validate();

            // On utilise une transaction pour s'assurer que tout est créé correctement.
            // Si une étape échoue, tout est annulé.
            DB::transaction(function () {
                // 1. On récupère le plan sélectionné par l'utilisateur
                $plan = Plan::findOrFail($this->selectedPlanId);

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

                // 4. Créer et assigner le rôle de "Propriétaire" pour cette entreprise
                setPermissionsTeamId($company->id);
                
                // Utiliser le nom de l'entreprise pour un affichage professionnel
                $sanitizedCompanyName = preg_replace('/[^a-zA-Z0-9\s]/', '', $company->name);
                $sanitizedCompanyName = preg_replace('/\s+/', '-', trim($sanitizedCompanyName));
                $roleName = 'Propriétaire-' . $sanitizedCompanyName;
                
                // Vérifier si le rôle existe déjà
                $ownerRole = \Spatie\Permission\Models\Role::where('name', $roleName)->first();
                
                if (!$ownerRole) {
                    $ownerRole = \Spatie\Permission\Models\Role::create([
                        'name' => $roleName,
                        'guard_name' => 'web',
                        'company_id' => $company->id,
                    ]);
                    
                    // Assigner toutes les permissions au rôle Propriétaire
                    $allPermissions = \Spatie\Permission\Models\Permission::all();
                    $ownerRole->syncPermissions($allPermissions);
                }
                
                // Assigner le rôle à l'utilisateur
                $user->assignRole($ownerRole);

                // 6. Création de l'abonnement
                $company->subscription()->create([
                    'plan_id' => $plan->id,
                    'starts_at' => now(),
                    'ends_at' => now()->addYear(), // Abonnement d'un an par défaut
                    'status' => 'active',
                ]);

                // 7. Connexion de l'utilisateur
                Auth::login($user);
            });

            // 8. Message de bienvenue et redirection
            session()->flash('notify', [
                'message' => "🎉 Bienvenue dans WondoStock ! Votre entreprise {$this->companyName} a été créée avec succès.",
                'type' => 'success',
            ]);

            return $this->redirect('/dashboard', navigate: true);
        } catch (\Exception $e) {
            // Log l'erreur pour le débogage
            \Log::error('Erreur lors de l\'inscription', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'user_data' => [
                    'companyName' => $this->companyName,
                    'email' => $this->email,
                    'userName' => $this->userName,
                ]
            ]);
            
            $this->dispatch('notify', [
                'message' => 'Une erreur est survenue lors de la création de votre compte: ' . $e->getMessage(),
                'type' => 'error',
            ]);
        }
    }

    public function render()
    {
        $plans = Plan::orderBy('price')->get();
        
        return view('livewire.auth.register', [
            'plans' => $plans,
        ]);
    }
}
