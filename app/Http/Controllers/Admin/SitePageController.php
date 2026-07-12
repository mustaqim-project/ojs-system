<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SitePage;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SitePageController extends Controller
{
    /**
     * Halaman yang bisa dikelola dari panel admin.
     * Urutan ini juga menentukan urutan di listing.
     */
    private const MANAGEABLE_PAGES = [
        'about'             => ['label' => 'About',              'icon' => 'bi-info-circle',      'group' => 'Journal Info'],
        'editorial-team'    => ['label' => 'Editorial Team',     'icon' => 'bi-people',           'group' => 'Journal Info'],
        'reviewer-board'    => ['label' => 'Reviewer Board',     'icon' => 'bi-person-check',     'group' => 'Journal Info'],
        'focus-and-scope'   => ['label' => 'Focus & Scope',      'icon' => 'bi-bullseye',         'group' => 'Journal Info'],
        'author-guidelines' => ['label' => 'Author Guidelines',  'icon' => 'bi-pencil-square',    'group' => 'Authors'],
        'ethics'            => ['label' => 'Publication Ethics', 'icon' => 'bi-shield-check',     'group' => 'Authors'],
        'peer-review'       => ['label' => 'Peer Review Process','icon' => 'bi-clipboard-check',  'group' => 'Authors'],
        'journal-policies'  => ['label' => 'Journal Policies',   'icon' => 'bi-file-earmark-text','group' => 'Authors'],
        'indexing'          => ['label' => 'Indexing',           'icon' => 'bi-database',         'group' => 'Browse'],
        'announcements'     => ['label' => 'Announcements',      'icon' => 'bi-megaphone',        'group' => 'Browse'],
        'call-for-papers'   => ['label' => 'Call for Papers',    'icon' => 'bi-send',             'group' => 'Browse'],
        'contact'           => ['label' => 'Contact',            'icon' => 'bi-envelope',         'group' => 'Info'],
        'privacy-policy'    => ['label' => 'Privacy Policy',     'icon' => 'bi-lock',             'group' => 'Legal'],
        'terms-conditions'  => ['label' => 'Terms & Conditions', 'icon' => 'bi-file-text',        'group' => 'Legal'],
    ];

    public function index(): View
    {
        $defaults = SitePage::defaults();

        $pages = collect(self::MANAGEABLE_PAGES)->map(function ($meta, $slug) use ($defaults) {
            $dbPage  = SitePage::where('slug', $slug)->first();
            $default = $defaults[$slug] ?? [];
            return [
                'slug'     => $slug,
                'label'    => $meta['label'],
                'icon'     => $meta['icon'],
                'group'    => $meta['group'],
                'title'    => $dbPage?->title ?: ($default['title'] ?? ucwords(str_replace('-', ' ', $slug))),
                'is_active'=> $dbPage ? $dbPage->is_active : true,
                'from_db'  => (bool) $dbPage,
                'updated_at' => $dbPage?->updated_at,
            ];
        })->groupBy('group');

        return view('admin.pages.index', compact('pages'));
    }

    public function edit(string $slug): View
    {
        $meta = self::MANAGEABLE_PAGES[$slug] ?? null;
        abort_if(! $meta, 404);

        $defaults = SitePage::defaults();
        $default  = $defaults[$slug] ?? [];

        $dbPage = SitePage::where('slug', $slug)->first();

        // Merge: DB values override defaults, but defaults fill in missing keys
        $page = [
            'slug'             => $slug,
            'label'            => $meta['label'],
            'icon'             => $meta['icon'],
            'title'            => $dbPage?->title ?? $default['title'] ?? '',
            'body'             => $dbPage?->body  ?? $default['body']  ?? '',
            'meta_description' => $dbPage?->meta_description ?? $default['meta_description'] ?? '',
            'extra'            => array_merge($default['extra'] ?? [], $dbPage?->extra ?? []),
            'is_active'        => $dbPage ? $dbPage->is_active : true,
        ];

        return view('admin.pages.edit', compact('page'));
    }

    public function update(Request $request, string $slug): RedirectResponse
    {
        abort_if(! array_key_exists($slug, self::MANAGEABLE_PAGES), 404);

        $validated = $request->validate([
            'title'            => ['required', 'string', 'max:255'],
            'body'             => ['nullable', 'string'],
            'meta_description' => ['nullable', 'string', 'max:500'],
            'is_active'        => ['boolean'],
            'extra'            => ['nullable', 'array'],
            'template_file'    => ['nullable', 'file', 'mimes:doc,docx,pdf,rtf', 'max:10240'],
        ]);

        $extra = $request->input('extra', []);

        if ($slug === 'author-guidelines' && $request->hasFile('template_file')) {
            $file = $request->file('template_file');
            $filename = 'manuscript_template_' . time() . '.' . $file->getClientOriginalExtension();
            // Simpan ke storage/app/public/templates
            $path = $file->storeAs('templates', $filename, 'public');
            $extra['template_url'] = asset('storage/' . $path);
        }

        // Ambil data lama agar extra tidak hilang seluruhnya jika salah satu tidak diupdate
        $dbPage = SitePage::where('slug', $slug)->first();
        $oldExtra = $dbPage ? ($dbPage->extra ?? []) : [];
        $mergedExtra = array_merge($oldExtra, $extra);

        $data = [
            'slug'             => $slug,
            'title'            => $validated['title'],
            'body'             => $validated['body'] ?? '',
            'meta_description' => $validated['meta_description'] ?? '',
            'is_active'        => (bool) ($validated['is_active'] ?? true),
            'extra'            => $mergedExtra,
        ];

        SitePage::updateOrCreate(['slug' => $slug], $data);

        return back()->with('success', "Halaman \"{$data['title']}\" berhasil disimpan!");
    }

    public function resetToDefault(string $slug): RedirectResponse
    {
        abort_if(! array_key_exists($slug, self::MANAGEABLE_PAGES), 404);
        SitePage::where('slug', $slug)->delete();

        return back()->with('success', 'Halaman berhasil direset ke konten default.');
    }
}
