<?php

namespace App\Services\Sms;

use App\Models\Order;
use App\Models\Setting;
use App\Models\SmsLog;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsService
{
    /**
     * Format a Bangladeshi phone number
     */
    public static function formatPhone(?string $phone, bool $withCountryCode = true): ?string
    {
        if (empty($phone)) {
            return null;
        }

        $digits = preg_replace('/[^0-9]/', '', $phone);

        // Remove leading 88 if present
        if (str_starts_with($digits, '8801')) {
            $digits = substr($digits, 2);
        }

        // If 10 digits starting with 1, prepend 0
        if (strlen($digits) === 10 && str_starts_with($digits, '1')) {
            $digits = '0' . $digits;
        }

        if (strlen($digits) !== 11 || !str_starts_with($digits, '01')) {
            return $phone; // Fallback to raw input
        }

        return $withCountryCode ? ('88' . $digits) : $digits;
    }

    /**
     * Dispatch an SMS message to a phone number
     */
    public function send(string $phone, string $message, ?int $orderId = null, string $event = 'custom'): array
    {
        $enabled = (bool) Setting::get('sms_enabled', false);
        $provider = Setting::get('sms_provider', 'log');
        $apiKey = trim((string) Setting::get('sms_api_key', ''));
        $senderId = trim((string) Setting::get('sms_sender_id', ''));

        // If SMS is globally disabled or credentials missing, log simulated delivery
        if (!$enabled || $provider === 'log' || (empty($apiKey) && $provider !== 'generic')) {
            $log = SmsLog::create([
                'order_id' => $orderId,
                'phone' => $phone,
                'message' => $message,
                'event' => $event,
                'gateway' => $provider,
                'status' => 'simulated',
                'response' => $enabled ? 'Gateway set to log/simulation mode' : 'SMS notifications disabled in settings',
            ]);

            return [
                'success' => true,
                'simulated' => true,
                'message' => 'SMS delivery simulated and logged successfully.',
                'log' => $log,
            ];
        }

        $formattedWith88 = self::formatPhone($phone, true);
        $formattedLocal = self::formatPhone($phone, false);

        try {
            $result = match($provider) {
                'greenweb' => $this->sendViaGreenweb($apiKey, $formattedWith88, $message),
                'bulksmsbd' => $this->sendViaBulkSmsBd($apiKey, $senderId, $formattedWith88, $message),
                'generic' => $this->sendViaGeneric($apiKey, $senderId, $formattedWith88, $message),
                default => ['success' => false, 'message' => "Unknown SMS gateway: {$provider}", 'response' => null],
            };

            $status = $result['success'] ? 'sent' : 'failed';

            $log = SmsLog::create([
                'order_id' => $orderId,
                'phone' => $phone,
                'message' => $message,
                'event' => $event,
                'gateway' => $provider,
                'status' => $status,
                'response' => is_string($result['response']) ? $result['response'] : json_encode($result['response']),
            ]);

            return [
                'success' => $result['success'],
                'simulated' => false,
                'message' => $result['message'],
                'log' => $log,
            ];
        } catch (\Throwable $e) {
            Log::error('SMS Dispatch Exception: ' . $e->getMessage(), [
                'phone' => $phone,
                'event' => $event,
                'order_id' => $orderId,
            ]);

            $log = SmsLog::create([
                'order_id' => $orderId,
                'phone' => $phone,
                'message' => $message,
                'event' => $event,
                'gateway' => $provider,
                'status' => 'failed',
                'response' => 'Exception: ' . $e->getMessage(),
            ]);

            return [
                'success' => false,
                'simulated' => false,
                'message' => 'SMS sending failed: ' . $e->getMessage(),
                'log' => $log,
            ];
        }
    }

    /**
     * Greenweb BD SMS Gateway
     * Endpoint: http://api.greenweb.com.bd/api.php
     */
    protected function sendViaGreenweb(string $token, string $to, string $message): array
    {
        $response = Http::timeout(15)->asForm()->post('https://api.greenweb.com.bd/api.php', [
            'token' => $token,
            'to' => $to,
            'message' => $message,
        ]);

        $body = trim($response->body());

        // Greenweb typically responds with "Ok: ..." on success
        if (str_starts_with(strtolower($body), 'ok')) {
            return [
                'success' => true,
                'message' => 'SMS delivered successfully via Greenweb BD.',
                'response' => $body,
            ];
        }

        return [
            'success' => false,
            'message' => 'Greenweb error: ' . $body,
            'response' => $body,
        ];
    }

    /**
     * BulkSMSBD SMS Gateway
     * Endpoint: http://bulksmsbd.net/api/smsapi
     */
    protected function sendViaBulkSmsBd(string $apiKey, string $senderId, string $to, string $message): array
    {
        $payload = [
            'api_key' => $apiKey,
            'type' => 'text',
            'number' => $to,
            'senderid' => $senderId,
            'message' => $message,
        ];

        $response = Http::timeout(15)->asForm()->post('https://bulksmsbd.net/api/smsapi', $payload);
        $data = $response->json();

        // BulkSMSBD returns response_code 202 on accepted/submitted
        if (isset($data['response_code']) && $data['response_code'] == 202) {
            return [
                'success' => true,
                'message' => 'SMS delivered successfully via BulkSMSBD.',
                'response' => $data,
            ];
        }

        $errorMsg = $data['error_message'] ?? $data['success_message'] ?? $response->body();

        return [
            'success' => false,
            'message' => 'BulkSMSBD error: ' . $errorMsg,
            'response' => $data ?: $response->body(),
        ];
    }

    /**
     * Generic HTTP Webhook / Custom Gateway
     */
    protected function sendViaGeneric(string $apiKey, string $senderId, string $to, string $message): array
    {
        $genericUrl = trim((string) Setting::get('sms_generic_url', ''));

        if (empty($genericUrl)) {
            return [
                'success' => false,
                'message' => 'Generic SMS Gateway URL is not configured.',
                'response' => null,
            ];
        }

        // Replace placeholders
        $url = str_replace(
            ['{api_key}', '{token}', '{sender_id}', '{senderid}', '{to}', '{phone}', '{number}', '{message}', '{msg}'],
            [urlencode($apiKey), urlencode($apiKey), urlencode($senderId), urlencode($senderId), urlencode($to), urlencode($to), urlencode($to), urlencode($message), urlencode($message)],
            $genericUrl
        );

        $parsed = parse_url($url);
        $scheme = strtolower($parsed['scheme'] ?? '');
        $host = $parsed['host'] ?? '';

        if (!in_array($scheme, ['http', 'https']) || empty($host)) {
            return [
                'success' => false,
                'message' => 'Invalid Generic SMS Gateway URL format.',
                'response' => null,
            ];
        }

        $ip = gethostbyname($host);
        if ($ip === $host && filter_var($ip, FILTER_VALIDATE_IP) === false) {
            return [
                'success' => false,
                'message' => 'Unable to resolve Generic SMS Gateway hostname.',
                'response' => null,
            ];
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE) === false) {
            return [
                'success' => false,
                'message' => 'Generic SMS Gateway URL resolves to a private or reserved network address.',
                'response' => null,
            ];
        }

        $response = Http::timeout(15)->get($url);

        return [
            'success' => $response->successful(),
            'message' => $response->successful() ? 'SMS sent via Generic Gateway.' : 'Generic Gateway returned HTTP ' . $response->status(),
            'response' => $response->body(),
        ];
    }

    /**
     * Send Order Placed confirmation SMS to customer
     */
    public function sendOrderPlaced(Order $order): array
    {
        $orderPlacedEnabled = (bool) Setting::get('sms_order_placed_enabled', true);
        if (!$orderPlacedEnabled) {
            return ['success' => false, 'message' => 'Order placed SMS is disabled in settings.'];
        }

        $defaultTemplate = "Dear {customer_name}, your order #{order_number} for BDT {total_amount} at {site_name} has been placed successfully! Track: {tracking_url}";
        $template = Setting::get('sms_order_placed_template', $defaultTemplate);

        $message = $this->renderTemplate($template, $order);

        return $this->send($order->customer_phone, $message, $order->id, 'order_placed');
    }

    /**
     * Send Order Shipped notification SMS to customer
     */
    public function sendOrderShipped(Order $order): array
    {
        $orderShippedEnabled = (bool) Setting::get('sms_order_shipped_enabled', true);
        if (!$orderShippedEnabled) {
            return ['success' => false, 'message' => 'Order shipped SMS is disabled in settings.'];
        }

        $defaultTemplate = "Dear {customer_name}, your order #{order_number} has been shipped via {courier_name}! Tracking Code: {tracking_code}. Track: {tracking_url}";
        $template = Setting::get('sms_order_shipped_template', $defaultTemplate);

        $message = $this->renderTemplate($template, $order);

        return $this->send($order->customer_phone, $message, $order->id, 'order_shipped');
    }

    /**
     * Send Admin alert SMS when a new order is received
     */
    public function sendAdminNewOrderAlert(Order $order): array
    {
        $adminAlertEnabled = (bool) Setting::get('sms_admin_alert_enabled', false);
        $adminPhone = trim((string) Setting::get('sms_admin_phone', ''));

        if (!$adminAlertEnabled || empty($adminPhone)) {
            return ['success' => false, 'message' => 'Admin alert SMS is disabled or phone is empty.'];
        }

        $siteName = Setting::get('site_name', 'Yanas Fashion');
        $message = "[{$siteName}] New Order #{$order->order_number} received from {$order->customer_name} ({$order->customer_phone}) for BDT " . number_format($order->total_amount) . ".";

        return $this->send($adminPhone, $message, $order->id, 'admin_alert');
    }

    /**
     * Replace template placeholders with real order data
     */
    public function renderTemplate(string $template, Order $order): string
    {
        $siteName = Setting::get('site_name', 'Yanas Fashion');
        $trackingUrl = route('tracking.index') . '?order_number=' . $order->order_number . '&phone=' . $order->customer_phone;
        $courierName = $order->courier_label ?: ($order->courier_name ? ucfirst($order->courier_name) : 'Express Courier');
        $trackingCode = $order->courier_tracking_code ?: 'Processing';

        $replacements = [
            '{customer_name}' => $order->customer_name,
            '{order_number}' => $order->order_number,
            '{total_amount}' => number_format($order->total_amount),
            '{items_count}' => $order->items ? $order->items->count() : 1,
            '{delivery_charge}' => number_format($order->delivery_charge),
            '{site_name}' => $siteName,
            '{tracking_url}' => $trackingUrl,
            '{courier_name}' => $courierName,
            '{tracking_code}' => $trackingCode,
            '{date}' => $order->created_at ? $order->created_at->format('d M Y') : date('d M Y'),
        ];

        return str_replace(array_keys($replacements), array_values($replacements), $template);
    }

    /**
     * Check gateway balance where supported
     */
    public function getBalance(): array
    {
        $provider = Setting::get('sms_provider', 'log');
        $apiKey = trim((string) Setting::get('sms_api_key', ''));

        if ($provider === 'greenweb') {
            if (empty($apiKey)) {
                return ['success' => false, 'message' => 'Greenweb API Token is not configured.'];
            }

            try {
                $response = Http::timeout(10)->get("https://api.greenweb.com.bd/grebxml.php?token={$apiKey}&balance");
                return [
                    'success' => true,
                    'balance' => trim($response->body()),
                    'provider' => 'Greenweb BD',
                ];
            } catch (\Throwable $e) {
                return ['success' => false, 'message' => 'Failed to reach Greenweb: ' . $e->getMessage()];
            }
        } elseif ($provider === 'bulksmsbd') {
            if (empty($apiKey)) {
                return ['success' => false, 'message' => 'BulkSMSBD API Key is not configured.'];
            }

            try {
                $response = Http::timeout(10)->get("https://bulksmsbd.net/api/getBalanceApi?api_key={$apiKey}");
                $data = $response->json();
                return [
                    'success' => true,
                    'balance' => $data['balance'] ?? ($data['response_message'] ?? $response->body()),
                    'provider' => 'BulkSMSBD',
                ];
            } catch (\Throwable $e) {
                return ['success' => false, 'message' => 'Failed to reach BulkSMSBD: ' . $e->getMessage()];
            }
        }

        return [
            'success' => true,
            'balance' => 'Simulation Mode (Unlimited)',
            'provider' => ucfirst($provider),
        ];
    }
}
