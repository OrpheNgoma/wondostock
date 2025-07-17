<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Storage;

class Store extends Model
{
    /** @use HasFactory<\Database\Factories\StoreFactory> */
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id', 'name', 'address', 'city', 'contact_phone', 'is_active',
        'is_country_branch', 'country_code', 'country_name', 'nif', 'rccm',
        'business_permit', 'tax_id', 'email', 'website', 'postal_box',
        'invoice_header_image', 'invoice_footer_image',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_country_branch' => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Company::class);
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(Document::class);
    }

    public function stockMovements(): HasMany
    {
        return $this->hasMany(StockMovement::class);
    }

    /**
     * Obtient l'URL complète de l'image d'en-tête pour les factures
     */
    public function getInvoiceHeaderImageUrl(): ?string
    {
        if (! $this->invoice_header_image) {
            return null;
        }

        return Storage::url($this->invoice_header_image);
    }

    /**
     * Obtient l'URL complète de l'image de pied de page pour les factures
     */
    public function getInvoiceFooterImageUrl(): ?string
    {
        if (! $this->invoice_footer_image) {
            return null;
        }

        return Storage::url($this->invoice_footer_image);
    }

    /**
     * Obtient l'image d'en-tête encodée en base64 pour les PDFs
     */
    public function getInvoiceHeaderImageBase64(): ?string
    {
        if (! $this->invoice_header_image) {
            return null;
        }

        $path = Storage::path($this->invoice_header_image);

        if (! file_exists($path)) {
            return null;
        }

        $imageData = file_get_contents($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return 'data:image/'.$extension.';base64,'.base64_encode($imageData);
    }

    /**
     * Obtient l'image de pied de page encodée en base64 pour les PDFs
     */
    public function getInvoiceFooterImageBase64(): ?string
    {
        if (! $this->invoice_footer_image) {
            return null;
        }

        $path = Storage::path($this->invoice_footer_image);

        if (! file_exists($path)) {
            return null;
        }

        $imageData = file_get_contents($path);
        $extension = pathinfo($path, PATHINFO_EXTENSION);

        return 'data:image/'.$extension.';base64,'.base64_encode($imageData);
    }

    /**
     * Scope pour obtenir seulement les branches pays
     */
    public function scopeCountryBranches($query)
    {
        return $query->where('is_country_branch', true);
    }

    /**
     * Scope pour obtenir seulement les magasins normaux
     */
    public function scopeRegularStores($query)
    {
        return $query->where('is_country_branch', false);
    }
}
