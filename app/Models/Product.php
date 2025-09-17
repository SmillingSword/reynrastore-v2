<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    protected $fillable = [
        'category_id',
        'name',
        'slug',
        'brand',
        'description',
        'image',
        'images',
        'base_price',
        'selling_price',
        'profit_percentage',
        'sku',
        'type',
        'diggie_product_id',
        'diggie_data',
        'is_active',
        'is_featured',
        'has_price_lists',
        'stock',
        'sort_order',
        'form_fields',
        'instructions',
    ];

    protected $casts = [
        'images' => 'array',
        'base_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'diggie_data' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'has_price_lists' => 'boolean',
        'stock' => 'integer',
        'sort_order' => 'integer',
        'form_fields' => 'array',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = $product->generateUniqueSlug($product->name);
            }
        });

        static::updating(function ($product) {
            if ($product->isDirty('name') && empty($product->slug)) {
                $product->slug = $product->generateUniqueSlug($product->name);
            }
        });
    }

    /**
     * Generate a unique slug for the product.
     */
    public function generateUniqueSlug($name)
    {
        $slug = Str::slug($name);
        $originalSlug = $slug;
        $counter = 1;

        while (static::where('slug', $slug)->where('id', '!=', $this->id ?? 0)->exists()) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        return $slug;
    }

    /**
     * Get the category that owns the product.
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get the order items for the product.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Get the price lists for the product.
     */
    public function priceLists(): HasMany
    {
        return $this->hasMany(PriceList::class);
    }

    /**
     * Get active price lists for the product.
     */
    public function activePriceLists(): HasMany
    {
        return $this->hasMany(PriceList::class)->where('is_active', true)
                    ->where('seller_status', true)
                    ->where('buyer_status', true);
    }

    /**
     * Scope a query to only include active products.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include featured products.
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Scope a query to only include Diggie products.
     */
    public function scopeDiggie($query)
    {
        return $query->where('type', 'diggie');
    }

    /**
     * Scope a query to only include manual products.
     */
    public function scopeManual($query)
    {
        return $query->where('type', 'manual');
    }

    /**
     * Scope a query to order products by sort order.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    /**
     * Calculate selling price based on base price and profit percentage.
     */
    public function calculateSellingPrice(): float
    {
        if ($this->profit_percentage > 0) {
            return $this->base_price * (1 + ($this->profit_percentage / 100));
        }
        
        return $this->base_price;
    }

    /**
     * Update selling price based on current profit percentage.
     */
    public function updateSellingPrice(): void
    {
        $this->selling_price = $this->calculateSellingPrice();
        $this->save();
    }

    /**
     * Check if product is in stock (for manual products).
     */
    public function isInStock(): bool
    {
        if ($this->type === 'manual') {
            return $this->stock > 0;
        }
        
        // Diggie products are always considered in stock
        return true;
    }

    /**
     * Get all images including the main image.
     */
    public function getAllImages(): array
    {
        $images = $this->images ?? [];
        
        if ($this->image) {
            array_unshift($images, $this->image);
        }
        
        return array_unique($images);
    }
}
