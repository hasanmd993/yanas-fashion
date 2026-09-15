<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

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
        return $this->sale_price ?? $this->regular_price;
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

