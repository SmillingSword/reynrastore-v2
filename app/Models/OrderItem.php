<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'product_id',
        'product_name',
        'product_sku',
        'unit_price',
        'quantity',
        'total_price',
        'product_data',
        'form_data',
        'status',
        'diggie_transaction_id',
        'diggie_response',
        'processing_notes',
        'processed_at',
        'completed_at',
        'error_message',
    ];

    protected $casts = [
        'unit_price' => 'decimal:2',
        'quantity' => 'integer',
        'total_price' => 'decimal:2',
        'product_data' => 'array',
        'form_data' => 'array',
        'diggie_response' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
    ];

    /**
     * Get the order that owns the order item.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Get the product that owns the order item.
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope a query to only include pending items.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing items.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed items.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include failed items.
     */
    public function scopeFailed($query)
    {
        return $query->where('status', 'failed');
    }

    /**
     * Check if item is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Check if item is failed.
     */
    public function isFailed(): bool
    {
        return $this->status === 'failed';
    }

    /**
     * Mark item as processing.
     */
    public function markAsProcessing(): void
    {
        $this->update(['status' => 'processing']);
    }

    /**
     * Mark item as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'processed_at' => now(),
        ]);
    }

    /**
     * Mark item as failed.
     */
    public function markAsFailed(string $notes = null): void
    {
        $this->update([
            'status' => 'failed',
            'processing_notes' => $notes,
            'processed_at' => now(),
        ]);
    }

    /**
     * Check if this is a Diggie product.
     */
    public function isDiggieProduct(): bool
    {
        return $this->product && $this->product->type === 'diggie';
    }

    /**
     * Check if this is a manual product.
     */
    public function isManualProduct(): bool
    {
        return $this->product && $this->product->type === 'manual';
    }
}
