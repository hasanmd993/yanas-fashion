<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Order extends Model
{
    use HasFactory, LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll()
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

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
        'courier_name',
        'courier_tracking_code',
        'courier_consignment_id',
        'courier_status',
        'courier_dispatched_at',
        'courier_response',
    ];

    protected $casts = [
        'delivery_charge' => 'decimal:2',
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total_amount' => 'decimal:2',
        'courier_dispatched_at' => 'datetime',
        'courier_response' => 'array',
    ];

    protected $appends = [
        'zone_label',
        'status_badge_class',
        'courier_tracking_url',
        'courier_label',
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

    public function smsLogs(): HasMany
    {
        return $this->hasMany(SmsLog::class)->latest();
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

    public function getCourierTrackingUrlAttribute(): ?string
    {
        if (empty($this->courier_tracking_code)) {
            return null;
        }

        return match($this->courier_name) {
            'steadfast' => "https://steadfast.com.bd/t/{$this->courier_tracking_code}",
            'pathao' => "https://pathao.com/courier-tracking/?consignment_id={$this->courier_tracking_code}",
            default => null,
        };
    }

    public function getCourierLabelAttribute(): string
    {
        return match($this->courier_name) {
            'steadfast' => 'Steadfast Express',
            'pathao' => 'Pathao Courier',
            'manual' => 'Manual Courier',
            default => $this->courier_name ? ucfirst($this->courier_name) : 'Not Dispatched',
        };
    }
}

