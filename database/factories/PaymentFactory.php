<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Payment>
 */
class PaymentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $amount = fake()->randomElement([15000, 20000, 25000, 30000]);

        return [
            'order_id' => Order::factory(),
            'payment_reference' => 'PAY-'.date('Ymd').'-'.strtoupper(Str::random(8)),
            'gateway_transaction_id' => 'midtrans-trx-'.Str::uuid(),
            'gateway' => 'midtrans',
            'payment_method' => fake()->randomElement(['qris', 'gopay', 'shopeepay', 'bca_va', 'bri_va']),
            'amount' => $amount,
            'fee' => 500,
            'status' => 'paid',
            'snap_token' => Str::random(32),
            'checkout_url' => 'https://app.sandbox.midtrans.com/snap/v2/vtweb/'.Str::random(20),
            'qr_code_url' => null,
            'qr_string' => null,
            'va_number' => fake()->numerify('12345##########'),
            'paid_at' => now(),
            'expires_at' => now()->addDay(),
            'gateway_response' => [
                'transaction_status' => 'settlement',
                'status_message' => 'Success',
            ],
        ];
    }
}
