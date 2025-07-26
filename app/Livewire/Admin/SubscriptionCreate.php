<?php

namespace App\Livewire\Admin;

use App\Models\Company;
use App\Models\Plan;
use App\Models\Subscription;
use Carbon\Carbon;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class SubscriptionCreate extends Component
{
    public $company_id = '';
    public $plan_id = '';
    public $starts_at = '';
    public $ends_at = '';
    public $status = 'active';

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

    public function mount()
    {
        $this->companies = Company::orderBy('name')->get();
        $this->plans = Plan::orderBy('name')->get();
        
        // Valeurs par défaut
        $this->starts_at = Carbon::now()->format('Y-m-d');
        $this->ends_at = Carbon::now()->addMonth()->format('Y-m-d');
    }

    public function updatedPlanId()
    {
        if ($this->plan_id && $this->starts_at) {
            // Auto-calculer la date de fin basée sur un mois d'abonnement
            $this->ends_at = Carbon::parse($this->starts_at)->addMonth()->format('Y-m-d');
        }
    }

    public function updatedStartsAt()
    {
        if ($this->starts_at && $this->plan_id) {
            // Recalculer la date de fin
            $this->ends_at = Carbon::parse($this->starts_at)->addMonth()->format('Y-m-d');
        }
    }

    public function save()
    {
        $this->validate();

        try {
            // Vérifier s'il y a déjà un abonnement actif pour cette entreprise
            $existingSubscription = Subscription::where('company_id', $this->company_id)
                ->where('status', 'active')
                ->first();

            if ($existingSubscription) {
                // Demander confirmation ou annuler l'ancien abonnement
                $existingSubscription->update(['status' => 'cancelled']);
            }

            Subscription::create([
                'company_id' => $this->company_id,
                'plan_id' => $this->plan_id,
                'starts_at' => $this->starts_at,
                'ends_at' => $this->ends_at,
                'status' => $this->status,
            ]);

            session()->flash('success', 'Abonnement créé avec succès !');
            return $this->redirect(route('admin.subscriptions.index'));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création de l\'abonnement : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.subscription-create');
    }
}