<?php

namespace App\Models;

use App\Models\Subscription;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;

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
    ];

    
    // La colonne 'features' est stockée en JSON dans la BDD,
    // mais Eloquent la traitera comme un tableau PHP.
    protected $casts = [
        'features' => 'array',
    ];

    /**
     * Relation : Un plan peut avoir plusieurs abonnements.
     */
    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }
}
