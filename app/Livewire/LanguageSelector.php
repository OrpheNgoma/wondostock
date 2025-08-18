<?php

namespace App\Livewire;

use Livewire\Component;

class LanguageSelector extends Component
{
    public function render()
    {
        return view('livewire.language-selector', [
            'currentLocale' => app()->getLocale(),
            'availableLocales' => config('app.available_locales'),
        ]);
    }
}
