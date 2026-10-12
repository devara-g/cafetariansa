<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreOrderRequest;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Services\MidtransService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class OrderController extends Controller
{
    public function __construct(private MidtransService $midtransService) {}

    /**
     * Store a newly created order and initiate payment record.
     */
    public function store(StoreOrderRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $menuIds = collect($validated['items'])->pluck('menu_id')->unique();
        $menus = Menu::with('stand')->whereIn('id', $menuIds)->get()->keyBy('id');

        return DB::transaction(function () use ($validated, $menus) {
            $subtotal = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $menu = $menus->get($item['menu_id']);
                if (! $menu || ! $menu->is_available) {
                    abort(422, 'Salah satu menu yang dipilih sedang tidak tersedia.');
                }

                $itemSubtotal = $menu->price * $item['quantity'];
                $subtotal += $itemSubtotal;

                $itemsData[] = [
                    'stand_id' => $menu->stand_id,
                    'menu_id' => $menu->id,
                    'menu_name' => $menu->name,
                    'unit_price' => $menu->price,
                    'quantity' => $item['quantity'],
                    'subtotal' => $itemSubtotal,
                    'notes' => $item['notes'] ?? null,
                    'item_status' => 'pending',
                ];
            }

            $serviceFee = 500; // Biaya platform kantin digital
            $discount = 0;
            $totalAmount = $subtotal + $serviceFee - $discount;

            $orderNumber = 'SKN-'.date('Ymd').'-'.strtoupper(Str::random(4));
            $isCash = $validated['payment_method'] === 'cash';

            $order = Order::create([
                'order_number' => $orderNumber,
                'user_id' => Auth::id(),
                'customer_name' => $validated['customer_name'],
                'customer_phone' => $validated['customer_phone'],
                'customer_type' => $validated['customer_type'],
                'pickup_type' => $validated['pickup_type'],
                'delivery_location' => $validated['delivery_location'] ?? null,
                'pickup_time_slot' => $validated['pickup_time_slot'],
                'notes' => $validated['notes'] ?? null,
                'subtotal' => $subtotal,
                'service_fee' => $serviceFee,
                'discount_amount' => $discount,
                'total_amount' => $totalAmount,
                'order_status' => $isCash ? 'confirmed' : 'pending_payment',
            ]);

            foreach ($itemsData as $data) {
                $order->items()->create($data);
            }

            $paymentRef = 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(8));

            $payment = Payment::create([
                'order_id' => $order->id,
                'payment_reference' => $paymentRef,
                'gateway' => $isCash ? 'cash' : 'midtrans',
                'payment_method' => $validated['payment_method'],
                'amount' => $totalAmount,
                'fee' => $serviceFee,
                'status' => 'pending',
                'expires_at' => now()->addHours(2),
            ]);

            $snapData = ['snap_token' => null, 'redirect_url' => null];
            if (! $isCash) {
                $snapData = $this->midtransService->createSnapTransaction($order, $payment);
            }

            $order->loadMissing('items.stand');

            return response()->json([
                'success' => true,
                'message' => 'Pesanan berhasil dibuat!',
                'data' => [
                    'order_number' => $order->order_number,
                    'customer_name' => $order->customer_name,
                    'pickup_time_slot' => $order->pickup_time_slot,
                    'pickup_type' => $order->pickup_type,
                    'delivery_location' => $order->delivery_location,
                    'total_amount' => $order->total_amount,
                    'formatted_total' => 'Rp '.number_format($order->total_amount, 0, ',', '.'),
                    'subtotal' => $order->subtotal,
                    'formatted_subtotal' => 'Rp '.number_format($order->subtotal, 0, ',', '.'),
                    'service_fee' => $order->service_fee,
                    'formatted_service_fee' => 'Rp '.number_format($order->service_fee, 0, ',', '.'),
                    'notes' => $order->notes,
                    'order_status' => $order->order_status,
                    'payment_method' => $payment->payment_method,
                    'payment_reference' => $payment->payment_reference,
                    'snap_token' => $snapData['snap_token'] ?? null,
                    'redirect_url' => $snapData['redirect_url'] ?? null,
                    'client_key' => $this->midtransService->getClientKey(),
                    'items' => $order->items->map(fn ($item) => [
                        'menu_id' => $item->menu_id,
                        'menu_name' => $item->menu_name,
                        'stand_name' => $item->stand?->name ?? 'Stand Kantin',
                        'unit_price' => (int) $item->unit_price,
                        'quantity' => (int) $item->quantity,
                        'subtotal' => (int) $item->subtotal,
                        'formatted_price' => 'Rp '.number_format($item->unit_price, 0, ',', '.'),
                        'formatted_subtotal' => 'Rp '.number_format($item->subtotal, 0, ',', '.'),
                        'notes' => $item->notes,
                    ]),
                ],
            ], 201);
        });
    }

    /**
     * Mark an order as paid after frontend Midtrans Snap success or manual confirmation.
     */
    public function confirmPayment(Request $request, string $orderNumber): JsonResponse
    {
        $order = Order::with(['items.stand', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        $payment = $order->payments()->latest()->first();

        if ($payment && $payment->status !== 'paid') {
            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
                'gateway_response' => array_merge((array) ($payment->gateway_response ?? []), [
                    'frontend_result' => $request->all(),
                    'confirmed_at' => now()->toIso8601String(),
                ]),
            ]);
        }

        if ($order->order_status !== 'confirmed') {
            $order->update([
                'order_status' => 'confirmed',
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Pembayaran berhasil dikonfirmasi!',
            'data' => [
                'order_number' => $order->order_number,
                'customer_name' => $order->customer_name,
                'pickup_time_slot' => $order->pickup_time_slot,
                'pickup_type' => $order->pickup_type,
                'delivery_location' => $order->delivery_location,
                'total_amount' => $order->total_amount,
                'formatted_total' => 'Rp '.number_format($order->total_amount, 0, ',', '.'),
                'subtotal' => $order->subtotal,
                'formatted_subtotal' => 'Rp '.number_format($order->subtotal, 0, ',', '.'),
                'service_fee' => $order->service_fee,
                'formatted_service_fee' => 'Rp '.number_format($order->service_fee, 0, ',', '.'),
                'notes' => $order->notes,
                'order_status' => $order->order_status,
                'payment_method' => $payment?->payment_method ?? 'qris',
                'payment_status' => $payment?->status ?? 'paid',
                'items' => $order->items->map(fn ($item) => [
                    'menu_id' => $item->menu_id,
                    'menu_name' => $item->menu_name,
                    'stand_name' => $item->stand?->name ?? 'Stand Kantin',
                    'unit_price' => (int) $item->unit_price,
                    'quantity' => (int) $item->quantity,
                    'subtotal' => (int) $item->subtotal,
                    'formatted_price' => 'Rp '.number_format($item->unit_price, 0, ',', '.'),
                    'formatted_subtotal' => 'Rp '.number_format($item->subtotal, 0, ',', '.'),
                    'notes' => $item->notes,
                ]),
            ],
        ]);
    }

    /**
     * Check order status by order number.
     */
    public function show(string $orderNumber): JsonResponse
    {
        $order = Order::with(['items.stand', 'payments'])
            ->where('order_number', $orderNumber)
            ->firstOrFail();

        return response()->json([
            'success' => true,
            'data' => $order,
        ]);
    }

    /**
     * Get live payment status for an order.
     */
    public function paymentStatus(string $orderNumber): JsonResponse
    {
        $order = Order::with('payments')->where('order_number', $orderNumber)->firstOrFail();
        $payment = $order->payments()->latest()->first();

        return response()->json([
            'success' => true,
            'data' => [
                'order_number' => $order->order_number,
                'order_status' => $order->order_status,
                'payment_status' => $payment?->status,
                'payment_method' => $payment?->payment_method,
                'paid_at' => $payment?->paid_at?->toIso8601String(),
            ],
        ]);
    }
}
