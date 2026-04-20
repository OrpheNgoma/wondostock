<?php

namespace App\Traits;

use App\Models\Audit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

trait Auditable
{
    /**
     * Buffer interne pour stocker les anciennes valeurs avant mise à jour.
     * Déclaré comme vraie propriété PHP pour éviter qu'Eloquent l'intercepte
     * via __set() et l'ajoute aux attributs SQL.
     *
     * @var array<string, mixed>
     */
    private array $_auditOldValues = [];

    /**
     * Colonnes à exclure de l'audit (techniques, timestamps, etc.).
     *
     * @var array<string>
     */
    protected array $auditExclude = [
        'created_at', 'updated_at', 'deleted_at',
        'remember_token', 'password',
        'two_factor_secret', 'two_factor_recovery_codes',
    ];

    public static function bootAuditable(): void
    {
        static::created(function ($model) {
            $model->recordAudit('created', [], $model->getAuditableAttributes());
        });

        static::updating(function ($model) {
            $model->_auditOldValues = array_intersect_key(
                $model->getOriginal(),
                $model->getDirty()
            );
        });

        static::updated(function ($model) {
            $old = $model->_auditOldValues;
            $new = array_intersect_key($model->getChanges(), $old + $model->getDirty());

            // Retirer les colonnes exclues
            $excluded = array_flip($model->auditExclude);
            $old = array_diff_key($old, $excluded);
            $new = array_diff_key($new, $excluded);

            if (empty($new)) {
                return;
            }

            $model->recordAudit('updated', $old, $new);
        });

        static::deleted(function ($model) {
            $model->recordAudit('deleted', $model->getAuditableAttributes(), []);
        });
    }

    /**
     * Retourne les attributs auditables (fillable sans les exclusions).
     *
     * @return array<string, mixed>
     */
    private function getAuditableAttributes(): array
    {
        $excluded = array_flip($this->auditExclude);
        $attributes = array_intersect_key($this->getAttributes(), array_flip($this->getFillable()));

        return array_diff_key($attributes, $excluded);
    }

    private function recordAudit(string $event, array $oldValues, array $newValues): void
    {
        Audit::create([
            'company_id' => $this->company_id ?? null,
            'user_id' => Auth::id(),
            'auditable_type' => static::class,
            'auditable_id' => $this->getKey(),
            'event' => $event,
            'old_values' => empty($oldValues) ? null : $oldValues,
            'new_values' => empty($newValues) ? null : $newValues,
            'ip_address' => Request::ip(),
            'user_agent' => Request::userAgent(),
            'created_at' => now(),
        ]);
    }

    /**
     * Relation polymorphique vers les audits.
     */
    public function audits(): \Illuminate\Database\Eloquent\Relations\MorphMany
    {
        return $this->morphMany(Audit::class, 'auditable')->latest('created_at');
    }
}
