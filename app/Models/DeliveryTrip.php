<?php

namespace App\Models;

use App\Enums\DeliveryTripStatus;
use App\Traits\Auditable;
use App\Traits\BelongsToCompany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;

class DeliveryTrip extends Model
{
    /** @use HasFactory<\Database\Factories\DeliveryTripFactory> */
    use Auditable, BelongsToCompany, HasFactory;

    const COMMISSION_RATE = 0.0015;

    protected $fillable = [
        'company_id',
        'driver_id',
        'vehicle_id',
        'zone_id',
        'closed_by',
        'status',
        'trip_date',
        'loaded_at',
        'departed_at',
        'returned_at',
        'closed_at',
        'loaded_crates',
        'returned_crates',
        'total_revenue',
        'total_margin',
        'total_expenses',
        'bank_percentage',
        'bank_amount',
        'cash_amount',
        'funds_amount',
        'mission_allowance_amount',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'status' => DeliveryTripStatus::class,
            'trip_date' => 'date',
            'loaded_at' => 'datetime',
            'departed_at' => 'datetime',
            'returned_at' => 'datetime',
            'closed_at' => 'datetime',
            'loaded_crates' => 'integer',
            'returned_crates' => 'integer',
            'total_revenue' => 'integer',
            'total_margin' => 'integer',
            'total_expenses' => 'integer',
            'bank_percentage' => 'integer',
            'bank_amount' => 'integer',
            'cash_amount' => 'integer',
            'funds_amount' => 'integer',
            'mission_allowance_amount' => 'integer',
        ];
    }

    public function driver(): BelongsTo
    {
        return $this->belongsTo(Driver::class);
    }

    public function vehicle(): BelongsTo
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function zone(): BelongsTo
    {
        return $this->belongsTo(Zone::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DeliveryItem::class, 'trip_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(DeliveryExpense::class, 'trip_id');
    }

    public function getSoldCratesAttribute(): ?int
    {
        if ($this->loaded_crates === null || $this->returned_crates === null) {
            return null;
        }

        return $this->loaded_crates - $this->returned_crates;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('trip_date', Carbon::today());
    }

    public function scopeForDate($query, Carbon|string $date)
    {
        return $query->whereDate('trip_date', $date);
    }

    public function scopeByStatus($query, DeliveryTripStatus $status)
    {
        return $query->where('status', $status);
    }

    public function scopeOpen($query)
    {
        return $query->where('status', '!=', DeliveryTripStatus::Closed);
    }

    public function canLoad(): bool
    {
        return $this->status === DeliveryTripStatus::Draft;
    }

    public function canReturn(): bool
    {
        return $this->status === DeliveryTripStatus::InProgress;
    }

    public function canClose(): bool
    {
        return $this->status === DeliveryTripStatus::Completed;
    }
}
