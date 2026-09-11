<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'customer_name',
        'customer_phone',
        'customer_address',
        'customer_note',
        'delivery_zone',
        'delivery_charge',
        'subtotal',
        'discount',
        'total_amount',
        'payment_method',
        'payment_status',
        'order_status',
        'admin_notes',
    ];

    protected $casts = [
        'delivery_charge' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($order) {
            if (empty($order->order_number)) {
                $order->order_number = 'YF-' . mt_rand(10000, 99999);
            }
        });
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function getZoneLabelAttribute(): string
    {
        return match($this->delivery_zone) {
            'inside_dhaka' => 'Inside Dhaka',
            'dhaka_suburbs' => 'Dhaka Suburbs',
            'outside_dhaka' => 'Outside Dhaka',
            default => ucfirst(str_replace('_', ' ', $this->delivery_zone)),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->order_status) {
            'pending' => 'badge-pending',
            'processing' => 'badge-processing',
            'shipped' => 'badge-shipped',
            'delivered' => 'badge-delivered',
            'cancelled' => 'badge-cancelled',
            default => 'badge-default',
        };
    }
}

