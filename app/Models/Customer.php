<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'customer_type',
        'customer_code',
        'company_name',
        'gst_number',
        'notes',
        'status',
        'credit_limit',
        'payment_terms',
        'discount_percentage',
        'email_notifications',
        'sms_notifications',
        'marketing_emails',
        'preferred_contact_method',
    ];

    protected $casts = [
        'credit_limit' => 'decimal:2',
        'payment_terms' => 'integer',
        'discount_percentage' => 'decimal:2',
        'email_notifications' => 'boolean',
        'sms_notifications' => 'boolean',
        'marketing_emails' => 'boolean',
    ];

    protected $attributes = [
        'status' => 'active',
        'customer_type' => 'individual',
        'credit_limit' => 0,
        'payment_terms' => 30,
        'discount_percentage' => 0,
        'email_notifications' => true,
        'sms_notifications' => false,
        'marketing_emails' => false,
        'preferred_contact_method' => 'email',
    ];

    public function addresses(): HasMany
    {
        return $this->hasMany(CustomerAddress::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('customer_type', $type);
    }
}
