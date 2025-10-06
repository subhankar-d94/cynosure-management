<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'description',
        'sku',
        'unit',
        'quantity',
        'unit_price',
        'tax_rate',
        'received_quantity',
        'notes',
        'material_name', // Keep for backward compatibility
        'unit_cost', // Keep for backward compatibility
        'subtotal', // Keep for backward compatibility
    ];

    protected $casts = [
        'quantity' => 'decimal:3',
        'unit_price' => 'decimal:2',
        'tax_rate' => 'decimal:2',
        'received_quantity' => 'decimal:3',
        'unit_cost' => 'decimal:2', // Backward compatibility
        'subtotal' => 'decimal:2', // Backward compatibility
    ];

    protected $attributes = [
        'received_quantity' => 0,
        'tax_rate' => 0,
    ];

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function getTotalAttribute(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        $taxAmount = $subtotal * ($this->tax_rate / 100);
        return $subtotal + $taxAmount;
    }

    public function getSubtotalAttribute(): float
    {
        return $this->quantity * $this->unit_price;
    }

    public function getTaxAmountAttribute(): float
    {
        $subtotal = $this->quantity * $this->unit_price;
        return $subtotal * ($this->tax_rate / 100);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    public function getIsFullyReceivedAttribute(): bool
    {
        return $this->received_quantity >= $this->quantity;
    }

    public function getReceiveProgressAttribute(): int
    {
        return $this->quantity > 0 ? (int) round(($this->received_quantity / $this->quantity) * 100) : 0;
    }
}
