<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Issue;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    public function index(Request $request): View
    {
        $status   = $request->get('status', '');
        $query    = Article::with(['journal', 'author', 'payment'])->latest();

        if ($status) $query->where('status', $status);

        $articles = $query->paginate(15)->withQueryString();
        return view('admin.articles.index', compact('articles', 'status'));
    }

    public function show(Article $article): View
    {
        $article->load(['journal', 'issue', 'author', 'reviews.reviewer', 'payment', 'assignedEditor', 'galleys']);
        $issues = Issue::where('journal_id', $article->journal_id)
            ->where('status', 'published')
            ->orderByDesc('year')
            ->get();

        return view('admin.articles.show', compact('article', 'issues'));
    }

    public function uploadGalley(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'galley_file' => ['required', 'file', 'mimes:pdf', 'max:25600'],
            'label'       => ['nullable', 'string', 'max:50'],
        ], [
            'galley_file.required' => 'Berkas Galley PDF wajib diunggah.',
            'galley_file.mimes'    => 'Berkas Galley harus berupa file PDF (.pdf).',
            'galley_file.max'      => 'Ukuran berkas Galley maksimal 25 MB.',
        ]);

        try {
            $file = $request->file('galley_file');
            $filename = 'galley_' . $article->id . '_' . \Illuminate\Support\Str::random(8) . '.pdf';

            $targetDir = public_path('uploads/galleys');
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            $file->move($targetDir, $filename);
            $relativePath = 'uploads/galleys/' . $filename;

            \App\Models\ArticleGalley::create([
                'article_id'   => $article->id,
                'label'        => $request->filled('label') ? $request->label : 'PDF',
                'file_path'    => $relativePath,
                'mime_type'    => 'application/pdf',
                'size_bytes'   => file_exists(public_path($relativePath)) ? filesize(public_path($relativePath)) : null,
                'is_published' => true,
            ]);

            return back()->with('success', 'Berkas Galley / Layout PDF berhasil diunggah!');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal mengunggah berkas Galley: ' . $e->getMessage());
        }
    }

    public function deleteGalley(Article $article, \App\Models\ArticleGalley $galley): RedirectResponse
    {
        if ($galley->article_id !== $article->id) {
            abort(403);
        }

        try {
            if ($galley->file_path && file_exists(public_path($galley->file_path))) {
                @unlink(public_path($galley->file_path));
            }
            $galley->delete();
            return back()->with('success', 'Berkas Galley berhasil dihapus.');
        } catch (\Throwable $e) {
            return back()->with('error', 'Gagal menghapus berkas Galley: ' . $e->getMessage());
        }
    }

    public function publish(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'issue_id' => ['required', 'exists:issues,id'],
        ]);

        try {
            $this->articleService->publish($article, $request->issue_id);
            return redirect()->route('admin.articles.show', $article)
                ->with('success', 'Artikel berhasil dipublish!');
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function updateMetadata(Request $request, Article $article): RedirectResponse
    {
        $request->validate([
            'doi'         => ['nullable', 'string', 'max:255'],
            'pages_start' => ['nullable', 'integer', 'min:1'],
            'pages_end'   => ['nullable', 'integer', 'min:1'],
        ]);

        $article->update([
            'doi'         => $request->doi,
            'pages_start' => $request->pages_start,
            'pages_end'   => $request->pages_end,
        ]);

        return redirect()->route('admin.articles.show', $article)
            ->with('success', 'Metadata artikel berhasil diperbarui!');
    }
}
