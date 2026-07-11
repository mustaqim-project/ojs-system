<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends Controller
{
    public function index(): View
    {
        $groups = [
            'general' => Setting::where('group', 'general')->get(),
            'payment' => Setting::where('group', 'payment')->get(),
            'review'  => Setting::where('group', 'review')->get(),
            'email'   => Setting::where('group', 'email')->get(),
        ];

        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings'          => ['required', 'array'],
            'settings.*.key'    => ['required', 'string'],
            'settings.*.value'  => ['nullable'],
        ]);

        foreach ($request->settings as $item) {
            Setting::set($item['key'], $item['value'] ?? '');
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
