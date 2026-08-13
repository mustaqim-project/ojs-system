<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Journal;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        $journals         = Journal::active()->with('articles')->take(6)->get()->map(function ($journal) {
            $journal->published_articles_count = $journal->articles()->where('status', 'published')->count();
            return $journal;
        });
        $latestArticles   = Article::published()
            ->with(['journal', 'author'])
            ->latest('published_at')
            ->take(6)
            ->get();
        $totalPublished   = Article::published()->count();
        $totalJournals    = Journal::active()->count();
        $siteName         = Setting::get('site_name', 'Portal Jurnal');
        $siteDescription  = Setting::get('site_description', '');

        return view('public.home', compact(
            'journals',
            'latestArticles',
            'totalPublished',
            'totalJournals',
            'siteName',
            'siteDescription'
        ));
    }

    public function search(Request $request): View
    {
        $query   = $request->get('q', '');
        $articles = collect();

        if ($query) {
            $articles = Article::published()
                ->with(['journal', 'author'])
                ->where(function ($q) use ($query) {
                    $q->where('title', 'like', "%{$query}%")
                        ->orWhere('abstract', 'like', "%{$query}%")
                        ->orWhere('keywords', 'like', "%{$query}%");
                })
                ->latest('published_at')
                ->paginate(10)
                ->withQueryString();
        }

        return view('public.search', compact('articles', 'query'));
    }
}
