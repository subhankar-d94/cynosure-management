<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Supplier extends Model
{
    use HasFactory;

    protected $fillable = [
        'company_name',     // Required
        'contact_person',   // Required
        'category',         // Required
        'email',           // Optional
        'phone',           // Optional
        'gst_number',      // Optional
        'address',         // Optional
        'website',         // Optional
        'status',          // Optional (defaults to 'active')
    ];

    protected $attributes = [
        'status' => 'active',
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
}
