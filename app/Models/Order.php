<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_email',
        'customer_phone',
        'customer_name',
        'subtotal',
        'tax_amount',
        'total_amount',
        'unique_amount',
        'status',
        'payment_status',
        'payment_method',
        'payment_reference',
        'payment_data',
        'customer_data',
        'notes',
        'processed_at',
        'completed_at',
        'snap_token',
        // Security fields
        'payment_signature',
        'security_token',
        'qris_transaction_id',
        'fraud_indicators',
        'is_suspicious',
        'security_status',
        'client_ip',
        'user_agent',
        'session_id',
        'security_verified_at',
        'verified_by',
        'payment_data_hash',
        'last_security_check',
        // Payment confirmation fields
        'payment_confirmed_by',
        'payment_confirmed_at',
        'payment_confirmation_notes',
        'auto_processed',
        'auto_processed_at',
    ];

    protected $casts = [
        'subtotal' => 'decimal:2',
        'tax_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'unique_amount' => 'decimal:2',
        'payment_data' => 'array',
        'customer_data' => 'array',
        'processed_at' => 'datetime',
        'completed_at' => 'datetime',
        'fraud_indicators' => 'array',
        'is_suspicious' => 'boolean',
        'security_verified_at' => 'datetime',
        'last_security_check' => 'datetime',
        'payment_confirmed_at' => 'datetime',
        'auto_processed' => 'boolean',
        'auto_processed_at' => 'datetime',
    ];

    /**
     * Boot the model.
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = static::generateOrderNumber();
            }
        });
    }

    /**
     * Get the order items for the order.
     */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Generate a unique order number.
     */
    public static function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'RS-' . date('Ymd') . '-' . strtoupper(Str::random(6));
        } while (static::where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Scope a query to only include pending orders.
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    /**
     * Scope a query to only include processing orders.
     */
    public function scopeProcessing($query)
    {
        return $query->where('status', 'processing');
    }

    /**
     * Scope a query to only include completed orders.
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed');
    }

    /**
     * Scope a query to only include paid orders.
     */
    public function scopePaid($query)
    {
        return $query->where('payment_status', 'paid');
    }

    /**
     * Check if order is paid.
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'paid';
    }

    /**
     * Check if order is completed.
     */
    public function isCompleted(): bool
    {
        return $this->status === 'completed';
    }

    /**
     * Mark order as paid.
     */
    public function markAsPaid(): void
    {
        $this->update([
            'payment_status' => 'paid',
            'status' => 'processing',
        ]);
    }

    /**
     * Mark order as completed.
     */
    public function markAsCompleted(): void
    {
        $this->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
    }

    /**
     * Get total items count.
     */
    public function getTotalItemsAttribute(): int
    {
        return $this->orderItems->sum('quantity');
    }

    /**
     * Check if all order items are completed.
     */
    public function allItemsCompleted(): bool
    {
        return $this->orderItems->every(function ($item) {
            return $item->status === 'completed';
        });
    }
}
