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
        $institutionId = $request->institution_id;
        if (!$institutionId && $request->filled('affiliation')) {
            $institution = \App\Models\Institution::firstOrCreate(
                ['name' => $request->affiliation],
                [
                    'acronym' => $this->generateAcronym($request->affiliation),
                    'country_code' => 'ID',
                ]
            );
            $institutionId = $institution->id;
        }

        $user = User::create([
            'name'           => $request->name,
            'email'          => $request->email,
            'password'       => Hash::make($request->password),
            'role'           => 'author',
            'affiliation'    => $request->affiliation,
            'institution_id' => $institutionId,
            'phone'          => $request->phone,
            'is_active'      => true,
        ]);

        event(new Registered($user));
        Auth::login($user);

        $route = $user->dashboardRoute();
        return redirect()->route($route)->with('success', 'Registrasi berhasil! Selamat datang, ' . $user->name . '!');
    }

    private function generateAcronym(string $name): ?string
    {
        $words = explode(' ', preg_replace('/[^a-zA-Z\s]/', '', $name));
        $acronym = '';
        foreach ($words as $word) {
            if (!empty($word) && !in_array(strtolower($word), ['of', 'in', 'and', 'dan', 'di', 'ke', 'the'])) {
                $acronym .= strtoupper($word[0]);
            }
        }
        return !empty($acronym) ? $acronym : null;
    }
}
