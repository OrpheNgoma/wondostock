<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class PlanEdit extends Component
{
    public Plan $plan;
    public $name = '';
    public $slug = '';
    public $description = '';
    public $price = '';
    public $user_limit = '';
    public $unlimited_users = false;
    public $features = [];
    public $newFeature = '';

    protected $rules = [
        'name' => 'required|string|max:255',
        'slug' => 'required|string|max:255',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'user_limit' => 'nullable|integer|min:1',
        'unlimited_users' => 'boolean',
        'features' => 'array',
    ];

    protected $messages = [
        'name.required' => 'Le nom du plan est requis.',
        'slug.required' => 'Le slug est requis.',
        'price.required' => 'Le prix est requis.',
        'price.numeric' => 'Le prix doit être un nombre.',
        'price.min' => 'Le prix ne peut pas être négatif.',
        'user_limit.integer' => 'La limite d\'utilisateurs doit être un nombre entier.',
        'user_limit.min' => 'La limite d\'utilisateurs doit être au moins 1.',
    ];

    public function mount(Plan $plan)
    {
        $this->plan = $plan;
        $this->name = $plan->name;
        $this->slug = $plan->slug;
        $this->description = $plan->description;
        $this->price = $plan->price;
        $this->user_limit = $plan->user_limit;
        $this->unlimited_users = $plan->unlimited_users;
        $this->features = $plan->getFeaturesArray();
    }

    public function updatedName()
    {
        // Ne regénérer le slug que si c'est différent
        if ($this->slug === Str::slug($this->plan->name)) {
            $this->slug = Str::slug($this->name);
        }
    }

    public function updatedUnlimitedUsers()
    {
        if ($this->unlimited_users) {
            $this->user_limit = '';
        }
    }

    public function addFeature()
    {
        if (!empty(trim($this->newFeature))) {
            $this->features[] = trim($this->newFeature);
            $this->newFeature = '';
        }
    }

    public function removeFeature($index)
    {
        unset($this->features[$index]);
        $this->features = array_values($this->features);
    }

    public function save()
    {
        // Validation conditionnelle pour user_limit
        $rules = $this->rules;
        if (!$this->unlimited_users) {
            $rules['user_limit'] = 'required|integer|min:1';
        }

        // Validation unique pour name et slug (exclure le plan actuel)
        $rules['name'] .= ',name,' . $this->plan->id;
        $rules['slug'] .= ',slug,' . $this->plan->id;

        $this->validate($rules);

        try {
            $this->plan->update([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'price' => $this->price,
                'user_limit' => $this->unlimited_users ? null : $this->user_limit,
                'unlimited_users' => $this->unlimited_users,
                'features' => $this->features,
            ]);

            session()->flash('success', 'Plan modifié avec succès !');
            return $this->redirect(route('admin.plans.index'));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la modification du plan : ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.plan-edit');
    }
}