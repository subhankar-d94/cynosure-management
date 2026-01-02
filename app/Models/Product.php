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
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'weight' => 'decimal:3',
        'dimensions' => 'array',
        'is_customizable' => 'boolean',
        'images' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function inventory(): HasOne
    {
        return $this->hasOne(Inventory::class);
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
}
