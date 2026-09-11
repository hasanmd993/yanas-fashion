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

