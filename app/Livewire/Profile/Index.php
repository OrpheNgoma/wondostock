<?php

namespace App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithFileUploads;

#[Layout('components.layouts.app')]
#[Title('Mon Profil - WondoStock')]
class Index extends Component
{
    use WithFileUploads;

    // --- Profile Information Properties ---
    public string $name = '';
    public string $email = '';
    public string $phone = '';
    public string $position = '';
    public $avatar;
    public string $current_avatar = '';

    // --- Password Properties ---
    public string $current_password = '';
    public string $password = '';
    public string $password_confirmation = '';

    // --- UI State ---
    public string $activeTab = 'profile';
    public bool $showPasswordFields = false;
    public bool $isUpdatingProfile = false;
    public bool $isUpdatingPassword = false;

    // --- Security Info ---
    public array $loginSessions = [];
    public bool $twoFactorEnabled = false;

    public function mount()
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone ?? '';
        $this->position = $user->position ?? '';
        $this->current_avatar = $user->avatar ?? '';
        
        $this->loadSecurityInfo();
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function togglePasswordFields()
    {
        $this->showPasswordFields = !$this->showPasswordFields;
        if (!$this->showPasswordFields) {
            $this->reset(['current_password', 'password', 'password_confirmation']);
        }
    }

    /**
     * Met à jour les informations du profil de l'utilisateur.
     */
    public function updateProfile()
    {
        $this->isUpdatingProfile = true;
        
        try {
            $user = Auth::user();

            $validated = $this->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|max:255|unique:users,email,'.$user->id,
                'phone' => 'nullable|string|max:20',
                'position' => 'nullable|string|max:100',
                'avatar' => 'nullable|image|max:2048', // 2MB max
            ]);

            // Handle avatar upload
            if ($this->avatar) {
                // Delete old avatar if exists
                if ($user->avatar) {
                    Storage::disk('public')->delete($user->avatar);
                }
                
                $avatarPath = $this->avatar->store('avatars', 'public');
                $validated['avatar'] = $avatarPath;
                $this->current_avatar = $avatarPath;
            }

            $user->update($validated);

            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('app.messages.success.updated')
            ]);
            
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error', 
                'message' => __('app.messages.error.general')
            ]);
        } finally {
            $this->isUpdatingProfile = false;
            $this->avatar = null;
        }
    }

    /**
     * Met à jour le mot de passe de l'utilisateur.
     */
    public function updatePassword()
    {
        $this->isUpdatingPassword = true;
        
        try {
            $user = Auth::user();

            $validated = $this->validate([
                'current_password' => 'required|string',
                'password' => ['required', 'string', 'min:8', 'confirmed', Password::defaults()],
            ]);

            // Vérifier si le mot de passe actuel est correct
            if (! Hash::check($validated['current_password'], $user->password)) {
                throw ValidationException::withMessages([
                    'current_password' => __('Le mot de passe actuel est incorrect.'),
                ]);
            }

            $user->update([
                'password' => Hash::make($validated['password']),
            ]);

            $this->reset(['current_password', 'password', 'password_confirmation']);
            $this->showPasswordFields = false;
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => __('Mot de passe mis à jour avec succès.')
            ]);
            
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'type' => 'error',
                'message' => __('app.messages.error.general')
            ]);
        } finally {
            $this->isUpdatingPassword = false;
        }
    }

    private function loadSecurityInfo()
    {
        // Simuler des sessions de connexion pour l'exemple
        $this->loginSessions = [
            [
                'device' => 'Chrome sur Windows',
                'location' => 'Paris, France',
                'last_active' => 'Il y a 2 minutes',
                'current' => true,
                'ip' => '192.168.1.1'
            ],
            [
                'device' => 'Safari sur iPhone',
                'location' => 'Lyon, France', 
                'last_active' => 'Il y a 2 heures',
                'current' => false,
                'ip' => '192.168.1.25'
            ]
        ];
    }

    public function terminateSession($index)
    {
        if (isset($this->loginSessions[$index]) && !$this->loginSessions[$index]['current']) {
            unset($this->loginSessions[$index]);
            $this->loginSessions = array_values($this->loginSessions);
            
            $this->dispatch('notify', [
                'type' => 'success',
                'message' => 'Session terminée avec succès'
            ]);
        }
    }

    public function render()
    {
        return view('livewire.profile.index');
    }
}
