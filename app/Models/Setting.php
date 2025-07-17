<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pour les paramètres de l'application.
 * Stocke les configurations spécifiques à chaque entreprise cliente.
 */
class Setting extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'key',
        'value',
    ];

    /**
     * Relation : Un paramètre appartient à une entreprise.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }
}
