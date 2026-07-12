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
        // Branding / General Settings
        Setting::firstOrCreate(
            ['key' => 'site_logo'],
            [
                'group' => 'general',
                'label' => 'Site Logo',
                'type' => 'image',
                'description' => 'Upload logo website (resolusi yang disarankan: 200x60px).',
                'value' => ''
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'site_favicon'],
            [
                'group' => 'general',
                'label' => 'Site Favicon',
                'type' => 'image',
                'description' => 'Upload favicon (resolusi yang disarankan: 32x32px atau 64x64px, .png/.ico).',
                'value' => ''
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'seo_meta_description'],
            [
                'group' => 'general',
                'label' => 'Global Meta Description',
                'type' => 'textarea',
                'description' => 'Deskripsi global website untuk keperluan SEO (akan digunakan jika halaman tidak memiliki deskripsi spesifik).',
                'value' => 'An internationally recognized open-access scholarly publishing platform.'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'seo_meta_keywords'],
            [
                'group' => 'general',
                'label' => 'Global Meta Keywords',
                'type' => 'text',
                'description' => 'Kata kunci (keywords) global dipisahkan dengan koma (contoh: journal, research, science).',
                'value' => 'journal, publication, research, open access'
            ]
        );

        // Pastikan setting legal ada untuk Privacy Policy & Terms Conditions
        Setting::firstOrCreate(
            ['key' => 'privacy_policy'],
            [
                'group' => 'legal',
                'label' => 'Privacy Policy Content',
                'type' => 'longtext',
                'description' => 'Isi Privacy Policy (mendukung tag HTML).',
                'value' => '<h5>1. Introduction</h5><p>The names and email addresses entered in this journal site will be used exclusively for the stated purposes of this journal and will not be made available for any other purpose or to any other party.</p>'
            ]
        );

        Setting::firstOrCreate(
            ['key' => 'terms_conditions'],
            [
                'group' => 'legal',
                'label' => 'Terms & Conditions Content',
                'type' => 'longtext',
                'description' => 'Isi Terms & Conditions (mendukung tag HTML).',
                'value' => '<h5>1. Acceptance of Terms</h5><p>By registering, accessing, or using this scholarly publishing platform, you agree to comply with and be bound by these Terms and Conditions.</p>'
            ]
        );

        $settings = Setting::all();
        $groups = $settings->groupBy('group');

        return view('admin.settings.index', compact('groups'));
    }

    public function update(Request $request): RedirectResponse
    {
        $request->validate([
            'settings'          => ['required', 'array'],
            'settings.*.key'    => ['required', 'string'],
            'settings.*.value'  => ['nullable'],
        ]);

        // Process text and boolean settings
        if ($request->has('settings')) {
            foreach ($request->settings as $item) {
                Setting::set($item['key'], $item['value'] ?? '');
            }
        }

        // Process file uploads for image settings
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $key => $file) {
                $path = $file->store('settings', 'public');
                Setting::set($key, 'storage/' . $path);
            }
        }

        return back()->with('success', 'Pengaturan berhasil disimpan!');
    }
}
