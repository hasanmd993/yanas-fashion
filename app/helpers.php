<?php

use App\Models\Setting;

if (!function_exists('get_setting')) {
    function get_setting($key, $default = null)
    {
        return Setting::get($key, $default);
    }
}

if (!function_exists('get_logo_url')) {
    function get_logo_url()
    {
        $logo = Setting::get('site_logo');
        if ($logo && file_exists(public_path($logo))) {
            return asset($logo);
        }
        if (file_exists(public_path('assets/logo.png'))) {
            return asset('assets/logo.png');
        }
        return asset('favicon.ico');
    }
}

if (!function_exists('get_logo_path')) {
    function get_logo_path()
    {
        $logo = Setting::get('site_logo');
        if ($logo && file_exists(public_path($logo))) {
            return public_path($logo);
        }
        if (file_exists(public_path('assets/logo.png'))) {
            return public_path('assets/logo.png');
        }
        return null;
    }
}

if (!function_exists('get_logo_base64')) {
    function get_logo_base64()
    {
        $path = get_logo_path();
        if ($path && file_exists($path)) {
            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
            $mime = match ($ext) {
                'png' => 'image/png',
                'jpg', 'jpeg' => 'image/jpeg',
                'svg' => 'image/svg+xml',
                'webp' => 'image/webp',
                'gif' => 'image/gif',
                'ico' => 'image/x-icon',
                default => 'image/png'
            };
            $data = file_get_contents($path);
            return 'data:' . $mime . ';base64,' . base64_encode($data);
        }
        return '';
    }
}

if (!function_exists('get_favicon_url')) {
    function get_favicon_url()
    {
        $favicon = Setting::get('site_favicon');
        if ($favicon && file_exists(public_path($favicon))) {
            return asset($favicon);
        }
        if (file_exists(public_path('assets/logo.png'))) {
            return asset('assets/logo.png');
        }
        return asset('favicon.ico');
    }
}

if (!function_exists('get_nav_categories')) {
    function get_nav_categories()
    {
        return \App\Models\Category::whereNull('parent_id')
            ->where('is_active', true)
            ->with(['activeChildren.activeChildren'])
            ->orderBy('sort_order', 'asc')
            ->get();
    }
}

if (!function_exists('get_whatsapp_number')) {
    function get_whatsapp_number()
    {
        $num = Setting::get('whatsapp_number', '8801713580400');
        return preg_replace('/[^0-9]/', '', (string)$num) ?: '8801713580400';
    }
}

if (!function_exists('resolve_image_url')) {
    function resolve_image_url(?string $path, string $default = 'images/placeholder.jpg'): string
    {
        if (empty($path)) {
            return asset($default);
        }
        if (str_starts_with($path, 'http://') || str_starts_with($path, 'https://')) {
            return $path;
        }
        $trimmed = ltrim($path, '/');
        $cleanStorage = preg_replace('#^storage/#i', '', $trimmed);

        // 1. Direct file in public/
        if (file_exists(public_path($trimmed))) {
            return asset($trimmed);
        }
        // 2. File in public/storage/
        if (file_exists(public_path('storage/' . $cleanStorage))) {
            return asset('storage/' . $cleanStorage);
        }
        // 3. File in storage/app/public/
        if (file_exists(storage_path('app/public/' . $cleanStorage))) {
            return asset('storage/' . $cleanStorage);
        }
        // 4. Known folder patterns
        if (str_starts_with($trimmed, 'assets/') || str_starts_with($trimmed, 'images/')) {
            return asset($trimmed);
        }
        if (str_starts_with($trimmed, 'storage/')) {
            return asset($trimmed);
        }
        return asset('storage/' . $cleanStorage);
    }
}

