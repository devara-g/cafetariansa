<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class AdminTransactionController extends Controller
{
    /**
     * Display list of financial transactions and payment gateway records.
     */
    public function index(Request $request): View
    {
        $status = $request->query('status', 'all');
        $gateway = $request->query('gateway', 'all');
        $search = $request->query('search', '');

        $query = Payment::with(['order.items', 'order.user'])->latest();

        if ($status !== 'all') {
            $query->where('status', $status);
        }

        if ($gateway !== 'all') {
            $query->where('gateway', $gateway);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('payment_reference', 'like', "%{$search}%")
                    ->orWhere('gateway_transaction_id', 'like', "%{$search}%")
                    ->orWhereHas('order', function ($sub) use ($search) {
                        $sub->where('order_number', 'like', "%{$search}%")
                            ->orWhere('customer_name', 'like', "%{$search}%");
                    });
            });
        }

        $payments = $query->paginate(15)->withQueryString();

        // Totals summary
        $totalPaid = Payment::where('status', 'paid')->sum('amount');
        $totalPending = Payment::where('status', 'pending')->sum('amount');
        $totalMidtrans = Payment::where('gateway', 'midtrans')->where('status', 'paid')->sum('amount');
        $totalCash = Payment::where('gateway', 'cash')->where('status', 'paid')->sum('amount');

        return view('admin.transactions.index', compact(
            'payments',
            'status',
            'gateway',
            'search',
            'totalPaid',
            'totalPending',
            'totalMidtrans',
            'totalCash'
        ));
    }

    /**
     * Manually mark a payment as paid.
     */
    public function markPaid(Payment $payment): RedirectResponse
    {
        $payment->update([
            'status' => 'paid',
            'paid_at' => now(),
        ]);

        if ($payment->order && $payment->order->order_status === 'pending_payment') {
            $payment->order->update(['order_status' => 'confirmed']);
        }

        return back()->with('success', "Pembayaran {$payment->payment_reference} berhasil ditandai Lunas!");
    }
}
