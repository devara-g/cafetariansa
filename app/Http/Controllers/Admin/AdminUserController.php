<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class AdminUserController extends Controller
{
    /**
     * Display list of users.
     */
    public function index(Request $request): View
    {
        $role = $request->query('role', 'all');
        $search = $request->query('search', '');

        $query = User::latest();

        if ($role !== 'all') {
            $query->where('role', $role);
        }

        if (! empty($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('class_or_division', 'like', "%{$search}%");
            });
        }

        $users = $query->paginate(15)->withQueryString();

        $roleCounts = [
            'all' => User::count(),
            'admin' => User::where('role', 'admin')->count(),
            'stand_owner' => User::where('role', 'stand_owner')->count(),
            'teacher' => User::where('role', 'teacher')->count(),
            'student' => User::where('role', 'student')->count(),
        ];

        return view('admin.users.index', compact('users', 'role', 'search', 'roleCounts'));
    }

    /**
     * Store new user.
     */
    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:student,teacher,staff,stand_owner,admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'class_or_division' => ['nullable', 'string', 'max:60'],
            'nisn_or_nip' => ['nullable', 'string', 'max:30'],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return back()->with('success', "Pengguna '{$user->name}' berhasil didaftarkan!");
    }

    /**
     * Update user role and information.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'unique:users,email,'.$user->id],
            'role' => ['required', 'in:student,teacher,staff,stand_owner,admin'],
            'phone' => ['nullable', 'string', 'max:20'],
            'class_or_division' => ['nullable', 'string', 'max:60'],
            'nisn_or_nip' => ['nullable', 'string', 'max:30'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (! empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return back()->with('success', "Data pengguna '{$user->name}' berhasil diperbarui!");
    }

    /**
     * Delete user.
     */
    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri saat sedang masuk!']);
        }

        $name = $user->name;
        $user->delete();

        return back()->with('success', "Pengguna '{$name}' berhasil dihapus.");
    }
}
