<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Stand;
use Illuminate\View\View;

class AdminDashboardController extends Controller
{
    /**
     * Display the main admin dashboard overview.
     */
    public function index(): View
    {
        $today = now()->startOfDay();

        // Financial KPIs
        $totalRevenue = (int) Order::whereIn('order_status', ['confirmed', 'paid', 'preparing', 'ready_for_pickup', 'completed'])
            ->sum('total_amount');

        $todayRevenue = (int) Order::whereIn('order_status', ['confirmed', 'paid', 'preparing', 'ready_for_pickup', 'completed'])
            ->where('created_at', '>=', $today)
            ->sum('total_amount');

        // Order counts
        $totalOrders = Order::count();
        $todayOrders = Order::where('created_at', '>=', $today)->count();
        $pendingOrders = Order::whereIn('order_status', ['pending_payment', 'confirmed', 'preparing'])->count();
        $completedOrders = Order::where('order_status', 'completed')->count();

        // Operational stats
        $totalMenus = Menu::count();
        $availableMenus = Menu::where('is_available', true)->count();
        $totalStands = Stand::count();
        $openStands = Stand::where('is_open', true)->count();
        $totalCategories = Category::count();

        // Recent 10 orders with full relationships
        $recentOrders = Order::with(['items.stand', 'payments', 'user'])
            ->latest()
            ->take(8)
            ->get();

        // Stand performance (revenue & order items count)
        $stands = Stand::withCount('menus')
            ->get()
            ->map(function ($stand) {
                $revenue = (int) $stand->orderItems()
                    ->whereHas('order', function ($q) {
                        $q->whereIn('order_status', ['confirmed', 'paid', 'preparing', 'ready_for_pickup', 'completed']);
                    })
                    ->sum('subtotal');

                $stand->total_revenue = $revenue;

                return $stand;
            })
            ->sortByDesc('total_revenue');

        // Top 5 popular menus
        $topMenus = Menu::with(['stand', 'category'])
            ->withCount(['orderItems as sold_count' => function ($q) {
                $q->whereHas('order', function ($sub) {
                    $sub->whereIn('order_status', ['confirmed', 'paid', 'preparing', 'ready_for_pickup', 'completed']);
                });
            }])
            ->orderByDesc('sold_count')
            ->take(5)
            ->get();

        // Payment gateway breakdown
        $paymentBreakdown = [
            'qris' => Payment::where('payment_method', 'qris')->where('status', 'paid')->sum('amount'),
            'va' => Payment::whereIn('payment_method', ['bca_va', 'bri_va'])->where('status', 'paid')->sum('amount'),
            'cash' => Payment::where('gateway', 'cash')->sum('amount'),
        ];

        return view('admin.dashboard', compact(
            'totalRevenue',
            'todayRevenue',
            'totalOrders',
            'todayOrders',
            'pendingOrders',
            'completedOrders',
            'totalMenus',
            'availableMenus',
            'totalStands',
            'openStands',
            'totalCategories',
            'recentOrders',
            'stands',
            'topMenus',
            'paymentBreakdown'
        ));
    }
}
