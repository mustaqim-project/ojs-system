<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class GoogleController extends Controller
{
    /**
     * Set configuration untuk Socialite Google secara dinamis dari database.
     */
    protected function configureSocialite(): bool
    {
        $clientId     = ApiIntegration::getValue('google', 'client_id');
        $clientSecret = ApiIntegration::getValue('google', 'client_secret');
        $redirectUri  = ApiIntegration::getValue('google', 'redirect_uri') ?? route('auth.google.callback');

        if (!$clientId || !$clientSecret) {
            return false;
        }

        config([
            'services.google' => [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect'      => $redirectUri,
            ]
        ]);

        return true;
    }

    /**
     * Redirect user ke halaman Google authorize.
     */
    public function redirect(): RedirectResponse
    {
        if (!ApiIntegration::isEnabled('google')) {
            return redirect()->route('login')->with('error', 'Integrasi Google sedang dinonaktifkan oleh administrator.');
        }

        if (!$this->configureSocialite()) {
            return redirect()->route('login')->with('error', 'Konfigurasi Google belum lengkap.');
        }

        return Socialite::driver('google')->redirect();
    }

    /**
     * Tangani Callback dari Google.
     */
    public function callback(): RedirectResponse
    {
        if (!$this->configureSocialite()) {
            return redirect()->route('login')->with('error', 'Konfigurasi Google tidak valid.');
        }

        try {
            $googleUser = Socialite::driver('google')->user();
        } catch (\Exception $e) {
            Log::error('Google OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal melakukan autentikasi dengan Google: ' . $e->getMessage());
        }

        $googleId = $googleUser->getId();
        $token    = $googleUser->token;
        $email    = $googleUser->getEmail();
        $name     = $googleUser->getName();

        // Kasus 1: User sudah login dan ingin menghubungkan akun Google
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cek apakah Google iD ini sudah dipakai oleh user lain
            $existing = User::where('google_id', $googleId)->where('id', '!=', $user->id)->first();
            if ($existing) {
                return redirect()->route($user->dashboardRoute())->with('error', 'Akun Google ini sudah dihubungkan ke akun lain.');
            }

            $user->update([
                'google_id'    => $googleId,
                'google_token' => $token,
            ]);

            return redirect()->route($user->dashboardRoute())->with('success', 'Akun Google berhasil dihubungkan!');
        }

        // Kasus 2: User belum login, coba cari user berdasarkan Google iD
        $user = User::where('google_id', $googleId)->first();

        if ($user) {
            // Update token terbaru
            $user->update(['google_token' => $token]);
            
            if (!$user->is_active) {
                return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan.');
            }
            
            Auth::login($user);
            return redirect()->route($user->dashboardRoute())->with('success', 'Berhasil masuk menggunakan Google.');
        }

        // Kasus 3: Cari berdasarkan email
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'google_id'    => $googleId,
                    'google_token' => $token
                ]);
                Auth::login($user);
                return redirect()->route($user->dashboardRoute())->with('success', 'Berhasil masuk dan menghubungkan Google Anda.');
            }
        }

        // Kasus 4: Registrasi User Baru (Khusus Author sesuai request)
        $newUser = User::create([
            'name'         => $name ?: 'Google User',
            'email'        => $email,
            'password'     => Hash::make(Str::random(24)),
            'role'         => 'author',
            'google_id'    => $googleId,
            'google_token' => $token,
            'is_active'    => true,
        ]);

        Auth::login($newUser);

        return redirect()->route('author.dashboard')->with('success', 'Registrasi berhasil via Google!');
    }
}
