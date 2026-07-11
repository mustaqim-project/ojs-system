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
        $article->load(['journal', 'issue', 'author', 'reviews.reviewer', 'payment', 'assignedEditor']);
        $issues = Issue::where('journal_id', $article->journal_id)
            ->where('status', 'published')
            ->orderByDesc('year')
            ->get();

        return view('admin.articles.show', compact('article', 'issues'));
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
