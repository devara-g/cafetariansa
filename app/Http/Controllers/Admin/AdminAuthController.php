<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Show admin login form.
     */
    public function showLogin(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()->role === 'admin') {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Handle admin login attempt.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();

            if (Auth::user()->role !== 'admin') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withErrors([
                    'email' => 'Akun ini tidak memiliki hak akses Administrator Kantin.',
                ]);
            }

            return redirect()->intended(route('admin.dashboard'))
                ->with('success', 'Selamat datang kembali, '.Auth::user()->name.'!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi tidak cocok dengan data kami.',
        ])->onlyInput('email');
    }

    /**
     * 1-Click Demo Login for quick testing.
     */
    public function quickLogin(Request $request): RedirectResponse
    {
        $admin = User::where('role', 'admin')->first();

        if (! $admin) {
            $admin = User::firstOrCreate(
                ['email' => 'admin@smkn1ciomas.sch.id'],
                [
                    'name' => 'Admin Kantin SMKN 1 Ciomas',
                    'password' => bcrypt('admin123'),
                    'role' => 'admin',
                    'phone' => '081234567890',
                ]
            );
        }

        Auth::login($admin, true);
        $request->session()->regenerate();

        return redirect()->route('admin.dashboard')
            ->with('success', 'Berhasil masuk secara instan sebagai Administrator Kantin!');
    }

    /**
     * Log the admin out.
     */
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')
            ->with('info', 'Anda telah berhasil keluar dari Panel Admin.');
    }
}
