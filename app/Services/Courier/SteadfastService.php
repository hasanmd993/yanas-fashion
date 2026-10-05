<?php

namespace App\Services\Courier;

use App\Models\Order;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SteadfastService
{
    protected string $baseUrl = 'https://portal.steadfast.courier/api/v1';

    public function isConfigured(): bool
    {
        $apiKey = Setting::get('steadfast_api_key');
        $secretKey = Setting::get('steadfast_secret_key');
        return !empty($apiKey) && !empty($secretKey);
    }

    protected function getHeaders(): array
    {
        return [
            'Api-Key' => Setting::get('steadfast_api_key', ''),
            'Secret-Key' => Setting::get('steadfast_secret_key', ''),
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];
    }

    /**
     * Dispatch an order to Steadfast Courier
     */
    public function createOrder(Order $order, array $custom = []): array
    {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'message' => 'Steadfast API Key or Secret Key is not configured. Please enter them in Store Settings.',
            ];
        }

        // Clean phone number (Steadfast expects 11 digit format like 017XXXXXXXX)
        $phone = preg_replace('/[^0-9]/', '', $order->customer_phone);
        if (str_starts_with($phone, '880')) {
            $phone = substr($phone, 2);
        }

        // Calculate COD amount: if already paid (e.g. bKash), COD is 0
        $codAmount = ($order->payment_status === 'paid')
            ? 0
            : (float)($custom['cod_amount'] ?? $order->total_amount);

        $payload = [
            'invoice' => $order->order_number,
            'recipient_name' => $order->customer_name,
            'recipient_phone' => $phone,
            'recipient_address' => $order->customer_address,
            'cod_amount' => $codAmount,
            'note' => $custom['note'] ?? ($order->customer_note ?: 'Yanas Fashion Luxury Apparel'),
        ];

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(15)
                ->post("{$this->baseUrl}/create_order", $payload);

            $result = $response->json();

            if ($response->successful() && isset($result['status']) && $result['status'] == 200 && isset($result['consignment'])) {
                $consignment = $result['consignment'];
                $trackingCode = $consignment['tracking_code'] ?? null;
                $consignmentId = $consignment['consignment_id'] ?? null;
                $status = $consignment['status'] ?? 'in_review';

                $order->update([
                    'courier_name' => 'steadfast',
                    'courier_tracking_code' => $trackingCode,
                    'courier_consignment_id' => $consignmentId,
                    'courier_status' => $status,
                    'courier_dispatched_at' => now(),
                    'courier_response' => $result,
                    'order_status' => ($order->order_status === 'pending') ? 'shipped' : $order->order_status,
                ]);

                return [
                    'success' => true,
                    'tracking_code' => $trackingCode,
                    'consignment_id' => $consignmentId,
                    'status' => $status,
                    'message' => "Order dispatched to Steadfast successfully! Tracking: {$trackingCode}",
                ];
            }

            $errorMessage = $result['message'] ?? $result['errors'] ?? 'Steadfast API returned an unhandled response.';
            if (is_array($errorMessage)) {
                $errorMessage = implode(', ', array_map(fn($v) => is_array($v) ? implode(' ', $v) : $v, $errorMessage));
            }

            Log::error('Steadfast Dispatch Failed', ['order' => $order->id, 'response' => $result]);

            return [
                'success' => false,
                'message' => "Steadfast Error: {$errorMessage}",
            ];
        } catch (\Exception $e) {
            Log::error('Steadfast Exception: ' . $e->getMessage(), ['order' => $order->id]);
            return [
                'success' => false,
                'message' => "Connection Exception: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Check real-time tracking status by tracking code or consignment ID
     */
    public function checkStatus(Order $order): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Steadfast credentials missing.'];
        }

        $code = $order->courier_tracking_code;
        $cid = $order->courier_consignment_id;

        if (!$code && !$cid) {
            return ['success' => false, 'message' => 'Order has no Steadfast tracking code.'];
        }

        try {
            $endpoint = $code
                ? "{$this->baseUrl}/status_by_trackingcode/{$code}"
                : "{$this->baseUrl}/status_by_cid/{$cid}";

            $response = Http::withHeaders($this->getHeaders())
                ->timeout(12)
                ->get($endpoint);

            $result = $response->json();

            if ($response->successful() && isset($result['delivery_status'])) {
                $status = $result['delivery_status'];
                $order->update([
                    'courier_status' => $status,
                    'courier_response' => array_merge($order->courier_response ?? [], ['latest_status' => $result]),
                ]);

                // Auto-sync order status if courier status indicates delivered or cancelled
                if ($status === 'delivered' && $order->order_status !== 'delivered') {
                    $order->update(['order_status' => 'delivered', 'payment_status' => 'paid']);
                } elseif ($status === 'cancelled' && $order->order_status !== 'cancelled') {
                    $order->update(['order_status' => 'cancelled']);
                }

                return [
                    'success' => true,
                    'status' => $status,
                    'data' => $result,
                    'message' => "Latest Steadfast Status: " . ucfirst(str_replace('_', ' ', $status)),
                ];
            }

            return [
                'success' => false,
                'message' => $result['message'] ?? 'Could not fetch status from Steadfast.',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => "Status Check Exception: {$e->getMessage()}",
            ];
        }
    }

    /**
     * Get merchant current account balance
     */
    public function getBalance(): array
    {
        if (!$this->isConfigured()) {
            return ['success' => false, 'message' => 'Steadfast is not configured.'];
        }

        try {
            $response = Http::withHeaders($this->getHeaders())
                ->timeout(10)
                ->get("{$this->baseUrl}/get_balance");

            $result = $response->json();

            if ($response->successful() && isset($result['current_balance'])) {
                return [
                    'success' => true,
                    'balance' => $result['current_balance'],
                ];
            }

            return ['success' => false, 'message' => 'Unable to retrieve balance.'];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
