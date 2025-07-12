<?php

namespace App\Livewire\Settings;

use App\Models\Setting;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;

#[Layout('components.layouts.app')]
#[Title('Numérotation - WondoStock')]
class Numbering extends Component
{
    public array $prefixes = [];

    public function mount()
    {
        $companyId = Auth::user()->company_id;
        $documentTypes = ['invoice', 'quote', 'credit_note', 'purchase_order'];
        
        foreach ($documentTypes as $type) {
            $this->prefixes[$type] = Setting::where('company_id', $companyId)
                ->where('key', "{$type}_prefix")
                ->value('value') ?? strtoupper(substr($type, 0, 4)) . '-';
        }
    }
    
    public function save()
    {
        $companyId = Auth::user()->company_id;
        foreach ($this->prefixes as $type => $prefix) {
            Setting::updateOrCreate(
                ['company_id' => $companyId, 'key' => "{$type}_prefix"],
                ['value' => $prefix]
            );
        }
        $this->dispatch('notify', message: 'Préfixes de numérotation mis à jour.');
    }

    public function render()
    {
        return view('livewire.settings.numbering');
    }
}