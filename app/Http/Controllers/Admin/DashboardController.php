<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Models\Journal;
use App\Models\Payment;
use App\Models\User;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_users'      => User::count(),
            'total_journals'   => Journal::count(),
            'total_articles'   => Article::count(),
            'published'        => Article::where('status', 'published')->count(),
            'pending_payment'  => Payment::whereIn('status', ['uploaded'])->count(),
            'total_revenue'    => Payment::where('status', 'verified')->sum('amount'),
        ];

        $recentArticles  = Article::with(['journal', 'author'])->latest()->take(8)->get();
        $pendingPayments = Payment::with(['article.author', 'article.journal'])
            ->where('status', 'uploaded')
            ->latest()
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentArticles', 'pendingPayments'));
    }
}
