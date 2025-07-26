<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class SubscriptionEdit extends Component
{
    public Subscription $subscription;
    
    public $company_id = '';
    public $plan_id = '';
    public $starts_at = '';
    public $ends_at = '';
    public $status = '';

    public $companies = [];
    public $plans = [];

    protected $rules = [
        'company_id' => 'required|exists:companies,id',
        'plan_id' => 'required|exists:plans,id',
        'starts_at' => 'required|date',
        'ends_at' => 'required|date|after:starts_at',
        'status' => 'required|in:active,inactive,cancelled',
    ];

    protected $messages = [
        'company_id.required' => 'Veuillez sélectionner une entreprise.',
        'company_id.exists' => 'L\'entreprise sélectionnée n\'existe pas.',
        'plan_id.required' => 'Veuillez sélectionner un plan.',
        'plan_id.exists' => 'Le plan sélectionné n\'existe pas.',
        'starts_at.required' => 'La date de début est requise.',
        'starts_at.date' => 'La date de début doit être une date valide.',
        'ends_at.required' => 'La date de fin est requise.',
        'ends_at.date' => 'La date de fin doit être une date valide.',
        'ends_at.after' => 'La date de fin doit être postérieure à la date de début.',
        'status.required' => 'Le statut est requis.',
        'status.in' => 'Le statut sélectionné n\'est pas valide.',
    ];

    public function mount(Subscription $subscription)
    {
        $this->subscription = $subscription->load(['company', 'plan']);
        
        $this->companies = Company::orderBy('name')->get();
        $this->plans = Plan::orderBy('name')->get();
        
        // Charger les valeurs actuelles
        $this->company_id = $subscription->company_id;
        $this->plan_id = $subscription->plan_id;
        $this->starts_at = $subscription->starts_at->format('Y-m-d');
        $this->ends_at = $subscription->ends_at->format('Y-m-d');
        $this->status = $subscription->status;
    }

    public function save()
    {
        $this->validate();

        try {
            $this->subscription->update([
                'company_id' => $this->company_id,
                'plan_id' => $this->plan_id,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'status' => $this->status,
            ]);

            session()->flash('success', 'Abonnement modifié avec succès !');
            return $this->redirect(route('admin.subscriptions.index'));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification de l\'abonnement : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.subscription-edit');
    }
}