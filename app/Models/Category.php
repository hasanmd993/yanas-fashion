<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'parent_id',
        'name',
        'name_bn',
        'slug',
        'image',
        'icon',
        'description',
        'sort_order',
        'is_featured',
        'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($category) {
            if (empty($category->slug)) {
                $category->slug = Str::slug($category->name);
            }
        });
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(Category::class, 'parent_id')->orderBy('sort_order', 'asc');
    }

    public function activeChildren(): HasMany
    {
        return $this->children()->where('is_active', true);
    }

    public function allChildren(): HasMany
    {
        return $this->children()->with('allChildren');
    }

    public function parentRecursive(): BelongsTo
    {
        return $this->parent()->with('parentRecursive');
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function scopeParents($query)
    {
        return $query->whereNull('parent_id');
    }

    public function isParent(): bool
    {
        return is_null($this->parent_id);
    }

    public function getLevelAttribute(): int
    {
        $level = 0;
        $current = $this;
        while ($current && $current->parent_id) {
            $level++;
            $current = $current->parent;
            if ($level > 5) break;
        }
        return $level;
    }

    public function getLevelNameAttribute(): string
    {
        return match ($this->level) {
            0 => 'Parent',
            1 => 'Subcategory',
            default => 'Child',
        };
    }

    public function getHierarchyPathAttribute(): string
    {
        $parts = [$this->name];
        $current = $this;
        $seen = [$this->id];
        while ($current->parent && !in_array($current->parent->id, $seen)) {
            array_unshift($parts, $current->parent->name);
            $seen[] = $current->parent->id;
            $current = $current->parent;
        }
        return implode(' > ', $parts);
    }

    public function getAllDescendantIds(): array
    {
        $ids = [$this->id];
        // Load children if not already loaded
        $children = $this->relationLoaded('children') ? $this->children : $this->children()->get();
        foreach ($children as $child) {
            $ids = array_merge($ids, $child->getAllDescendantIds());
        }
        return array_values(array_unique($ids));
    }
}
