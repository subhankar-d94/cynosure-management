<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'customer_id',
        'invoice_number',
        'invoice_date',
        'issue_date',
        'due_date',
        'customer_name',
        'customer_email',
        'customer_address',
        'customer_phone',
        'total_amount',
        'total',
        'subtotal',
        'discount',
        'tax_amount',
        'status',
        'payment_status',
        'paid_amount',
        'pdf_path',
        'notes',
    ];

    protected $casts = [
        'invoice_date' => 'date',
        'issue_date' => 'date',
        'due_date' => 'date',
        'total_amount' => 'decimal:2',
        'total' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'paid_amount' => 'decimal:2',
    ];

    // Payment status constants
    const PAYMENT_STATUS_PENDING = 'pending';
    const PAYMENT_STATUS_PARTIAL = 'partial';
    const PAYMENT_STATUS_PAID = 'paid';
    const PAYMENT_STATUS_OVERDUE = 'overdue';

    // Status constants
    const STATUS_GENERATED = 'generated';
    const STATUS_SENT = 'sent';
    const STATUS_PAID = 'paid';

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePaid($query)
    {
        return $query->where('status', 'paid');
    }
}
