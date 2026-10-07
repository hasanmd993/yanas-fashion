<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'key',
        'value',
    ];

    protected const CACHE_KEY = 'store_settings_cache_map';

    /**
     * Retrieve all settings mapped as key => value with caching.
     */
    public static function getAllCached(): array
    {
        return Cache::rememberForever(self::CACHE_KEY, function () {
            try {
                return static::pluck('value', 'key')->toArray();
            } catch (\Throwable $e) {
                return [];
            }
        });
    }

    /**
     * Retrieve a specific setting value from cache.
     */
    public static function get($key, $default = null)
    {
        $settings = static::getAllCached();
        return array_key_exists($key, $settings) ? $settings[$key] : $default;
    }

    /**
     * Save a setting value and invalidate cache.
     */
    public static function set($key, $value)
    {
        $result = static::updateOrCreate(['key' => $key], ['value' => $value]);
        Cache::forget(self::CACHE_KEY);
        return $result;
    }

    /**
     * Explicitly clear settings cache.
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }
}

