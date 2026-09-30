<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Product extends Model
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
        'category_id',
        'title',
        'title_bn',
        'slug',
        'sku',
        'regular_price',
        'sale_price',
        'stock_qty',
        'thumbnail',
        'gallery',
        'sizes',
        'colors',
        'short_desc',
        'description',
        'size_chart_html',
        'badge',
        'rating',
        'reviews_count',
        'is_featured',
        'is_trending',
        'is_active',
    ];

    protected $casts = [
        'gallery' => 'array',
        'sizes' => 'array',
        'colors' => 'array',
        'regular_price' => 'decimal:2',
        'sale_price' => 'decimal:2',
        'is_featured' => 'boolean',
        'is_trending' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($product) {
            if (empty($product->slug)) {
                $product->slug = Str::slug($product->title);
            }
            if (empty($product->sku)) {
                $product->sku = 'YF-' . strtoupper(Str::random(6));
            }
        });
    }

    protected $appends = [
        'effective_price',
        'discount_percent',
        'primary_image',
        'gallery_urls',
        'name',
    ];

    public function getNameAttribute(): string
    {
        return $this->title ?? '';
    }

    public function getPrimaryImageAttribute(): string
    {
        if ($this->thumbnail) {
            return resolve_image_url($this->thumbnail, 'images/placeholder.jpg');
        }
        if (!empty($this->gallery) && is_array($this->gallery) && count($this->gallery) > 0) {
            return resolve_image_url($this->gallery[0], 'images/placeholder.jpg');
        }
        return asset('images/placeholder.jpg');
    }

    public function getGalleryUrlsAttribute(): array
    {
        if (empty($this->gallery) || !is_array($this->gallery)) {
            return [];
        }
        return array_map(function ($img) {
            return resolve_image_url($img, 'images/placeholder.jpg');
        }, $this->gallery);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class)->where('is_approved', true)->latest();
    }

    public function getEffectivePriceAttribute()
    {
        return (float) ($this->sale_price ?? $this->regular_price ?? 0);
    }

    public function getDiscountPercentAttribute()
    {
        if ($this->sale_price && $this->regular_price > $this->sale_price) {
            return round((($this->regular_price - $this->sale_price) / $this->regular_price) * 100);
        }
        return 0;
    }

    public function getAverageRatingAttribute()
    {
        if ($this->relationLoaded('reviews') && $this->reviews->isNotEmpty()) {
            return round($this->reviews->avg('rating'), 1);
        }
        $dbAvg = $this->reviews()->avg('rating');
        if ($dbAvg) {
            return round($dbAvg, 1);
        }
        return (float) ($this->rating ?? 0);
    }

    public function getTotalReviewsCountAttribute()
    {
        if ($this->relationLoaded('reviews')) {
            return $this->reviews->count();
        }
        return $this->reviews()->count();
    }
}

