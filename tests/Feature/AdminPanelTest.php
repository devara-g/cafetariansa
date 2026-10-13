<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class AdminPanelTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Unauthenticated guest is redirected to login page.
     */
    public function test_unauthenticated_user_cannot_access_admin_dashboard_and_is_redirected_to_login(): void
    {
        $response = $this->get('/admin/dashboard');

        $response->assertRedirect('/admin/login');
    }

    /**
     * Non-admin user cannot access admin panel.
     */
    public function test_non_admin_user_is_forbidden_from_accessing_admin_dashboard(): void
    {
        $student = User::factory()->create(['role' => 'student']);

        $response = $this->actingAs($student)->get('/admin/dashboard');

        $response->assertStatus(403);
    }

    /**
     * Admin user can access all admin management pages.
     */
    public function test_admin_user_can_access_dashboard_and_all_management_pages(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);

        // Dashboard
        $response = $this->actingAs($admin)->get('/admin/dashboard');
        $response->assertStatus(200);

        // Orders
        $response = $this->actingAs($admin)->get('/admin/orders');
        $response->assertStatus(200);

        // Menus
        $response = $this->actingAs($admin)->get('/admin/menus');
        $response->assertStatus(200);

        // Stands
        $response = $this->actingAs($admin)->get('/admin/stands');
        $response->assertStatus(200);

        // Categories
        $response = $this->actingAs($admin)->get('/admin/categories');
        $response->assertStatus(200);

        // Transactions
        $response = $this->actingAs($admin)->get('/admin/transactions');
        $response->assertStatus(200);

        // Users
        $response = $this->actingAs($admin)->get('/admin/users');
        $response->assertStatus(200);
    }

    /**
     * Admin can update order status.
     */
    public function test_admin_can_update_order_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['order_status' => 'pending_payment']);

        $response = $this->actingAs($admin)->put("/admin/orders/{$order->id}/status", [
            'order_status' => 'confirmed',
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'confirmed',
        ]);
    }

    /**
     * Admin can toggle stand open/close status.
     */
    public function test_admin_can_toggle_stand_open_status(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $stand = Stand::factory()->create(['is_open' => true]);

        $response = $this->actingAs($admin)->post("/admin/stands/{$stand->id}/toggle-open");

        $response->assertRedirect();
        $this->assertDatabaseHas('stands', [
            'id' => $stand->id,
            'is_open' => false,
        ]);
    }

    /**
     * Admin can toggle menu availability.
     */
    public function test_admin_can_toggle_menu_availability(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $stand = Stand::factory()->create();
        $category = Category::factory()->create();
        $menu = Menu::factory()->create([
            'stand_id' => $stand->id,
            'category_id' => $category->id,
            'is_available' => true,
        ]);

        $response = $this->actingAs($admin)->post("/admin/menus/{$menu->id}/toggle-available");

        $response->assertRedirect();
        $this->assertDatabaseHas('menus', [
            'id' => $menu->id,
            'is_available' => false,
        ]);
    }

    /**
     * Admin can mark pending payment as paid.
     */
    public function test_admin_can_mark_payment_as_paid(): void
    {
        $admin = User::factory()->create(['role' => 'admin']);
        $order = Order::factory()->create(['order_status' => 'pending_payment']);
        $payment = Payment::factory()->create([
            'order_id' => $order->id,
            'status' => 'pending',
            'amount' => 15000,
        ]);

        $response = $this->actingAs($admin)->post("/admin/transactions/{$payment->id}/mark-paid");

        $response->assertRedirect();
        $this->assertDatabaseHas('payments', [
            'id' => $payment->id,
            'status' => 'paid',
        ]);
        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'order_status' => 'confirmed',
        ]);
    }

    /**
     * Quick demo login button works.
     */
    public function test_admin_quick_login(): void
    {
        // Ensure admin user exists
        User::firstOrCreate(
            ['email' => 'admin@smkn1ciomas.sch.id'],
            [
                'name' => 'Administrator Kantin',
                'password' => bcrypt('admin123'),
                'role' => 'admin',
            ]
        );

        $response = $this->post('/admin/quick-login');

        $response->assertRedirect('/admin/dashboard');
        $this->assertAuthenticated();
        $this->assertEquals('admin@smkn1ciomas.sch.id', Auth::user()?->email);
    }
}
