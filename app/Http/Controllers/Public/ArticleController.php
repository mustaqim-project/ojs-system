<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\View\View;

class ArticleController extends Controller
{
    public function index(): View
    {
        $articles = Article::published()
            ->with(['journal', 'author', 'issue'])
            ->latest('published_at')
            ->paginate(15);

        return view('public.articles.index', compact('articles'));
    }

    public function show(string $slug): View
    {
        $article = Article::where('slug', $slug)
            ->where('status', 'published')
            ->with(['journal', 'author', 'issue'])
            ->firstOrFail();

        // Artikel terkait (jurnal yang sama)
        $related = Article::published()
            ->where('journal_id', $article->journal_id)
            ->where('id', '!=', $article->id)
            ->latest('published_at')
            ->take(5)
            ->get();

        return view('public.articles.show', compact('article', 'related'));
    }
}
