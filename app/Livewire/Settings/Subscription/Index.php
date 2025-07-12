<?php

namespace App\Livewire\Settings\Subscription;

use App\Models\Plan;
use Livewire\Component;
use Livewire\Attributes\Title;
use Livewire\Attributes\Layout;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

#[Layout('components.layouts.app')]
#[Title('Mon Abonnement - WondoStock')]
class Index extends Component
{
    public $currentPlan;
    public $plans;
    public $subscription;
    public $company;

    public function mount()
    {
        try {
            $this->company = Auth::user()->company;
            $this->subscription = $this->company->subscription;
            $this->currentPlan = $this->subscription->plan;
            $this->plans = Plan::orderBy('id')->get();
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur lors du chargement des informations d\'abonnement.', type: 'error');
            \Log::error('Erreur subscription mount: ' . $e->getMessage());
        }
    }

    public function requestPlanChange(int $planId)
    {
        try {
            $requestedPlan = Plan::findOrFail($planId);
            
            // Vérifier que ce n'est pas le plan actuel
            if ($this->currentPlan->id === $planId) {
                $this->dispatch('notify', message: 'Vous êtes déjà sur ce plan.', type: 'warning');
                return;
            }

            // Dans une vraie application, cela créerait une demande de changement de plan
            // Pour l'instant, on simule avec une notification
            $planName = $requestedPlan->name;
            $this->dispatch('notify', message: "Votre demande pour le plan '{$planName}' a été prise en compte. Nous vous contacterons sous 24h.", type: 'success');
            
            \Log::info("Demande de changement de plan pour la company {$this->company->id}: {$this->currentPlan->name} -> {$planName}");
            
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur lors de la demande de changement de plan.', type: 'error');
            \Log::error('Erreur requestPlanChange: ' . $e->getMessage());
        }
    }

    public function requestOnboardingQuote()
    {
        try {
            // Dans une vraie application, cela créerait une demande de devis d'onboarding
            $this->dispatch('notify', message: 'Votre demande de devis pour l\'onboarding a été envoyée. Notre équipe vous contactera dans les prochaines heures pour organiser votre formation sur site à Libreville.', type: 'success');
            
            \Log::info("Demande de devis onboarding pour la company {$this->company->id} - Plan: {$this->currentPlan->name}");
            
        } catch (\Exception $e) {
            $this->dispatch('notify', message: 'Erreur lors de la demande de devis d\'onboarding.', type: 'error');
            \Log::error('Erreur requestOnboardingQuote: ' . $e->getMessage());
        }
    }

    public function getSubscriptionStatusProperty()
    {
        if (!$this->subscription) return 'Aucun abonnement';
        
        $startDate = $this->subscription->starts_at;
        $endDate = $this->subscription->ends_at;
        
        if ($endDate && $endDate->isPast()) {
            return 'Expiré';
        }
        
        if ($endDate && $endDate->diffInDays(now()) <= 7) {
            return 'Expire bientôt';
        }
        
        return 'Actif';
    }

    public function getPlanFeaturesProperty()
    {
        $featuresLabels = [
            'base_stock' => 'Gestion de stock de base',
            'invoicing' => 'Facturation et devis',
            'reporting_essentiel' => 'Suivi des paiements',
            'multi_store' => 'Gestion multi-magasins (jusqu\'à 3)',
            'roles_permissions' => 'Rôles et permissions avancés',
            'advanced_reporting' => 'Statistiques avancées',
            'stock_transfers' => 'Transferts de stock entre magasins',
            'api_access' => 'API d\'intégration complète',
            'priority_support' => 'Support prioritaire',
            'custom_onboarding' => 'Onboarding personnalisé'
        ];
        
        return $featuresLabels;
    }

    public function getPlanDetailsProperty()
    {
        return [
            'WondoStock ESSENTIEL' => [
                'description' => 'Idéal pour TPE, indépendants, 1 magasin',
                'target' => 'Jusqu\'à 2 utilisateurs • 1 magasin',
                'monthly_price' => 25000,
                'quarterly_price' => 70000,
                'yearly_price' => 250000,
                'yearly_note' => '2 mois offerts',
                'currency' => 'XAF'
            ],
            'WondoStock PRO' => [
                'description' => 'Parfait pour PME en croissance, multi-sites',
                'target' => 'Jusqu\'à 10 utilisateurs • Jusqu\'à 3 magasins',
                'monthly_price' => 55000,
                'quarterly_price' => 155000,
                'yearly_price' => 550000,
                'yearly_note' => '2 mois offerts',
                'currency' => 'XAF'
            ],
            'WondoStock ENTREPRISE' => [
                'description' => 'Solution complète pour grandes PME, besoins spécifiques',
                'target' => 'Utilisateurs illimités • Magasins illimités',
                'monthly_price' => 'Sur Devis',
                'quarterly_price' => null,
                'yearly_price' => null,
                'yearly_note' => null,
                'currency' => 'XAF'
            ]
        ];
    }

    public function getOnboardingInfoProperty()
    {
        return [
            'price' => 50000,
            'currency' => 'XAF',
            'description' => 'Forfait unique optionnel pour une prise en main complète',
            'includes' => [
                'Paramétrage initial de votre système',
                'Importation de votre catalogue produit existant',
                'Formation de 2h sur site à Libreville'
            ]
        ];
    }

    public function render()
    {
        return view('livewire.settings.subscription.index');
    }
}