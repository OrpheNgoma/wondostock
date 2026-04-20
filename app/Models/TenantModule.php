<?php

namespace App\Models;

use App\Enums\ModuleKey;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TenantModule extends Model
{
    protected $fillable = [
        'company_id',
        'module_key',
        'is_enabled',
        'config',
        'enabled_at',
        'enabled_by',
    ];

    protected function casts(): array
    {
        return [
            'module_key' => ModuleKey::class,
            'is_enabled' => 'boolean',
            'config' => 'array',
            'enabled_at' => 'datetime',
        ];
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function enabledBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'enabled_by');
    }

    public function scopeEnabled($query)
    {
        return $query->where('is_enabled', true);
    }

    public function scopeForModule($query, ModuleKey|string $moduleKey)
    {
        $value = $moduleKey instanceof ModuleKey ? $moduleKey->value : $moduleKey;

        return $query->where('module_key', $value);
    }
}
