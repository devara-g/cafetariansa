<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\PaymentLog;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use InvalidArgumentException;

class MidtransService
{
    private string $serverKey;

    private string $clientKey;

    private bool $isProduction;

    private string $snapUrl;

    private string $apiUrl;

    public function __construct()
    {
        $this->serverKey = (string) config('services.midtrans.server_key');
        $this->clientKey = (string) config('services.midtrans.client_key');
        $this->isProduction = (bool) config('services.midtrans.is_production');
        $this->snapUrl = (string) config('services.midtrans.snap_url');
        $this->apiUrl = (string) config('services.midtrans.api_url');
    }

    /**
     * Create Midtrans Snap transaction token and redirect URL.
     *
     * @return array{snap_token: ?string, redirect_url: ?string, is_mock?: bool}
     */
    public function createSnapTransaction(Order $order, Payment $payment): array
    {
        $items = [];
        $order->loadMissing('items');

        foreach ($order->items as $item) {
            $items[] = [
                'id' => (string) $item->menu_id,
                'price' => (int) $item->unit_price,
                'quantity' => (int) $item->quantity,
                'name' => mb_substr((string) $item->menu_name, 0, 50),
            ];
        }

        if ($order->service_fee > 0) {
            $items[] = [
                'id' => 'FEE-CANTEEN',
                'price' => (int) $order->service_fee,
                'quantity' => 1,
                'name' => 'Biaya Layanan Kantin',
            ];
        }

        $enabledPayments = match ($payment->payment_method) {
            'qris' => ['gopay', 'shopeepay', 'qris', 'other_qris'],
            'bca_va' => ['bca_va'],
            'bri_va' => ['bri_va'],
            default => ['gopay', 'shopeepay', 'qris', 'bca_va', 'bri_va'],
        };

        $params = [
            'transaction_details' => [
                'order_id' => $payment->payment_reference,
                'gross_amount' => (int) $payment->amount,
            ],
            'customer_details' => [
                'first_name' => $order->customer_name,
                'phone' => $order->customer_phone,
            ],
            'item_details' => $items,
            'enabled_payments' => $enabledPayments,
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s O'),
                'unit' => 'minute',
                'duration' => 60,
            ],
        ];

