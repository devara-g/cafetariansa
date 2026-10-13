<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Stand;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class OrderCheckoutTest extends TestCase
{
    use DatabaseTransactions;

    public function test_user_can_place_an_order_successfully(): void
    {
        $stand = Stand::factory()->create();
        $category = Category::factory()->create();
        $menu = Menu::factory()->create([
            'stand_id' => $stand->id,
            'category_id' => $category->id,
            'price' => 12000,
            'is_available' => true,
        ]);

        $payload = [
            'customer_name' => 'Fajar Pratama',
            'customer_phone' => '081234567890',
            'customer_type' => 'student',
            'pickup_type' => 'pickup_at_stand',
            'pickup_time_slot' => 'Istirahat 1 (09:45 - 10:15 WIB)',
            'payment_method' => 'qris',
            'notes' => 'Tolong sambal dipisah',
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
                    'customer_name' => 'Fajar Pratama',
                    'pickup_type' => 'pickup_at_stand',
                    'total_amount' => 24500, // (12000 * 2) + 500
                    'payment_method' => 'qris',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'customer_name' => 'Fajar Pratama',
            'total_amount' => 24500,
        ]);

        $this->assertDatabaseHas('order_items', [
            'menu_id' => $menu->id,
            'quantity' => 2,
            'unit_price' => 12000,
            'subtotal' => 24000,
        ]);

        $this->assertDatabaseHas('payments', [
            'payment_method' => 'qris',
            'amount' => 24500,
            'status' => 'pending',
        ]);
    }

    public function test_order_fails_when_required_fields_are_missing(): void
    {
        $response = $this->postJson('/api/orders', []);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['customer_name', 'customer_phone', 'items']);
    }

    public function test_user_can_view_order_status(): void
    {
        $testOrderNumber = 'SKN-TEST-'.uniqid();
        $order = Order::factory()->create([
            'order_number' => $testOrderNumber,
        ]);

        $response = $this->getJson("/api/orders/{$order->order_number}");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_number' => $testOrderNumber,
                ],
            ]);
    }

    public function test_unpaid_order_can_be_cancelled(): void
    {
        $order = Order::factory()->create([
            'order_status' => 'pending_payment',
        ]);

        $response = $this->postJson("/api/orders/{$order->order_number}/cancel");

        $response->assertStatus(200)
            ->assertJson([
                'success' => true,
                'data' => [
                    'order_number' => $order->order_number,
                    'order_status' => 'cancelled',
                ],
            ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'cancelled',
        ]);
    }
}
