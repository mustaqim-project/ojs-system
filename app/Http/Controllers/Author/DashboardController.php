<?php

namespace App\Http\Controllers\Author;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $user     = auth()->user();
        $articles = Article::forAuthor($user->id)
            ->with(['journal', 'payment'])
            ->latest()
            ->get();

        $stats = [
            'total'           => $articles->count(),
            'submitted'       => $articles->where('status', 'submitted')->count(),
            'under_review'    => $articles->where('status', 'under_review')->count(),
            'accepted'        => $articles->whereIn('status', ['accepted', 'waiting_payment', 'payment_uploaded', 'payment_verification', 'paid'])->count(),
            'published'       => $articles->where('status', 'published')->count(),
            'waiting_payment' => $articles->whereIn('status', ['waiting_payment', 'payment_uploaded', 'payment_verification'])->count(),
        ];

        return view('author.dashboard', compact('articles', 'stats'));
    }
}
