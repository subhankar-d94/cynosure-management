<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseItemUsageHistory extends Model
{
    use HasFactory;

    protected $table = 'purchase_item_usage_history';

    protected $fillable = [
        'purchase_item_id',
        'usage_date',
        'quantity_used',
        'quantity_remaining',
        'notes',
        'recorded_by',
    ];

    protected $casts = [
        'usage_date' => 'date',
        'quantity_used' => 'decimal:3',
        'quantity_remaining' => 'decimal:3',
    ];

    public function purchaseItem(): BelongsTo
    {
        return $this->belongsTo(PurchaseItem::class);
    }

    public function recordedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recorded_by');
    }
}
