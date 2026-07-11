<?php
// === app/Http/Controllers/Auth/LoginController.php ===

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\View\View;

class LoginController extends Controller
{
    public function show(): View
    {
        return view('auth.login');
    }

    public function store(LoginRequest $request): RedirectResponse
    {
        // Rate Limiting: maks 5 percobaan per 60 detik
        $key = 'login:' . Str::lower($request->email) . '|' . $request->ip();

        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            return back()->withErrors([
                'email' => "Terlalu banyak percobaan login. Coba lagi dalam {$seconds} detik.",
            ])->withInput($request->only('email'));
        }

        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::hit($key, 60);
            return back()->withErrors([
                'email' => 'Email atau password tidak valid.',
            ])->withInput($request->only('email'));
        }

        // Cek user aktif
        if (!auth()->user()->is_active) {
            Auth::logout();
            return back()->withErrors([
                'email' => 'Akun Anda telah dinonaktifkan.',
            ]);
        }

        RateLimiter::clear($key);
        $request->session()->regenerate();

        // Redirect sesuai role
        $route = auth()->user()->dashboardRoute();
        return redirect()->route($route)->with('success', 'Selamat datang, ' . auth()->user()->name . '!');
    }
}
