<?php

namespace App\Models;

use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zone extends Model
{
    /** @use HasFactory<\Database\Factories\ZoneFactory> */
    use BelongsToCompany, HasFactory;

    protected $fillable = [
        'company_id',
        'name',
        'city',
        'mission_allowance',
    ];

    protected function casts(): array
    {
        return [
            'mission_allowance' => 'integer',
        ];
    }

    public function deliveryTrips(): HasMany
    {
        return $this->hasMany(DeliveryTrip::class);
    }
}
