<?php

namespace App\Models;

use App\Enums\DocumentStatus;
use App\Enums\DocumentType;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;

class Document extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable =
        [
            'company_id', 'customer_id', 'store_id', 'user_id', 'source_document_id', 'type',
            'status', 'document_number', 'document_date', 'due_date', 'sub_total', 'tax_amount',
            'total_amount', 'paid_amount', 'notes', 'signature', 'last_reminder_sent_at', 'validated_at', 'supplier_id',
        ];

    protected $casts =
        [
            'type' => DocumentType::class, 'status' => DocumentStatus::class, 'document_date' => 'date',
            'due_date' => 'date', 'sub_total' => 'decimal:3', 'tax_amount' => 'decimal:3',
            'total_amount' => 'decimal:3', 'paid_amount' => 'decimal:3', 'validated_at' => 'datetime',
            'last_reminder_sent_at' => 'datetime',
        ];

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sourceDocument(): BelongsTo
    {
        return $this->belongsTo(Document::class, 'source_document_id');
    }

    public function items(): HasMany
    {
        return $this->hasMany(DocumentItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class, 'invoice_id');
    }

    protected function balance(): Attribute
    {
        return Attribute::make(get: fn () => $this->total_amount - $this->paid_amount);
    }

    /**
     * Relation pour trouver le document qui a été créé à partir de celui-ci.
     */
    public function convertedToDocument(): HasOne
    {
        return $this->hasOne(Document::class, 'source_document_id');
    }
}
