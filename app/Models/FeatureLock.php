<?php

namespace App\Models;

use App\Enums\FeatureEnum;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Modèle pour les verrouillages de fonctionnalités par entreprise.
 *
 * Permet à l'admin global de verrouiller/déverrouiller des fonctionnalités
 * spécifiques pour certaines entreprises du SaaS.
 *
 * @property int $id
 * @property int $company_id
 * @property string $feature_key
 * @property bool $is_locked
 * @property string|null $reason
 * @property Carbon|null $locked_at
 * @property Carbon|null $expires_at
 * @property int|null $locked_by
 * @property array|null $metadata
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property-read Company $company
 * @property-read User|null $lockedBy
 */
class FeatureLock extends Model
{
    /** @use HasFactory<\Database\Factories\FeatureLockFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'company_id',
        'feature_key',
        'is_locked',
        'reason',
        'locked_at',
        'expires_at',
        'locked_by',
        'metadata',
    ];

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'is_locked' => 'boolean',
            'locked_at' => 'datetime',
            'expires_at' => 'datetime',
            'metadata' => 'array',
        ];
    }

    /**
     * Relation vers l'entreprise concernée.
     */
    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Relation vers l'utilisateur admin qui a effectué le verrouillage.
     */
    public function lockedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'locked_by');
    }

    /**
     * Scope pour les fonctionnalités verrouillées.
     */
    public function scopeLocked(Builder $query): Builder
    {
        return $query->where('is_locked', true);
    }

    /**
     * Scope pour les fonctionnalités déverrouillées.
     */
    public function scopeUnlocked(Builder $query): Builder
    {
        return $query->where('is_locked', false);
    }

    /**
     * Scope pour les verrouillages actifs (non expirés).
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_locked', true)
            ->where(function ($q) {
                $q->whereNull('expires_at')
                    ->orWhere('expires_at', '>', now());
            });
    }

    /**
     * Scope pour les verrouillages expirés.
     */
    public function scopeExpired(Builder $query): Builder
    {
        return $query->where('is_locked', true)
            ->whereNotNull('expires_at')
            ->where('expires_at', '<=', now());
    }

    /**
     * Scope pour filtrer par entreprise.
     */
    public function scopeForCompany(Builder $query, Company $company): Builder
    {
        return $query->where('company_id', $company->id);
    }

    /**
     * Scope pour filtrer par fonctionnalité.
     */
    public function scopeForFeature(Builder $query, string|FeatureEnum $feature): Builder
    {
        $featureKey = $feature instanceof FeatureEnum ? $feature->value : $feature;

        return $query->where('feature_key', $featureKey);
    }

    /**
     * Vérifie si le verrouillage est actif.
     */
    public function isActive(): bool
    {
        if (! $this->is_locked) {
            return false;
        }

        if ($this->expires_at && $this->expires_at->isPast()) {
            return false;
        }

        return true;
    }

    /**
     * Vérifie si le verrouillage est expiré.
     */
    public function isExpired(): bool
    {
        return $this->is_locked
            && $this->expires_at
            && $this->expires_at->isPast();
    }

    /**
     * Marque la fonctionnalité comme verrouillée.
     */
    public function lock(?string $reason = null, ?User $admin = null, ?Carbon $expiresAt = null): self
    {
        $this->update([
            'is_locked' => true,
            'reason' => $reason,
            'locked_at' => now(),
            'locked_by' => $admin?->id,
            'expires_at' => $expiresAt,
        ]);

        return $this;
    }

    /**
     * Marque la fonctionnalité comme déverrouillée.
     */
    public function unlock(): self
    {
        $this->update([
            'is_locked' => false,
            'reason' => null,
            'locked_at' => null,
            'expires_at' => null,
        ]);

        return $this;
    }

    /**
     * Retourne l'instance de l'énumération pour cette fonctionnalité.
     */
    public function getFeatureEnum(): ?FeatureEnum
    {
        return FeatureEnum::tryFrom($this->feature_key);
    }

    /**
     * Retourne la description de la fonctionnalité.
     */
    public function getFeatureDescription(): string
    {
        $enum = $this->getFeatureEnum();

        return $enum ? $enum->getDescription() : $this->feature_key;
    }

    /**
     * Retourne la catégorie de la fonctionnalité.
     */
    public function getFeatureCategory(): string
    {
        $enum = $this->getFeatureEnum();

        return $enum ? $enum->getCategory() : 'Autre';
    }

    /**
     * Vérifie si la fonctionnalité est critique.
     */
    public function isCriticalFeature(): bool
    {
        $enum = $this->getFeatureEnum();

        return $enum ? $enum->isCritical() : false;
    }

    /**
     * Ajoute des métadonnées.
     */
    public function addMetadata(array $data): self
    {
        $metadata = $this->metadata ?? [];
        $this->update(['metadata' => array_merge($metadata, $data)]);

        return $this;
    }

    /**
     * Retourne une métadonnée spécifique.
     */
    public function getMetadata(string $key, $default = null)
    {
        return data_get($this->metadata, $key, $default);
    }

    /**
     * Bootstrap du modèle.
     */
    protected static function booted(): void
    {
        // Le nettoyage des locks expirés est géré par CleanupExpiredFeatureLocks
        // (scheduled command) pour éviter les UPDATEs en cascade sur chaque lecture.
    }
}
