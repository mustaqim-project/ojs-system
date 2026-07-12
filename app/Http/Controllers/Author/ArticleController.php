<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreArticleRequest;
use App\Http\Requests\UploadRevisionRequest;
use App\Models\Article;
use App\Models\Journal;
use App\Services\ArticleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function __construct(private ArticleService $articleService) {}

    public function index(): View
    {
        $articles = Article::forAuthor(auth()->id())
            ->with(['journal', 'payment'])
            ->latest()
            ->paginate(10);

        return view('author.articles.index', compact('articles'));
    }

    public function create(): View
    {
        $journals = Journal::active()->get();
        return view('author.articles.create', compact('journals'));
    }

    public function store(StoreArticleRequest $request): RedirectResponse
    {
        $article = $this->articleService->submit($request->validated(), auth()->id());

        // Notify Editors & Admins
        $adminsAndEditors = \App\Models\User::whereIn('role', ['admin', 'editor'])->active()->get();
        \Illuminate\Support\Facades\Notification::send($adminsAndEditors, new \App\Notifications\ArticleSubmittedNotification($article));

        return redirect()
            ->route('author.articles.show', $article)
            ->with('success', 'Artikel berhasil disubmit! Tim editor akan segera meninjau artikel Anda.');
    }

    public function show(Article $article): View
    {
        // Policy: author hanya bisa lihat artikel miliknya
        $this->authorizeArticle($article);

        $article->load(['journal', 'issue', 'reviews.reviewer', 'payment']);
        return view('author.articles.show', compact('article'));
    }

    public function uploadRevision(Article $article): View
    {
        $this->authorizeArticle($article);

        // Hanya bisa upload revisi jika status revision_required
        if ($article->status !== Article::STATUS_REVISION_REQUIRED) {
            return redirect()->route('author.articles.show', $article)
                ->with('error', 'Artikel tidak dalam status membutuhkan revisi.');
        }

        return view('author.articles.revision', compact('article'));
    }

    public function storeRevision(UploadRevisionRequest $request, Article $article): RedirectResponse
    {
        $this->authorizeArticle($article);

        if ($article->status !== Article::STATUS_REVISION_REQUIRED) {
            return redirect()->route('author.articles.show', $article)
                ->with('error', 'Artikel tidak dalam status membutuhkan revisi.');
        }

        $this->articleService->uploadRevision(
            $article,
            $request->file('revision_file'),
            $request->author_note
        );

        return redirect()
            ->route('author.articles.show', $article)
            ->with('success', 'Revisi berhasil diunggah!');
    }

    /**
     * Pastikan artikel milik author yang login
     */
    private function authorizeArticle(Article $article): void
    {
        if ($article->author_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke artikel ini.');
        }
    }
}
