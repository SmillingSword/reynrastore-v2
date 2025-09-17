<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PriceList extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'diggie_sku_code',
        'name',
        'type',
        'description',
        'base_price',
        'selling_price',
        'profit_percentage',
        'seller_status',
        'buyer_status',
        'unlimited_stock',
        'stock',
        'multi',
        'start_cut_off',
        'end_cut_off',
        'is_active',
    ];

    protected $casts = [
        'base_price' => 'decimal:2',
        'selling_price' => 'decimal:2',
        'profit_percentage' => 'decimal:2',
        'seller_status' => 'boolean',
        'buyer_status' => 'boolean',
        'unlimited_stock' => 'boolean',
        'multi' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Get the product that owns the price list
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for active price lists
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where('seller_status', true)
                    ->where('buyer_status', true);
    }

    /**
     * Get formatted price
     */
    public function getFormattedPriceAttribute()
    {
        return 'Rp ' . number_format($this->selling_price, 0, ',', '.');
    }

    /**
     * Get formatted base price
     */
    public function getFormattedBasePriceAttribute()
    {
        return 'Rp ' . number_format($this->base_price, 0, ',', '.');
    }
}
