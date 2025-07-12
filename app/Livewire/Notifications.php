<?php

namespace App\Livewire;

use Livewire\Component;

use Livewire\Attributes\On;

class Notifications extends Component
{
    public $message;
    public $type;
    public $show = false;

    /**
     * Méthode mount pour vérifier la session au chargement.
     */
    public function mount()
    {
        if (session()->has('notify')) {
            $notification = session('notify');
            $this->showNotification($notification['message'], $notification['type']);
        }
    }

    #[On('notify')]
    public function showNotification($message, $type = 'success')
    {
        $this->message = $message;
        $this->type = $type;
        $this->show = true;

        // On utilise un dispatch de browser pour le timer, c'est plus fiable avec AlpineJS
        $this->dispatch('notification-shown');
    }

    public function render()
    {
        return view('livewire.notifications');
    }
}
