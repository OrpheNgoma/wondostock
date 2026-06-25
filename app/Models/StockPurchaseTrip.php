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

class StockPurchaseTrip extends Model
{
    /** @use HasFactory<\Database\Factories\StockPurchaseTripFactory> */
    use Auditable, BelongsToCompany, HasFactory;

    /** Prime de déplacement fixe par défaut (FCFA), figée sur chaque voyage à la création. */
    const DEFAULT_MISSION_ALLOWANCE = 10000;

    protected $fillable = [
        'company_id',
        'driver_id',
        'vehicle_id',
        'store_id',
        'supplier_id',
        'closed_by',
        'status',
        'trip_date',
        'loaded_at',
        'departed_at',
        'returned_at',
        'closed_at',
        'stock_applied_at',
        'empty_crates_out',
        'full_crates_in',
        'total_purchase_cost',
        'total_expenses',
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
            'stock_applied_at' => 'datetime',
            'empty_crates_out' => 'integer',
            'full_crates_in' => 'integer',
            'total_purchase_cost' => 'integer',
            'total_expenses' => 'integer',
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

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function closedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'closed_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(StockPurchaseItem::class, 'trip_id');
    }

    public function expenses(): HasMany
    {
        return $this->hasMany(StockPurchaseExpense::class, 'trip_id');
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
