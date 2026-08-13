<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;

class JournalController extends Controller
{
    public function index(): View
    {
        $journals = Journal::withTrashed()->with('editor')->withCount('articles')->paginate(15);
        return view('admin.journals.index', compact('journals'));
    }

    public function create(): View
    {
        $editors = User::byRole('editor')->active()->get();
        return view('admin.journals.create', compact('editors'));
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'title'        => ['required', 'string', 'max:255'],
            'abbreviation' => ['nullable', 'string', 'max:20'],
            'description'  => ['nullable', 'string'],
            'issn_print'   => ['nullable', 'string', 'max:20'],
            'issn_online'  => ['nullable', 'string', 'max:20'],
            'publisher'    => ['nullable', 'string', 'max:255'],
            'subject_area' => ['nullable', 'string', 'max:255'],
            'frequency'    => ['required', 'in:monthly,bimonthly,quarterly,semiannual,annual'],
            'editor_id'    => ['nullable', 'exists:users,id'],
            'cover_image'  => ['nullable', 'image', 'max:2048'],
        ]);

        $data         = $request->except('cover_image');
        $data['slug'] = Str::slug($request->title);

        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'journal_' . time() . '_' . Str::random(5) . '.webp';
            $destPath = public_path('upload/' . $filename);

            if (!file_exists(public_path('upload'))) {
                mkdir(public_path('upload'), 0777, true);
            }

            if ($this->convertToWebp($file->getPathname(), $destPath)) {
                $data['cover_image'] = 'upload/' . $filename;
            } else {
                $normalFilename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload'), $normalFilename);
                $data['cover_image'] = 'upload/' . $normalFilename;
            }
        }

        Journal::create($data);
        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil ditambahkan!');
    }

    public function edit(Journal $journal): View
    {
        $editors = User::byRole('editor')->active()->get();
        return view('admin.journals.edit', compact('journal', 'editors'));
    }

    public function update(Request $request, Journal $journal): RedirectResponse
    {
        $request->validate([
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'frequency'   => ['required', 'in:monthly,bimonthly,quarterly,semiannual,annual'],
            'editor_id'   => ['nullable', 'exists:users,id'],
            'is_active'   => ['boolean'],
            'cover_image' => ['nullable', 'image', 'max:2048'],
        ]);

        $data = $request->except('cover_image', '_token', '_method');
        
        if ($request->hasFile('cover_image')) {
            $file = $request->file('cover_image');
            $filename = 'journal_' . time() . '_' . Str::random(5) . '.webp';
            $destPath = public_path('upload/' . $filename);

            if (!file_exists(public_path('upload'))) {
                mkdir(public_path('upload'), 0777, true);
            }

            if ($this->convertToWebp($file->getPathname(), $destPath)) {
                if ($journal->cover_image && file_exists(public_path($journal->cover_image))) {
                    @unlink(public_path($journal->cover_image));
                }
                $data['cover_image'] = 'upload/' . $filename;
            } else {
                $normalFilename = time() . '_' . $file->getClientOriginalName();
                $file->move(public_path('upload'), $normalFilename);
                if ($journal->cover_image && file_exists(public_path($journal->cover_image))) {
                    @unlink(public_path($journal->cover_image));
                }
                $data['cover_image'] = 'upload/' . $normalFilename;
            }
        }

        $journal->update($data);
        return redirect()->route('admin.journals.index')->with('success', 'Jurnal berhasil diupdate!');
    }

    private function convertToWebp(string $sourcePath, string $destinationPath): bool
    {
        $info = getimagesize($sourcePath);
        if (!$info) {
            return false;
        }

        $mime = $info['mime'];
        switch ($mime) {
            case 'image/jpeg':
            case 'image/jpg':
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case 'image/png':
                $image = imagecreatefrompng($sourcePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                    imagealphablending($image, true);
                    imagesavealpha($image, true);
                }
                break;
            case 'image/gif':
                $image = imagecreatefromgif($sourcePath);
                if ($image) {
                    imagepalettetotruecolor($image);
                }
                break;
            case 'image/webp':
                $image = imagecreatefromwebp($sourcePath);
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        $result = imagewebp($image, $destinationPath, 80);
        imagedestroy($image);

        return $result;
    }
}
