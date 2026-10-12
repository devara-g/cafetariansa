<?php

namespace Database\Factories;

use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Stand;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OrderItem>
 */
class OrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $price = fake()->randomElement([10000, 12000, 15000]);
        $qty = fake()->numberBetween(1, 3);

        return [
            'order_id' => Order::factory(),
            'stand_id' => Stand::factory(),
            'menu_id' => Menu::factory(),
            'menu_name' => fake()->words(3, true),
            'unit_price' => $price,
            'quantity' => $qty,
            'subtotal' => $price * $qty,
            'notes' => fake()->optional()->sentence(),
            'item_status' => 'pending',
        ];
    }
}
