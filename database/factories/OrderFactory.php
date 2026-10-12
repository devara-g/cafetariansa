<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $subtotal = fake()->randomElement([10000, 15000, 20000, 25000, 35000]);

        return [
            'order_number' => 'SKN-'.date('Ymd').'-'.strtoupper(Str::random(5)),
            'user_id' => User::factory(),
            'customer_name' => fake()->name(),
            'customer_phone' => fake()->phoneNumber(),
            'customer_type' => fake()->randomElement(['student', 'teacher', 'staff']),
            'pickup_type' => fake()->randomElement(['pickup_at_stand', 'deliver_to_room']),
            'delivery_location' => null,
            'pickup_time_slot' => 'Istirahat 1 (09:45 WIB)',
            'notes' => fake()->optional()->sentence(),
            'subtotal' => $subtotal,
            'service_fee' => 500,
            'discount_amount' => 0,
            'total_amount' => $subtotal + 500,
            'order_status' => fake()->randomElement(['pending_payment', 'paid', 'preparing', 'ready_for_pickup', 'completed']),
        ];
    }
}
