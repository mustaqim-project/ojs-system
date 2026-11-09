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

        // Dynamic metrics directly from database without hardcoded fallbacks
        $totalAuthors = \App\Models\User::where('role', 'author')
            ->orWhereHas('roles', function($q) { $q->where('name', 'author'); })
            ->orWhereHas('articles')
            ->distinct()
            ->count();

        $totalReviewers = \App\Models\User::where('role', 'reviewer')
            ->orWhereHas('roles', function($q) { $q->where('name', 'reviewer'); })
            ->orWhereHas('reviewAssignments')
            ->distinct()
            ->count();

        // Calculate Average First Decision Days
        $firstDecisions = \App\Models\EditorialDecision::selectRaw('submission_id, MIN(decided_at) as first_decision_at')
            ->groupBy('submission_id')
            ->get();
        $decisionDaysList = [];
        foreach ($firstDecisions as $decision) {
            $article = Article::find($decision->submission_id);
            if ($article && $article->submitted_at) {
                $decisionDaysList[] = \Carbon\Carbon::parse($article->submitted_at)->diffInDays(\Carbon\Carbon::parse($decision->first_decision_at));
            }
        }
        $decidedArticles = Article::whereNotNull('accepted_at')->whereNotNull('submitted_at')->get();
        foreach ($decidedArticles as $art) {
            $decisionDaysList[] = $art->submitted_at->diffInDays($art->accepted_at);
        }
        $avgFirstDecisionDays = count($decisionDaysList) > 0 ? (int) round(array_sum($decisionDaysList) / count($decisionDaysList)) : 0;

        // Calculate Acceptance Rate
        $totalSubmissions = Article::count();
        $acceptedCount = Article::whereIn('status', ['accepted', 'published', 'waiting_payment', 'payment_uploaded', 'payment_verification', 'paid'])->count();
        $acceptanceRate = $totalSubmissions > 0 ? (int) round(($acceptedCount / $totalSubmissions) * 100) : 0;

        // Calculate Average Publication Days
        $pubArticles = Article::published()->whereNotNull('submitted_at')->whereNotNull('published_at')->get();
        $pubDaysList = [];
        foreach ($pubArticles as $art) {
            $pubDaysList[] = $art->submitted_at->diffInDays($art->published_at);
        }
        $avgPublicationDays = count($pubDaysList) > 0 ? (int) round(array_sum($pubDaysList) / count($pubDaysList)) : 0;

        // Calculate Total Views and Downloads from Database
        $totalDownloadsVal = (int) Article::sum('downloads_count');
        $totalDownloads = $totalDownloadsVal >= 1000 ? round($totalDownloadsVal / 1000, 1) . 'K+' : (string) $totalDownloadsVal;

        $totalViewsVal = (int) Article::sum('views_count');
        $totalViews = $totalViewsVal >= 1000 ? round($totalViewsVal / 1000, 1) . 'K+' : (string) $totalViewsVal;

        return view('public.home', compact(
            'journals',
            'latestArticles',
            'totalPublished',
            'totalJournals',
            'siteName',
            'siteDescription',
            'totalAuthors',
            'totalReviewers',
            'avgFirstDecisionDays',
            'acceptanceRate',
            'avgPublicationDays',
            'totalDownloads',
            'totalViews'
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
