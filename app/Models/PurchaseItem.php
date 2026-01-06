<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

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
        'used_quantity',
        'remaining_quantity',
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
        'used_quantity' => 'decimal:3',
        'remaining_quantity' => 'decimal:3',
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

    public function usageHistory(): HasMany
    {
        return $this->hasMany(PurchaseItemUsageHistory::class);
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

    /**
     * Record usage of this purchase item
     *
     * @param float $quantity Quantity used
     * @param string|null $notes Optional notes
     * @param int|null $userId User who recorded the usage
     * @return bool Success status
     */
    public function recordUsage(float $quantity, ?string $notes = null, ?int $userId = null): bool
    {
        DB::beginTransaction();
        try {
            // Update used and remaining quantities
            $this->used_quantity += $quantity;
            $this->remaining_quantity = $this->received_quantity - $this->used_quantity;
            $this->save();

            // Create history record
            PurchaseItemUsageHistory::create([
                'purchase_item_id' => $this->id,
                'usage_date' => now(),
                'quantity_used' => $quantity,
                'quantity_remaining' => $this->remaining_quantity,
                'notes' => $notes,
                'recorded_by' => $userId,
            ]);

            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Failed to record purchase item usage', [
                'purchase_item_id' => $this->id,
                'quantity' => $quantity,
                'error' => $e->getMessage()
            ]);
            return false;
        }
    }
}
