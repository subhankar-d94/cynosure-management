<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'description',
        'base_price',
        'weight',
        'dimensions',
        'is_customizable',
        'sku',
        'images',
        'stock_quantity',
        'reorder_level',
        'cost_per_unit',
        'supplier_id',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'array',
        'is_customizable' => 'boolean',
        'images' => 'array',
        'stock_quantity' => 'integer',
        'reorder_level' => 'integer',
        'cost_per_unit' => 'decimal:2',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function supplier(): BelongsTo
    {
        return $this->belongsTo(Supplier::class);
    }

    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function scopeCustomizable($query)
    {
        return $query->where('is_customizable', true);
    }

    public function getImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    public function setImagesAttribute($value)
    {
        $this->attributes['images'] = $value ? json_encode($value) : null;
    }

    public function getFirstImageAttribute()
    {
        $images = $this->images;
        return !empty($images) ? $images[0] : null;
    }

    public function hasImages()
    {
        return !empty($this->images);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = static::generateSku($product->category_id);
            }
        });
    }

    public static function generateSku($categoryId): string
    {
        $category = Category::find($categoryId);
        if (!$category) {
            throw new \Exception('Category not found for SKU generation');
        }

        // Create category prefix from name (first 3 letters, uppercase)
        $categoryPrefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $category->name), 0, 3));

        // If category name is too short, pad with 'X'
        $categoryPrefix = str_pad($categoryPrefix, 3, 'X');

        // Get the next sequential number for this category
        $lastProduct = static::where('category_id', $categoryId)
                            ->where('sku', 'LIKE', $categoryPrefix . '%')
                            ->orderBy('sku', 'desc')
                            ->first();

        $sequenceNumber = 1;
        if ($lastProduct && preg_match('/(\d+)$/', $lastProduct->sku, $matches)) {
            $sequenceNumber = (int)$matches[1] + 1;
        }

        // Format: CAT0001, CAT0002, etc.
        return $categoryPrefix . str_pad($sequenceNumber, 4, '0', STR_PAD_LEFT);
    }

    public function regenerateSku(): string
    {
        $this->sku = static::generateSku($this->category_id);
        $this->save();
        return $this->sku;
    }

    public function getFormattedSkuAttribute(): string
    {
        return $this->sku ?? 'Auto-generated';
    }

    // Stock management scopes
    public function scopeLowStock($query)
    {
        return $query->whereColumn('stock_quantity', '<=', 'reorder_level')
                     ->where('stock_quantity', '>', 0);
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('stock_quantity', 0);
    }

    public function scopeInStock($query)
    {
        return $query->where('stock_quantity', '>', 0);
    }

    // Stock management helper methods
    public function getStockValueAttribute(): float
    {
        return $this->stock_quantity * ($this->cost_per_unit ?? 0);
    }

    public function isLowStock(): bool
    {
        return $this->stock_quantity <= $this->reorder_level && $this->stock_quantity > 0;
    }

    public function isOutOfStock(): bool
    {
        return $this->stock_quantity == 0;
    }
}
