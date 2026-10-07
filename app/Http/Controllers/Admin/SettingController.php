<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

use Inertia\Inertia;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key');
        return Inertia::render('Admin/Settings/Index', [
            'settings' => $settings,
            'logoUrl' => function_exists('get_logo_url') ? get_logo_url() : asset('logo.png'),
            'faviconUrl' => function_exists('get_favicon_url') ? get_favicon_url() : asset('favicon.ico'),
        ]);
    }

    public function update(Request $request)
    {
        $allowedKeys = [
            'site_name',
            'tagline',
            'inside_dhaka_charge',
            'suburbs_charge',
            'outside_dhaka_charge',
            'free_shipping_threshold',
            'hotline',
            'whatsapp_number',
            'email',
            'address',
            'announcement_bar',
            'facebook_url',
            'instagram_url',
            'tiktok_url',
            'default_courier',
            'steadfast_api_key',
            'steadfast_secret_key',
            'pathao_client_id',
            'pathao_client_secret',
            'pathao_username',
            'pathao_password',
            'pathao_store_id',
            'pathao_sandbox',
            'sms_enabled',
            'sms_provider',
            'sms_api_key',
            'sms_sender_id',
            'sms_generic_url',
            'sms_order_placed_enabled',
            'sms_order_placed_template',
            'sms_order_shipped_enabled',
            'sms_order_shipped_template',
            'sms_admin_alert_enabled',
            'sms_admin_phone',
        ];

        $request->validate([
            'site_logo' => 'nullable|image|mimes:png,jpg,jpeg,svg,webp,gif|max:4096',
            'site_favicon' => 'nullable|file|mimes:ico,png,svg,jpg,jpeg,webp|max:2048',
            'site_name' => 'nullable|string|max:255',
            'tagline' => 'nullable|string|max:255',
            'inside_dhaka_charge' => 'nullable|numeric|min:0',
            'suburbs_charge' => 'nullable|numeric|min:0',
            'outside_dhaka_charge' => 'nullable|numeric|min:0',
            'free_shipping_threshold' => 'nullable|numeric|min:0',
            'hotline' => 'nullable|string|max:50',
            'whatsapp_number' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'facebook_url' => 'nullable|url|max:255',
            'instagram_url' => 'nullable|url|max:255',
            'tiktok_url' => 'nullable|url|max:255',
            'default_courier' => 'nullable|string|in:steadfast,pathao',
            'sms_provider' => 'nullable|string|in:log,greenweb,bulksmsbd,generic',
            'sms_generic_url' => [
                'nullable',
                'url',
                function ($attribute, $value, $fail) {
                    if (empty($value)) return;
                    $parsed = parse_url($value);
                    $host = $parsed['host'] ?? '';
                    if (empty($host) || in_array(strtolower($host), ['localhost', '127.0.0.1', '::1', '0.0.0.0', '169.254.169.254'])) {
                        $fail('The generic SMS gateway URL cannot point to internal or loopback addresses.');
                        return;
                    }
                    $ip = gethostbyname($host);
                    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
                        $fail('The generic SMS gateway URL resolves to an internal or private network address.');
                    }
                },
            ],
        ]);

        $uploadDir = public_path('uploads/settings');
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }

        // Handle Site Logo Upload
        if ($request->hasFile('site_logo')) {
            $file = $request->file('site_logo');
            $filename = 'logo_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            Setting::set('site_logo', 'uploads/settings/' . $filename);
        }

        // Handle Site Favicon Upload
        if ($request->hasFile('site_favicon')) {
            $file = $request->file('site_favicon');
            $filename = 'favicon_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($uploadDir, $filename);
            Setting::set('site_favicon', 'uploads/settings/' . $filename);
        }

        $data = $request->only($allowedKeys);

        foreach ($data as $key => $value) {
            Setting::set($key, $value);
        }

        return redirect()->back()->with('success', 'Store settings and branding saved successfully!');
    }
}