        // If server key is a placeholder or empty, create a local mock/test token
        if (empty($this->serverKey) || str_starts_with($this->serverKey, 'SB-Mid-server-your-server-key')) {
            $mockToken = 'SNAP-MOCK-'.bin2hex(random_bytes(16));
            $mockUrl = "https://app.sandbox.midtrans.com/snap/v2/vtweb/{$mockToken}";

            $payment->update([
                'snap_token' => $mockToken,
                'checkout_url' => $mockUrl,
                'status' => 'pending',
                'gateway_response' => ['mock' => true, 'params' => $params],
            ]);

            PaymentLog::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'gateway' => 'midtrans',
                'event' => 'snap_mock_generated',
                'status' => 'pending',
                'payload' => ['mock_token' => $mockToken],
                'ip_address' => request()->ip(),
            ]);

            return [
                'snap_token' => $mockToken,
                'redirect_url' => $mockUrl,
                'is_mock' => true,
            ];
        }

        try {
            /** @var Response $response */
            $response = Http::withBasicAuth($this->serverKey, '')
                ->acceptJson()
                ->asJson()
                ->timeout(15)
                ->post($this->snapUrl, $params);

            if ($response->successful()) {
                $data = $response->json();
                $snapToken = $data['token'] ?? null;
                $redirectUrl = $data['redirect_url'] ?? null;

                $payment->update([
                    'snap_token' => $snapToken,
                    'checkout_url' => $redirectUrl,
                    'gateway_response' => $data,
                ]);

                PaymentLog::create([
                    'payment_id' => $payment->id,
                    'order_id' => $order->id,
                    'gateway' => 'midtrans',
                    'event' => 'snap_token_created',
                    'status' => 'pending',
                    'payload' => $data,
                    'ip_address' => request()->ip(),
                ]);

                return [
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                    'is_mock' => false,
                ];
            }

            Log::error('Midtrans Snap Error: '.$response->body());

            PaymentLog::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'gateway' => 'midtrans',
                'event' => 'snap_token_failed',
                'status' => 'failed',
                'payload' => ['response' => $response->json() ?? $response->body()],
                'ip_address' => request()->ip(),
            ]);

            return [
                'snap_token' => null,
                'redirect_url' => null,
                'is_mock' => false,
            ];
        } catch (\Throwable $e) {
            Log::error('Midtrans Exception: '.$e->getMessage());

            PaymentLog::create([
                'payment_id' => $payment->id,
                'order_id' => $order->id,
                'gateway' => 'midtrans',
                'event' => 'snap_token_exception',
                'status' => 'failed',
                'payload' => ['error' => $e->getMessage()],
                'ip_address' => request()->ip(),
            ]);

            return [
                'snap_token' => null,
                'redirect_url' => null,
                'is_mock' => false,
            ];
        }
    }

    /**
     * Verify webhook signature hash.
     */
    public function verifySignature(string $orderId, string $statusCode, string $grossAmount, string $signatureKey): bool
    {
        $input = $orderId.$statusCode.$grossAmount.$this->serverKey;
        $expected = hash('sha512', $input);

        return hash_equals($expected, $signatureKey);
    }

    /**
     * Handle incoming notification webhook from Midtrans.
     *
     * @param  array<string, mixed>  $payload
     * @return array{status: string, payment: Payment, order: Order}
     */
    public function handleNotification(array $payload): array
    {
        $orderId = (string) ($payload['order_id'] ?? '');
        $statusCode = (string) ($payload['status_code'] ?? '');
        $grossAmount = (string) ($payload['gross_amount'] ?? '');
        $signatureKey = (string) ($payload['signature_key'] ?? '');
        $transactionStatus = (string) ($payload['transaction_status'] ?? '');
        $fraudStatus = (string) ($payload['fraud_status'] ?? 'accept');
        $transactionId = (string) ($payload['transaction_id'] ?? '');

        // Verify signature if server key is configured
        if (! empty($this->serverKey) && ! str_starts_with($this->serverKey, 'SB-Mid-server-your-server-key')) {
            if (! $this->verifySignature($orderId, $statusCode, $grossAmount, $signatureKey)) {
                throw new InvalidArgumentException('Invalid Midtrans signature key.');
            }
        }

        $payment = Payment::with('order')->where('payment_reference', $orderId)->firstOrFail();
        $order = $payment->order;

        $newPaymentStatus = match ($transactionStatus) {
            'capture' => ($fraudStatus === 'challenge') ? 'challenge' : 'paid',
            'settlement' => 'paid',
            'pending' => 'pending',
            'deny' => 'failed',
            'expire' => 'expired',
            'cancel' => 'cancelled',
            default => $transactionStatus,
        };

        // Extract VA numbers or QR strings if present
        $vaNumber = null;
        if (! empty($payload['va_numbers'][0]['va_number'])) {
            $vaNumber = $payload['va_numbers'][0]['va_number'];
        }

        $payment->update([
            'status' => $newPaymentStatus,
            'gateway_transaction_id' => $transactionId ?: $payment->gateway_transaction_id,
            'paid_at' => ($newPaymentStatus === 'paid') ? now() : $payment->paid_at,
            'va_number' => $vaNumber ?: $payment->va_number,
            'gateway_response' => $payload,
        ]);

        // Synchronize Order status
        if ($newPaymentStatus === 'paid') {
            $order->update(['order_status' => 'confirmed']);
        } elseif (in_array($newPaymentStatus, ['failed', 'expired', 'cancelled'], true)) {
            $order->update(['order_status' => 'cancelled']);
        }

        PaymentLog::create([
            'payment_id' => $payment->id,
            'order_id' => $order->id,
            'gateway' => 'midtrans',
            'event' => 'notification_'.$transactionStatus,
            'status' => $newPaymentStatus,
            'payload' => $payload,
            'ip_address' => request()->ip(),
        ]);

        return [
            'status' => $newPaymentStatus,
            'payment' => $payment,
            'order' => $order,
        ];
    }

    /**
     * Query Midtrans API for the current transaction status.
     *
     * @return array<string, mixed>
     */
    public function checkTransactionStatus(string $paymentReference): array
    {
        if (empty($this->serverKey) || str_starts_with($this->serverKey, 'SB-Mid-server-your-server-key')) {
            return ['status' => 'mock_mode'];
        }

        $url = "{$this->apiUrl}/v2/{$paymentReference}/status";
        $response = Http::withBasicAuth($this->serverKey, '')
            ->acceptJson()
            ->timeout(10)
            ->get($url);

        return $response->json() ?? [];
    }

    public function getClientKey(): string
    {
        return $this->clientKey;
    }
}
