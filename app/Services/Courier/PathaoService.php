<?php

namespace App\Services\Courier;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PathaoService
{
    protected function getBaseUrl(): string
    {
        $isSandbox = Setting::get('pathao_sandbox', false);
        return $isSandbox
            ? 'https://courier-api-sandbox.pathao.com'
            : 'https://api-hermes.pathao.com';
    }

    public function isConfigured(): bool
    {
        $clientId = Setting::get('pathao_client_id');
        $clientSecret = Setting::get('pathao_client_secret');
        $username = Setting::get('pathao_username');
        $password = Setting::get('pathao_password');
        $storeId = Setting::get('pathao_store_id');

        return !empty($clientId) && !empty($clientSecret) && !empty($username) && !empty($password) && !empty($storeId);
    }

    /**
     * Get OAuth Bearer Access Token (Cached for efficiency)
     */
    public function getAccessToken(): ?string
    {
        return Cache::remember('pathao_courier_access_token', 3300, function () {
            $clientId = Setting::get('pathao_client_id');
            $clientSecret = Setting::get('pathao_client_secret');
            $username = Setting::get('pathao_username');
            $password = Setting::get('pathao_password');

            if (empty($clientId) || empty($clientSecret) || empty($username) || empty($password)) {
                return null;
            }

            try {
                $response = Http::asJson()
                    ->timeout(15)
                    ->post("{$this->getBaseUrl()}/aladdin/api/v1/issue-token", [
                        'client_id' => $clientId,
                        'client_secret' => $clientSecret,
                        'username' => $username,
                        'password' => $password,
                        'grant_type' => 'password',
                    ]);

                $data = $response->json();
                if ($response->successful() && !empty($data['access_token'])) {
                    return $data['access_token'];
                }

                Log::error('Pathao Token Error', ['response' => $data]);
                return null;
            } catch (\Exception $e) {
                Log::error('Pathao Token Exception: ' . $e->getMessage());
                return null;
            }
        });
    }

    /**
     * Dispatch an order to Pathao Courier
     */
    public function createOrder(Order $order, array $custom = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Pathao API credentials (Client ID, Secret, Username, Password, Store ID) are missing in Settings.',
            ];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return [
                'success' => false,
                'message' => 'Could not authenticate with Pathao API. Please verify your credentials.',
            ];
        }

        // Clean phone number (11 digits e.g. 017XXXXXXXX)
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 2);
        }

        $codAmount = ($order->payment_status === 'paid')
            ? 0
            : (float)($custom['cod_amount'] ?? $order->total_amount);

        $payload = [
            'store_id' => (int)Setting::get('pathao_store_id'),
            'merchant_order_id' => $order->order_number,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $phone,
            'recipient_address' => $order->customer_address,
            'recipient_city' => (int)($custom['recipient_city'] ?? 1), // Default 1 (Dhaka)
            'recipient_zone' => (int)($custom['recipient_zone'] ?? 1),
            'delivery_type' => 48, // 48 hours normal delivery
            'item_type' => 2, // 2: Parcel
            'special_instruction' => $custom['note'] ?? ($order->customer_note ?: 'Yanas Fashion Luxury Apparel'),
            'item_quantity' => $order->items->sum('quantity') ?: 1,
            'item_weight' => (float)($custom['weight'] ?? 0.5), // in kg
            'amount_to_collect' => $codAmount,
            'item_description' => 'Luxury clothing and apparel',
        ];

        try {
            $response = Http::withToken($token)
                ->asJson()
                ->timeout(15)
                ->post("{$this->getBaseUrl()}/aladdin/api/v1/orders", $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['data']['consignment_id'])) {
                $consignmentId = $result['data']['consignment_id'];
                $status = $result['data']['order_status'] ?? 'Pending';

                $order->update([
                    'courier_name' => 'pathao',
                    'courier_tracking_code' => $consignmentId,
                    'courier_consignment_id' => $consignmentId,
                    'courier_status' => strtolower($status),
                    'courier_dispatched_at' => now(),
                    'courier_response' => $result,
                    'order_status' => ($order->order_status === 'pending') ? 'shipped' : $order->order_status,
                ]);

                return [
                    'success' => true,
                    'tracking_code' => $consignmentId,
                    'consignment_id' => $consignmentId,
                    'status' => $status,
                    'message' => "Order dispatched to Pathao successfully! Consignment ID: {$consignmentId}",
                ];
            }

            $errorMessage = $result['message'] ?? 'Pathao returned an error.';
            if (isset($result['errors'])) {
                $errors = is_array($result['errors']) ? implode(', ', array_map(fn($v) => is_array($v) ? implode(' ', $v) : $v, $result['errors'])) : $result['errors'];
                $errorMessage .= " ({$errors})";
            }

            Log::error('Pathao Dispatch Error', ['order' => $order->id, 'response' => $result]);

            return [
                'success' => false,
                'message' => "Pathao Error: {$errorMessage}",
            ];
        } catch (\Exception $e) {
            Log::error('Pathao Exception: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => "Connection Exception: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Check order tracking status
     */
    public function checkStatus(Order $order): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Pathao credentials missing.'];
        }

        $token = $this->getAccessToken();
        if (!$token) {
            return ['success' => false, 'message' => 'Pathao authentication failed.'];
        }

        $consignmentId = $order->courier_consignment_id;
        if (!$consignmentId) {
            return ['success' => false, 'message' => 'Order has no Pathao consignment ID.'];
        }

        try {
            $response = Http::withToken($token)
                ->timeout(12)
                ->get("{$this->getBaseUrl()}/aladdin/api/v1/orders/{$consignmentId}");

            $result = $response->json();

            if ($response->successful() && isset($result['data']['order_status'])) {
                $status = strtolower($result['data']['order_status']);
                $order->update([
                    'courier_status' => $status,
                    'courier_response' => array_merge($order->courier_response ?? [], ['latest_status' => $result]),
                ]);

                if ($status === 'delivered' && $order->order_status !== 'delivered') {
                    $order->update(['order_status' => 'delivered', 'payment_status' => 'paid']);
                }

                return [
                    'success' => true,
                    'status' => $status,
                    'data' => $result['data'],
                    'message' => "Pathao Status: " . ucfirst(str_replace('_', ' ', $status)),
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Could not fetch Pathao order status.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Status Check Exception: {$e->getMessage()}",
            ];
        }
    }
}
