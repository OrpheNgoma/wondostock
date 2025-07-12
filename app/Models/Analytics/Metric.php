<?php

namespace App\Models\Analytics;

use App\Models\Company;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Metric extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_id', 'type', 'category', 'data', 'value', 'recorded_at'
    ];

    protected $casts = [
        'data' => 'array',
        'value' => 'decimal:2',
        'recorded_at' => 'datetime',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    /**
     * Enregistrer une métrique
     */
    public static function record(string $type, string $category, ?int $companyId = null, array $data = [], ?float $value = null): self
    {
        return self::create([
            'company_id' => $companyId,
            'type' => $type,
            'category' => $category,
            'data' => $data,
            'value' => $value,
            'recorded_at' => now(),
        ]);
    }

    /**
     * Scope pour filtrer par période
     */
    public function scopeInPeriod($query, $start, $end)
    {
        return $query->whereBetween('recorded_at', [$start, $end]);
    }

    /**
     * Scope pour filtrer par catégorie
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Scope pour filtrer par type
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }
}
