<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SmsLog;
use App\Services\Sms\SmsService;
use Illuminate\Http\Request;

class SmsController extends Controller
{
    public function __construct(
        protected SmsService $smsService
    ) {}

    /**
     * Send a test SMS to any mobile number to verify gateway credentials
     */
    public function testSms(Request $request)
    {
        $request->validate([
            'phone' => 'required|string',
            'message' => 'required|string|max:300',
        ]);

        $result = $this->smsService->send(
            phone: $request->phone,
            message: $request->message,
            orderId: null,
            event: 'test'
        );

        if ($result['success']) {
            return response()->json([
                'success' => true,
                'message' => $result['message'],
                'simulated' => $result['simulated'] ?? false,
                'log' => $result['log'] ?? null,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message'],
        ], 422);
    }

    /**
     * Send or resend SMS for a specific order
     */
    public function sendOrderSms(Request $request, $orderId)
    {
        $request->validate([
            'type' => 'required|in:order_placed,order_shipped,custom',
            'custom_message' => 'nullable|string|max:500',
        ]);

        $order = Order::with('items')->findOrFail($orderId);

        if ($request->type === 'order_placed') {
            $result = $this->smsService->sendOrderPlaced($order);
        } elseif ($request->type === 'order_shipped') {
            $result = $this->smsService->sendOrderShipped($order);
        } else {
            $message = trim((string) $request->custom_message);
            if (empty($message)) {
                return redirect()->back()->with('error', 'Please enter a message to send.');
            }

            // Render placeholders if admin used any
            $rendered = $this->smsService->renderTemplate($message, $order);
            $result = $this->smsService->send($order->customer_phone, $rendered, $order->id, 'custom');
        }

        if ($result['success']) {
            $note = ($result['simulated'] ?? false) ? ' (Simulation Mode)' : '';
            return redirect()->back()->with('success', "SMS sent successfully to {$order->customer_phone}{$note}!");
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Query SMS gateway balance
     */
    public function getBalance()
    {
        $result = $this->smsService->getBalance();
        return response()->json($result);
    }

    /**
     * Fetch recent SMS logs
     */
    public function getLogs(Request $request)
    {
        $query = SmsLog::with('order:id,order_number,customer_name')->latest();

        if ($request->filled('order_id')) {
            $query->where('order_id', $request->order_id);
        }

        if ($request->filled('phone')) {
            $query->where('phone', 'like', "%{$request->phone}%");
        }

        $logs = $query->paginate(20);
        return response()->json($logs);
    }
}
