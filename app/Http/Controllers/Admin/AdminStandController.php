<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stand;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class AdminStandController extends Controller
{
    /**
     * Display list of stands.
     */
    public function index(): View
    {
        $stands = Stand::with(['user'])->withCount('menus')->latest()->get();
        $users = User::orderBy('name')->get();

        return view('admin.stands.index', compact('stands', 'users'));
    }

    /**
     * Store newly created stand.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'stand_number' => ['required', 'string', 'max:50'],
            'user_id' => ['nullable', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:25'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'is_open' => ['nullable', 'boolean'],
            'opening_time' => ['nullable', 'string'],
            'closing_time' => ['nullable', 'string'],
        ]);

        $validated['slug'] = Str::slug($validated['name']).'-'.Str::random(3);
        $validated['is_open'] = $request->boolean('is_open', true);

        $stand = Stand::create($validated);

        return back()->with('success', "Stand Kantin '{$stand->name}' berhasil ditambahkan!");
    }

    /**
     * Update stand.
     */
    public function update(Request $request, Stand $stand): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'stand_number' => ['required', 'string', 'max:50'],
            'user_id' => ['nullable', 'exists:users,id'],
            'phone' => ['nullable', 'string', 'max:25'],
            'description' => ['nullable', 'string'],
            'image' => ['nullable', 'string'],
            'is_open' => ['nullable', 'boolean'],
            'opening_time' => ['nullable', 'string'],
            'closing_time' => ['nullable', 'string'],
        ]);

        $validated['is_open'] = $request->boolean('is_open');

        $stand->update($validated);

        return back()->with('success', "Data stand '{$stand->name}' berhasil diperbarui!");
    }

    /**
     * Toggle open/close status.
     */
    public function toggleOpen(Stand $stand): RedirectResponse|JsonResponse
    {
        $stand->update(['is_open' => ! $stand->is_open]);

        if (request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'is_open' => $stand->is_open,
                'message' => $stand->is_open ? 'Stand telah dibuka' : 'Stand telah ditutup',
            ]);
        }

        $statusText = $stand->is_open ? 'BUKA' : 'TUTUP';

        return back()->with('success', "Status Stand '{$stand->name}' diubah menjadi: {$statusText}");
    }

    /**
     * Delete stand.
     */
    public function destroy(Stand $stand): RedirectResponse
    {
        $name = $stand->name;
        $stand->menus()->delete();
        $stand->delete();

        return back()->with('success', "Stand '{$name}' dan menu terkait berhasil dihapus.");
    }
}
