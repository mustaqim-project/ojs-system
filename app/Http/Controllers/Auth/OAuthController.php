<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class OAuthController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();
            
            // Cek apakah user sudah terdaftar dengan google_id ini
            $user = User::where('google_id', $googleUser->id)->first();
            
            if (!$user) {
                // Jika belum, cek email
                $user = User::where('email', $googleUser->email)->first();
                
                if ($user) {
                    // Update dengan google_id jika email sudah ada
                    $user->update([
                        'google_id' => $googleUser->id,
                    ]);
                } else {
                    // Register user baru
                    $user = User::create([
                        'name' => $googleUser->name,
                        'email' => $googleUser->email,
                        'google_id' => $googleUser->id,
                        'role' => 'author', // Default role for public registration
                        'password' => bcrypt(\Illuminate\Support\Str::random(16)), // Random password
                    ]);
                }
            }

            Auth::login($user);

            return redirect()->intended('/dashboard');

        } catch (\Exception $e) {
            return redirect('/login')->with('error', 'Authentication failed: ' . $e->getMessage());
        }
    }
}
