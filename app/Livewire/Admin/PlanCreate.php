<?php

namespace App\Livewire\Admin;

use App\Models\Plan;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('components.layouts.admin')]
class PlanCreate extends Component
{
    public $name = '';

    public $slug = '';

    public $description = '';

    public $price = '';

    public $user_limit = '';

    public $unlimited_users = false;

    public $features = [];

    public $newFeature = '';

    protected $rules = [
        'name' => 'required|string|max:255|unique:plans,name',
        'slug' => 'required|string|max:255|unique:plans,slug',
        'description' => 'nullable|string',
        'price' => 'required|numeric|min:0',
        'user_limit' => 'nullable|integer|min:1',
        'unlimited_users' => 'boolean',
        'features' => 'array',
    ];

    protected $messages = [
        'name.required' => 'Le nom du plan est requis.',
        'name.unique' => 'Ce nom de plan existe déjà.',
        'slug.required' => 'Le slug est requis.',
        'slug.unique' => 'Ce slug existe déjà.',
        'price.required' => 'Le prix est requis.',
        'price.numeric' => 'Le prix doit être un nombre.',
        'price.min' => 'Le prix ne peut pas être négatif.',
        'user_limit.integer' => 'La limite d\'utilisateurs doit être un nombre entier.',
        'user_limit.min' => 'La limite d\'utilisateurs doit être au moins 1.',
    ];

    public function updatedName()
    {
        $this->slug = Str::slug($this->name);
    }

    public function updatedUnlimitedUsers()
    {
        if ($this->unlimited_users) {
            $this->user_limit = '';
        }
    }

    public function addFeature()
    {
        if (! empty(trim($this->newFeature))) {
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
        if (! $this->unlimited_users) {
            $rules['user_limit'] = 'required|integer|min:1';
        }

        $this->validate($rules);

        try {
            Plan::create([
                'name' => $this->name,
                'slug' => $this->slug,
                'description' => $this->description,
                'price' => $this->price,
                'user_limit' => $this->unlimited_users ? null : $this->user_limit,
                'unlimited_users' => $this->unlimited_users,
                'features' => $this->features,
            ]);

            session()->flash('success', 'Plan créé avec succès !');

            return $this->redirect(route('admin.plans.index'));

        } catch (\Exception $e) {
            session()->flash('error', 'Erreur lors de la création du plan : '.$e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.admin.plan-create');
    }
}
