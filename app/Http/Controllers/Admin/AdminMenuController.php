<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Menu;
use App\Models\Stand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminMenuController extends Controller
{
    /**
     * Display list of menus.
     */
    public function index(Request $request): View
    {
        $search = $request->query('search', '');
        $standId = $request->query('stand_id', '');
        $categoryId = $request->query('category_id', '');

        $query = Menu::with(['stand', 'category'])->latest();

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if (! empty($standId)) {
            $query->where('stand_id', $standId);
        }

        if (! empty($categoryId)) {
            $query->where('category_id', $categoryId);
        }

        $menus = $query->paginate(12)->withQueryString();
        $stands = Stand::orderBy('name')->get();
        $categories = Category::orderBy('name')->get();

        return view('admin.menus.index', compact('menus', 'stands', 'categories', 'search', 'standId', 'categoryId'));
    }

    /**
     * Store newly created menu item.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'stand_id' => ['required', 'exists:stands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:500'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'preparation_time_minutes' => ['nullable', 'integer', 'min:1'],
            'is_available' => ['nullable', 'boolean'],
            'is_popular' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(4);
        $validated['is_available'] = $request->boolean('is_available', true);
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_recommended'] = $request->boolean('is_recommended');
        $validated['image'] = $validated['image'] ?: 'assets/images/food-menu-1.png';

        $menu = Menu::create($validated);

        return back()->with('success', "Menu '{$menu->name}' berhasil ditambahkan!");
    }

    /**
     * Update menu item.
     */
    public function update(Request $request, Menu $menu): RedirectResponse
    {
        $validated = $request->validate([
            'stand_id' => ['required', 'exists:stands,id'],
            'category_id' => ['required', 'exists:categories,id'],
            'name' => ['required', 'string', 'max:150'],
            'price' => ['required', 'numeric', 'min:500'],
            'cost_price' => ['nullable', 'numeric', 'min:0'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string', 'max:255'],
            'stock' => ['nullable', 'integer', 'min:0'],
            'preparation_time_minutes' => ['nullable', 'integer', 'min:1'],
            'is_available' => ['nullable', 'boolean'],
            'is_popular' => ['nullable', 'boolean'],
            'is_recommended' => ['nullable', 'boolean'],
        ]);

        $validated['is_available'] = $request->boolean('is_available');
        $validated['is_popular'] = $request->boolean('is_popular');
        $validated['is_recommended'] = $request->boolean('is_recommended');

        $menu->update($validated);

        return back()->with('success', "Menu '{$menu->name}' berhasil diperbarui!");
    }

    /**
     * Delete menu item.
     */
    public function destroy(Menu $menu): RedirectResponse
    {
        $name = $menu->name;
        $menu->delete();

        return back()->with('success', "Menu '{$name}' berhasil dihapus.");
    }

    /**
     * Toggle availability status (Tersedia / Habis).
     */
    public function toggleAvailability(Menu $menu): RedirectResponse|JsonResponse
    {
        $menu->update(['is_available' => ! $menu->is_available]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_available' => $menu->is_available,
                'message' => $menu->is_available ? 'Menu ditandai Tersedia' : 'Menu ditandai Habis',
            ]);
        }

        $statusStr = $menu->is_available ? 'Tersedia' : 'Habis';

        return back()->with('success', "Status menu '{$menu->name}' diubah menjadi: {$statusStr}");
    }

    /**
     * Toggle popular / recommended badge.
     */
    public function toggleBadge(Request $request, Menu $menu, string $badgeType): RedirectResponse|JsonResponse
    {
        if ($badgeType === 'popular') {
            $menu->update(['is_popular' => ! $menu->is_popular]);
        } elseif ($badgeType === 'recommended') {
            $menu->update(['is_recommended' => ! $menu->is_recommended]);
        }

        if ($request->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_popular' => $menu->is_popular,
                'is_recommended' => $menu->is_recommended,
            ]);
        }

        return back()->with('success', "Badge menu '{$menu->name}' berhasil diperbarui.");
    }
}
