<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with('items')->latest();

        if ($request->filled('status')) {
            $query->where('order_status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%")
                  ->orWhere('courier_tracking_code', 'like', "%{$search}%")
                  ->orWhere('courier_consignment_id', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(15)->withQueryString();

        $rawCounts = Order::selectRaw('order_status, count(*) as count')
            ->groupBy('order_status')
            ->pluck('count', 'order_status')
            ->toArray();

        $statusCounts = [
            'all' => array_sum($rawCounts),
            'pending' => (int) ($rawCounts['pending'] ?? 0),
            'processing' => (int) ($rawCounts['processing'] ?? 0),
            'shipped' => (int) ($rawCounts['shipped'] ?? 0),
            'delivered' => (int) ($rawCounts['delivered'] ?? 0),
            'cancelled' => (int) ($rawCounts['cancelled'] ?? 0),
        ];

        return \Inertia\Inertia::render('Admin/Orders/Index', [
            'orders' => $orders,
            'statusCounts' => $statusCounts,
            'filters' => $request->only(['status', 'search']),
        ]);
    }

    public function show($id)
    {
        $order = Order::with(['items.product', 'smsLogs'])->findOrFail($id);
        return \Inertia\Inertia::render('Admin/Orders/Show', [
            'order' => $order,
        ]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'order_status' => 'required|in:pending,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed',
            'admin_notes' => 'nullable|string',
        ]);

        $order = Order::findOrFail($id);
        $previousStatus = $order->order_status;

        $order->update([
            'order_status' => $request->order_status,
            'payment_status' => $request->payment_status,
            'admin_notes' => $request->admin_notes,
        ]);

        if ($request->order_status === 'shipped' && $previousStatus !== 'shipped') {
            \App\Jobs\SendOrderSmsJob::dispatch($order, 'order_shipped');
        }

        return redirect()->back()->with('success', "Order #{$order->order_number} status updated successfully!");
    }

    public function destroy($id)
    {
        $order = Order::findOrFail($id);
        $order->items()->delete();
        $order->delete();

        return redirect()->route('admin.orders.index')->with('success', 'Order deleted successfully!');
    }

    public function downloadInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.order_pdf', compact('order'));
        return $pdf->download("Invoice-{$order->order_number}.pdf");
    }

    public function streamInvoice($id)
    {
        $order = Order::with('items')->findOrFail($id);
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('invoices.order_pdf', compact('order'));
        return $pdf->stream("Invoice-{$order->order_number}.pdf");
    }

    public function printInvoice($id)
    {
        return $this->streamInvoice($id);
    }

    public function exportExcel(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');
        $fileName = 'Yanas_Fashion_Orders_' . date('Y_m_d_His') . '.xlsx';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\OrdersExport($status, $search), $fileName);
    }

    public function exportPdf(Request $request)
    {
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $query = Order::with('items')->latest();

        if ($status && $status !== 'all') {
            $query->where('order_status', $status);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhere('customer_name', 'like', "%{$search}%")
                  ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->take(500)->get();

        $totalRevenue = $orders->sum('total_amount');
        $statusCounts = [
            'total' => $orders->count(),
            'pending' => $orders->where('order_status', 'pending')->count(),
            'processing' => $orders->where('order_status', 'processing')->count(),
            'shipped' => $orders->where('order_status', 'shipped')->count(),
            'delivered' => $orders->where('order_status', 'delivered')->count(),
            'cancelled' => $orders->where('order_status', 'cancelled')->count(),
        ];

        $orientation = $request->get('orientation', 'portrait');
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.orders.pdf_report', compact('orders', 'status', 'search', 'totalRevenue', 'statusCounts'))
            ->setPaper('a4', $orientation);

        $fileName = 'Yanas_Fashion_Orders_Report_' . ($status !== 'all' ? ucfirst($status) . '_' : '') . date('Y_m_d_His') . '.pdf';

        if ($request->has('download')) {
            return $pdf->download($fileName);
        }

        return $pdf->stream($fileName);
    }

    public function exportCourierCsv(Request $request)
    {
        $status = $request->get('status', 'pending');
        $fileName = 'Steadfast_Courier_Bulk_' . date('Y_m_d_His') . '.csv';
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\CourierBulkExport($status), $fileName, \Maatwebsite\Excel\Excel::CSV);
    }
}

