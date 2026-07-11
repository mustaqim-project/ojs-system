<?php

namespace App\Http\Controllers\Editor;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $articles = Article::with(['journal', 'author'])
            ->latest()
            ->get();

        $stats = [
            'submitted'           => Article::where('status', 'submitted')->count(),
            'under_review'        => Article::where('status', 'under_review')->count(),
            'revision_required'   => Article::where('status', 'revision_required')->count(),
            'accepted'            => Article::where('status', 'accepted')->count(),
            'published'           => Article::where('status', 'published')->count(),
        ];

        $recentArticles = Article::with(['journal', 'author'])
            ->whereIn('status', ['submitted', 'under_review', 'revision_required'])
            ->latest()
            ->take(10)
            ->get();

        return view('editor.dashboard', compact('stats', 'recentArticles'));
    }
}
