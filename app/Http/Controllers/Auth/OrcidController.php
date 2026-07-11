<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\ApiIntegration;
use App\Models\User;
use App\Services\OrcidService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class OrcidController extends Controller
{
    /**
     * Set configuration untuk Socialite ORCID secara dinamis dari database.
     */
    protected function configureSocialite(): bool
    {
        $clientId     = ApiIntegration::getValue('orcid', 'client_id');
        $clientSecret = ApiIntegration::getValue('orcid', 'client_secret');
        $redirectUri  = ApiIntegration::getValue('orcid', 'redirect_uri') ?? route('auth.orcid.callback');
        $isSandbox    = (bool) ApiIntegration::getValue('orcid', 'sandbox', false);

        if (!$clientId || !$clientSecret) {
            return false;
        }

        // Untuk Sandbox ORCID, URL authorize dan token berbeda
        config([
            'services.orcid' => [
                'client_id'     => $clientId,
                'client_secret' => $clientSecret,
                'redirect'      => $redirectUri,
                // Custom fields supported by socialiteproviders/orcid if any, otherwise dynamic
            ]
        ]);

        return true;
    }

    /**
     * Redirect user ke halaman ORCID authorize.
     */
    public function redirect(): RedirectResponse
    {
        if (!ApiIntegration::isEnabled('orcid')) {
            return redirect()->route('login')->with('error', 'Integrasi ORCID sedang dinonaktifkan oleh administrator.');
        }

        if (!$this->configureSocialite()) {
            return redirect()->route('login')->with('error', 'Konfigurasi ORCID belum lengkap.');
        }

        // Tentukan host auth berdasarkan sandbox
        $isSandbox = (bool) ApiIntegration::getValue('orcid', 'sandbox', false);
        $host = $isSandbox ? 'sandbox.orcid.org' : 'orcid.org';

        // Override URL Socialite agar redirect ke sandbox jika diaktifkan
        $driver = Socialite::driver('orcid');
        
        // Scope /authenticate adalah default untuk collection ORCID iD
        return $driver->scopes(['/authenticate'])->redirect();
    }

    /**
     * Tangani Callback dari ORCID.
     */
    public function callback(OrcidService $orcidService): RedirectResponse
    {
        if (!$this->configureSocialite()) {
            return redirect()->route('login')->with('error', 'Konfigurasi ORCID tidak valid.');
        }

        try {
            $orcidUser = Socialite::driver('orcid')->user();
        } catch (\Exception $e) {
            Log::error('ORCID OAuth Error: ' . $e->getMessage());
            return redirect()->route('login')->with('error', 'Gagal melakukan autentikasi dengan ORCID: ' . $e->getMessage());
        }

        $orcidId    = $orcidUser->id; // Format: 0000-0000-0000-0000
        $token      = $orcidUser->token;
        $orcidUrl   = OrcidService::normalizeOrcidId($orcidId);

        // Kasus 1: User sudah login dan ingin menghubungkan akun ORCID
        if (Auth::check()) {
            $user = Auth::user();
            
            // Cek apakah ORCID iD ini sudah dipakai oleh user lain
            $existing = User::where('orcid', $orcidUrl)->where('id', '!=', $user->id)->first();
            if ($existing) {
                return redirect()->route($user->dashboardRoute())->with('error', 'ORCID iD ini sudah dihubungkan ke akun lain.');
            }

            $user->update([
                'orcid'       => $orcidUrl,
                'orcid_token' => $token,
            ]);

            return redirect()->route($user->dashboardRoute())->with('success', 'Akun ORCID berhasil dihubungkan!');
        }

        // Kasus 2: User belum login, coba cari user berdasarkan ORCID iD
        $user = User::where('orcid', $orcidUrl)->first();

        if ($user) {
            // Update token terbaru
            $user->update(['orcid_token' => $token]);
            
            if (!$user->is_active) {
                return redirect()->route('login')->with('error', 'Akun Anda sedang dinonaktifkan.');
            }
            
            Auth::login($user);
            return redirect()->route($user->dashboardRoute())->with('success', 'Berhasil masuk menggunakan ORCID.');
        }

        // Kasus 3: Cari berdasarkan email jika ORCID menyediakannya (jarang karena alasan privasi)
        $email = $orcidUser->email;
        if ($email) {
            $user = User::where('email', $email)->first();
            if ($user) {
                $user->update([
                    'orcid'       => $orcidUrl,
                    'orcid_token' => $token
                ]);
                Auth::login($user);
                return redirect()->route($user->dashboardRoute())->with('success', 'Berhasil masuk dan menghubungkan ORCID Anda.');
            }
        }

        // Kasus 4: Registrasi User Baru (Role: Author) dengan prefill dari ORCID
        try {
            $profile = $orcidService->parseSummaryProfile($orcidId, $token);
        } catch (\Exception $e) {
            $profile = [];
        }

        // Prefill values
        $name        = $profile['name'] ?? ($orcidUser->name ?: 'ORCID User');
        $affiliation = $profile['affiliation'] ?? null;
        $bio         = $profile['bio'] ?? null;

        // Buat user baru
        $newUser = User::create([
            'name'        => $name,
            'email'       => $email ?: 'orcid_' . Str::random(10) . '@ojs-orcid.net', // Dummy email jika tidak ada
            'password'    => Hash::make(Str::random(24)), // Random password secure
            'role'        => 'author',
            'affiliation' => $affiliation,
            'bio'         => $bio,
            'orcid'       => $orcidUrl,
            'orcid_token' => $token,
            'is_active'   => true,
        ]);

        Auth::login($newUser);
        
        // Kirim warning jika email menggunakan dummy
        if (!$email) {
            return redirect()->route('author.dashboard')->with('success', 'Registrasi berhasil via ORCID! Harap perbarui email Anda di pengaturan profil.');
        }

        return redirect()->route('author.dashboard')->with('success', 'Registrasi berhasil via ORCID!');
    }
}
