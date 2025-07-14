<?php

namespace App\Livewire\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;

#[Layout('livewire.layouts.auth')]
#[Title('Connexion - KaziFlow')]
class Login extends Component
{
    public string $email = '';
    public string $password = '';
    public bool $remember = false;

    protected function rules()
    {
        return [
            'email' => 'required|email',
            'password' => 'required',
        ];
    }

    public function login()
    {
        try {
            $this->validate();

            if (!Auth::attempt(['email' => $this->email, 'password' => $this->password], $this->remember)) {
                $this->dispatch('notify', [
                    'message' => 'Email ou mot de passe incorrect. Veuillez vérifier vos identifiants.',
                    'type' => 'error'
                ]);
                
                throw ValidationException::withMessages([
                    'email' => 'Ces identifiants ne correspondent à aucun compte.',
                ]);
            }
        } catch (ValidationException $e) {
            throw $e;
        } catch (\Exception $e) {
            $this->dispatch('notify', [
                'message' => 'Une erreur est survenue. Veuillez réessayer.',
                'type' => 'error'
            ]);
            return;
        }

        session()->regenerate();
        
        session()->flash('notify', [
            'message' => 'Connexion réussie ! Bienvenue dans WondoStock.',
            'type' => 'success'
        ]);

        return $this->redirect('/dashboard', navigate: true);
    }

    public function render()
    {
        return view('livewire.auth.login');
    }
}
