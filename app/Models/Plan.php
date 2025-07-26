<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Modèle pour les plans d'abonnement (Essentiel, Pro, etc.).
 */
class Plan extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'description',
        'price',
        'features',
        'user_limit',
        'unlimited_users',
    ];

    // La colonne 'features' est stockée en JSON dans la BDD,
    // mais Eloquent la traitera comme un tableau PHP.
    protected $casts = [
        'features' => 'json',
        'price' => 'decimal:2',
        'user_limit' => 'integer',
        'unlimited_users' => 'boolean',
    ];

    /**
     * Relation : Un plan peut avoir plusieurs abonnements.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Vérifie si le plan permet des utilisateurs illimités.
     */
    public function hasUnlimitedUsers(): bool
    {
        return $this->unlimited_users;
    }

    /**
     * Retourne la limite d'utilisateurs pour ce plan.
     */
    public function getUserLimit(): int
    {
        return $this->unlimited_users ? PHP_INT_MAX : $this->user_limit;
    }

    /**
     * Vérifie si un nombre d'utilisateurs est autorisé pour ce plan.
     */
    public function canHaveUsers(int $userCount): bool
    {
        return $this->unlimited_users || $userCount <= $this->user_limit;
    }

    /**
     * Retourne les fonctionnalités sous forme de tableau.
     */
    public function getFeaturesArray(): array
    {
        $features = $this->getRawOriginal('features') ?? '[]';

        if (is_string($features)) {
            $decoded = json_decode($features, true);

            return is_array($decoded) ? $decoded : [];
        }

        if (is_array($features)) {
            return $features;
        }

        return [];
    }

    /**
     * Accessor pour les fonctionnalités - garantit le retour d'un tableau.
     */
    public function getFeaturesAttribute($value)
    {
        // Si la valeur est déjà un tableau, la retourner
        if (is_array($value)) {
            return $value;
        }

        // Si c'est une chaîne JSON, la décoder
        if (is_string($value)) {
            $decoded = json_decode($value, true);

            return is_array($decoded) ? $decoded : [];
        }

        // Pour tous les autres cas (null, false, etc.), retourner un tableau vide
        return [];
    }

    /**
     * Mutator pour les fonctionnalités - encode en JSON.
     */
    public function setFeaturesAttribute($value)
    {
        $this->attributes['features'] = is_array($value) ? json_encode($value) : $value;
    }
}
