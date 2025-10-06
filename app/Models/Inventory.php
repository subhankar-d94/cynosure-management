<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventory extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'quantity_in_stock',
        'reorder_level',
        'cost_per_unit',
        'supplier_id',
    ];

    protected $casts = [
        'cost_per_unit' => 'decimal:2',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('quantity_in_stock', '<=', 'reorder_level');
    }
}
