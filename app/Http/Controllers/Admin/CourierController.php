<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Courier\PathaoService;
use App\Services\Courier\SteadfastService;
use Illuminate\Http\Request;

class CourierController extends Controller
{
    public function __construct(
        protected SteadfastService $steadfast,
        protected PathaoService $pathao
    ) {}

    /**
     * Dispatch an order to Steadfast or Pathao via API
     */
    public function dispatchOrder(Request $request, $id)
    {
        $request->validate([
            'courier' => 'required|in:steadfast,pathao',
            'cod_amount' => 'nullable|numeric|min:0',
            'note' => 'nullable|string|max:500',
            'weight' => 'nullable|numeric|min:0.1|max:20',
        ]);

        $order = Order::with('items')->findOrFail($id);

        if ($request->courier === 'steadfast') {
            $result = $this->steadfast->createOrder($order, [
                'cod_amount' => $request->cod_amount,
                'note' => $request->note,
            ]);
        } else {
            $result = $this->pathao->createOrder($order, [
                'cod_amount' => $request->cod_amount,
                'note' => $request->note,
                'weight' => $request->weight ?? 0.5,
            ]);
        }

        if ($result['success']) {
            \App\Jobs\SendOrderSmsJob::dispatch($order->fresh(), 'order_shipped');

            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Fetch and update real-time courier tracking status
     */
    public function trackOrder($id)
    {
        $order = Order::findOrFail($id);

        if (empty($order->courier_name)) {
            return redirect()->back()->with('error', 'This order has not been dispatched to a courier yet.');
        }

        if ($order->courier_name === 'steadfast') {
            $result = $this->steadfast->checkStatus($order);
        } elseif ($order->courier_name === 'pathao') {
            $result = $this->pathao->checkStatus($order);
        } else {
            return redirect()->back()->with('info', "Courier '{$order->courier_name}' does not support live API polling.");
        }

        if ($result['success']) {
            return redirect()->back()->with('success', $result['message']);
        }

        return redirect()->back()->with('error', $result['message']);
    }

    /**
     * Assign manual courier tracking (e.g. Sundarban, SA Paribahan, etc.)
     */
    public function manualCourier(Request $request, $id)
    {
        $request->validate([
            'courier_name' => 'required|string|max:100',
            'courier_tracking_code' => 'required|string|max:100',
            'courier_status' => 'nullable|string|max:100',
        ]);

        $order = Order::findOrFail($id);
        $order->update([
            'courier_name' => strtolower($request->courier_name),
            'courier_tracking_code' => $request->courier_tracking_code,
            'courier_consignment_id' => $request->courier_tracking_code,
            'courier_status' => $request->courier_status ?: 'shipped',
            'courier_dispatched_at' => now(),
            'order_status' => ($order->order_status === 'pending') ? 'shipped' : $order->order_status,
        ]);

        \App\Jobs\SendOrderSmsJob::dispatch($order->fresh(), 'order_shipped');

        return redirect()->back()->with('success', "Assigned {$request->courier_name} tracking (#{$request->courier_tracking_code}) successfully!");
    }

    /**
     * Check Steadfast account balance
     */
    public function getBalance()
    {
        $res = $this->steadfast->getBalance();
        return response()->json($res);
    }
}
