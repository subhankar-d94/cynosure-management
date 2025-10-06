<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',
        'contact_person',
        'email',
        'phone',
        'mobile',
        'fax',
        'designation',
        'address',
        'city',
        'state',
        'postal_code',
        'country',
        'website',
        'category',
        'status',
        'registration_number',
        'tax_id',
        'description',
        'payment_terms',
        'credit_limit',
        'currency',
        'lead_time',
        'min_order_value',
        'discount_terms',
        'bank_name',
        'account_number',
        'routing_number',
        'swift_code',
        'bank_address',
        'products_services',
        'notes',
        'certifications',
        'insurance',
        'logo',
        'business_license',
        'tax_certificate',
        'insurance_certificate',
        'quality_certificates',
        'rating',
        'total_reviews',
        'total_orders',
        'total_value',
        'materials_supplied', // Keep for backward compatibility
    ];

    protected $casts = [
        'rating' => 'decimal:2',
        'credit_limit' => 'decimal:2',
        'min_order_value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'total_orders' => 'integer',
        'total_reviews' => 'integer',
        'lead_time' => 'integer',
    ];

    protected $attributes = [
        'status' => 'active',
        'rating' => 0.0,
        'total_reviews' => 0,
        'total_orders' => 0,
        'total_value' => 0.0,
        'currency' => 'USD',
        'payment_terms' => 'net_30',
    ];

    public function inventories(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }

    public function purchases(): HasMany
    {
        return $this->hasMany(Purchase::class);
    }

    public function rawMaterials(): HasMany
    {
        return $this->hasMany(RawMaterial::class);
    }

    // Scope for active suppliers
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Scope for suppliers by category
    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    // Scope for top rated suppliers
    public function scopeTopRated($query, $minRating = 4.0)
    {
        return $query->where('rating', '>=', $minRating);
    }

    // Accessor for formatted rating
    public function getFormattedRatingAttribute()
    {
        return number_format($this->rating, 1);
    }

    // Accessor for name (uses company_name as primary)
    public function getNameAttribute()
    {
        return $this->company_name ?: $this->contact_person;
    }

    // Accessor for display name (company_name fallback to contact_person)
    public function getDisplayNameAttribute()
    {
        return $this->company_name ?: $this->contact_person;
    }

    // Accessor for full address
    public function getFullAddressAttribute()
    {
        $parts = array_filter([
            $this->address,
            $this->city,
            $this->state,
            $this->postal_code,
            $this->country
        ]);

        return implode(', ', $parts);
    }
}
