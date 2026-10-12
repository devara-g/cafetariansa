<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Menu;
use App\Models\Stand;
use Illuminate\Contracts\View\View;

class CanteenController extends Controller
{
    /**
     * Display the canteen homepage with dynamic database records.
     */
    public function index(): View
    {
        $categories = Category::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        $stands = Stand::withCount('menus')
            ->where('is_open', true)
            ->get();

        $menus = Menu::with(['stand', 'category'])
            ->available()
            ->latest()
            ->get();

        $popularMenus = Menu::with(['stand', 'category'])
            ->available()
            ->popular()
            ->take(6)
            ->get();

        $recommendedMenus = Menu::with(['stand', 'category'])
            ->available()
            ->where('is_recommended', true)
            ->take(4)
            ->get();

        return view('welcome', [
            'activePage' => 'home',
            'categories' => $categories,
            'stands' => $stands,
            'menus' => $menus,
            'popularMenus' => $popularMenus,
            'recommendedMenus' => $recommendedMenus,
        ]);
    }

    /**
     * Display full menu catalog.
     */
    public function menu(): View
    {
        $categories = Category::where('is_active', true)->orderBy('sort_order')->get();
        $stands = Stand::where('is_open', true)->get();
        $menus = Menu::with(['stand', 'category'])->available()->get();

        return view('welcome', [
            'activePage' => 'search',
            'categories' => $categories,
            'stands' => $stands,
            'menus' => $menus,
            'popularMenus' => $menus->where('is_popular', true)->take(6),
            'recommendedMenus' => $menus->where('is_recommended', true)->take(4),
        ]);
    }
}
