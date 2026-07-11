<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\RegisterRequest;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class RegisterController extends Controller
{
    public function show(): View
    {
        return view('auth.register');
    }

    public function store(RegisterRequest $request): RedirectResponse
    {
        $user = User::create([
            'name'        => $request->name,
            'email'       => $request->email,
            'password'    => Hash::make($request->password),
            'role'        => 'author',
            'affiliation' => $request->affiliation,
            'phone'       => $request->phone,
            'is_active'   => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        $route = $user->dashboardRoute();
        return redirect()->route($route)->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '!');
    }
}
