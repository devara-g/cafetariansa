<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Stand;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class MidtransPaymentTest extends TestCase
{
    use DatabaseTransactions;

    public function test_checkout_with_qris_initiates_snap_token(): void
    {
        $stand = Stand::factory()->create();
        $category = Category::factory()->create();
        $menu = Menu::factory()->create([
            'stand_id' => $stand->id,
            'category_id' => $category->id,
            'price' => 15000,
            'is_available' => true,
        ]);

        $payload = [
            'customer_name' => 'Fajar Pratama',
            'customer_phone' => '081234567890',
            'customer_type' => 'student',
            'pickup_type' => 'pickup_at_stand',
            'pickup_time_slot' => 'Istirahat 1 (09:45 - 10:15 WIB)',
            'payment_method' => 'qris',
            'items' => [
                [
                    'menu_id' => $menu->id,
                    'quantity' => 2,
                ],
            ],
        ];

        $response = $this->postJson('/api/orders', $payload);

        $response->assertStatus(201)
            ->assertJson([
                'success' => true,
                'data' => [
                    'payment_method' => 'qris',
                    'order_status' => 'pending_payment',
                ],
            ]);

        $this->assertNotEmpty($response->json('data.snap_token'));
        $order = Order::where('order_number', $response->json('data.order_number'))->first();
        $this->assertDatabaseHas('payments', [
            'order_id' => $order->id,
            'payment_method' => 'qris',
            'status' => 'pending',
        ]);
    }

    public function test_midtrans_webhook_settlement_confirms_order(): void
    {
        $order = Order::factory()->create([
            'order_status' => 'pending_payment',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_reference' => 'PAY-TEST-'.uniqid(),
            'gateway' => 'midtrans',
            'payment_method' => 'qris',
            'amount' => $order->total_amount,
            'fee' => 500,
            'status' => 'pending',
            'expires_at' => now()->addHour(),
        ]);

        $serverKey = (string) config('services.midtrans.server_key');
        $validSignature = hash('sha512', $payment->payment_reference.'200'.$payment->amount.$serverKey);

        $webhookPayload = [
            'order_id' => $payment->payment_reference,
            'status_code' => '200',
            'gross_amount' => (string) $payment->amount,
            'transaction_status' => 'settlement',
            'fraud_status' => 'accept',
            'payment_type' => 'qris',
            'transaction_id' => 'TRX-'.uniqid(),
            'signature_key' => $validSignature,
        ];

        $response = $this->postJson('/api/midtrans/notification', $webhookPayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'paid',
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'confirmed',
        ]);
    }

    public function test_midtrans_webhook_expire_cancels_order(): void
    {
        $order = Order::factory()->create([
            'order_status' => 'pending_payment',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_reference' => 'PAY-EXP-'.uniqid(),
            'gateway' => 'midtrans',
            'payment_method' => 'bca_va',
            'amount' => $order->total_amount,
            'fee' => 500,
            'status' => 'pending',
            'expires_at' => now()->addHour(),
        ]);

        $serverKey = (string) config('services.midtrans.server_key');
        $validSignature = hash('sha512', $payment->payment_reference.'200'.$payment->amount.$serverKey);

        $webhookPayload = [
            'order_id' => $payment->payment_reference,
            'status_code' => '200',
            'gross_amount' => (string) $payment->amount,
            'transaction_status' => 'expire',
            'fraud_status' => 'accept',
            'payment_type' => 'bca_va',
            'transaction_id' => 'TRX-'.uniqid(),
            'signature_key' => $validSignature,
        ];

        $response = $this->postJson('/api/midtrans/notification', $webhookPayload);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'status' => 'expired',
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'expired',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'cancelled',
        ]);
    }

    public function test_user_can_check_payment_status(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'SKN-CHK-'.uniqid(),
            'order_status' => 'confirmed',
        ]);

        Payment::create([
            'order_id' => $order->id,
            'payment_reference' => 'PAY-CHK-'.uniqid(),
            'gateway' => 'midtrans',
            'payment_method' => 'qris',
            'amount' => $order->total_amount,
            'fee' => 500,
            'status' => 'paid',
            'paid_at' => now(),
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->getJson("/api/orders/{$order->order_number}/payment-status");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_number' => $order->order_number,
                    'order_status' => 'confirmed',
                    'payment_status' => 'paid',
                    'payment_method' => 'qris',
                ],
            ]);
    }

    public function test_order_can_be_confirmed_via_confirm_payment_endpoint(): void
    {
        $order = Order::factory()->create([
            'order_number' => 'SKN-CONF-'.uniqid(),
            'order_status' => 'pending_payment',
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'payment_reference' => 'PAY-CONF-'.uniqid(),
            'gateway' => 'midtrans',
            'payment_method' => 'qris',
            'amount' => $order->total_amount,
            'fee' => 500,
            'status' => 'pending',
            'expires_at' => now()->addHour(),
        ]);

        $response = $this->postJson("/api/orders/{$order->order_number}/confirm-payment", [
            'transaction_status' => 'settlement',
            'status_code' => '200',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_number' => $order->order_number,
                    'order_status' => 'confirmed',
                    'payment_status' => 'paid',
                ],
            ]);

        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'confirmed',
        ]);
    }
}
