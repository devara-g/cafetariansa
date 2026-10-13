<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminOrderController extends Controller
{
    /**
     * Display list of orders with filter tabs and search.
     */
    public function index(Request $request): View
    {
        $statusFilter = $request->query('status', 'all');
        $search = $request->query('search', '');
        $dateFilter = $request->query('date', '');

        $query = Order::with(['items.stand', 'payments', 'user'])->latest();

        if ($statusFilter !== 'all') {
            $query->where('order_status', $statusFilter);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%")
                    ->orWhere('delivery_location', 'like', "%{$search}%");
            });
        }

        if (! empty($dateFilter)) {
            $query->whereDate('created_at', $dateFilter);
        }

        $orders = $query->paginate(15)->withQueryString();

        // Status counts for tabs
        $counts = [
            'all' => Order::count(),
            'pending_payment' => Order::where('order_status', 'pending_payment')->count(),
            'confirmed' => Order::where('order_status', 'confirmed')->count(),
            'preparing' => Order::where('order_status', 'preparing')->count(),
            'ready_for_pickup' => Order::where('order_status', 'ready_for_pickup')->count(),
            'completed' => Order::where('order_status', 'completed')->count(),
            'cancelled' => Order::where('order_status', 'cancelled')->count(),
        ];

        return view('admin.orders.index', compact('orders', 'statusFilter', 'search', 'dateFilter', 'counts'));
    }

    /**
     * Show single order details (JSON for modal / details).
     */
    public function show(Order $order): JsonResponse|View
    {
        $order->load(['items.stand', 'payments', 'user']);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'data' => $order,
            ]);
        }

        return view('admin.orders.show', compact('order'));
    }

    /**
     * Update order status.
     */
    public function updateStatus(Request $request, Order $order): RedirectResponse|JsonResponse
    {
        $validated = $request->validate([
            'order_status' => [
                'required',
                'in:pending_payment,paid,confirmed,preparing,ready_for_pickup,completed,cancelled',
            ],
            'cancelled_reason' => ['nullable', 'string', 'max:255'],
        ]);

        $dataToUpdate = [
            'order_status' => $validated['order_status'],
        ];

        if ($validated['order_status'] === 'completed') {
            $dataToUpdate['completed_at'] = now();
        }

        if ($validated['order_status'] === 'cancelled' && ! empty($validated['cancelled_reason'])) {
            $dataToUpdate['cancelled_reason'] = $validated['cancelled_reason'];
        }

        // If marked paid/confirmed, also update pending payment record if exists
        if (in_array($validated['order_status'], ['paid', 'confirmed', 'completed'], true)) {
            $payment = $order->payments()->latest()->first();
            if ($payment && $payment->status === 'pending') {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);
            }
        }

        $order->update($dataToUpdate);

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Status pesanan berhasil diperbarui!',
                'data' => $order,
            ]);
        }

        return back()->with('success', "Status pesanan {$order->order_number} berhasil diubah menjadi: ".ucwords(str_replace('_', ' ', $validated['order_status'])));
    }

    /**
     * Print receipt view for thermal or A4 printing.
     */
    public function printReceipt(Order $order): View
    {
        $order->load(['items.stand', 'payments', 'user']);

        return view('admin.orders.print', compact('order'));
    }

    /**
     * Delete an order.
     */
    public function destroy(Order $order): RedirectResponse
    {
        $orderNumber = $order->order_number;
        $order->items()->delete();
        $order->payments()->delete();
        $order->delete();

        return back()->with('success', "Pesanan {$orderNumber} berhasil dihapus dari sistem.");
    }
}
