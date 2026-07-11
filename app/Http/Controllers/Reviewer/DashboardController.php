<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Models\Review;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $reviews = Review::where('reviewer_id', auth()->id())
            ->with(['article.journal', 'article.author'])
            ->latest()
            ->get();

        $stats = [
            'pending'     => $reviews->where('status', 'pending')->count(),
            'in_progress' => $reviews->where('status', 'in_progress')->count(),
            'completed'   => $reviews->where('status', 'completed')->count(),
            'total'       => $reviews->count(),
        ];

        $pendingReviews = $reviews->whereIn('status', ['pending', 'in_progress']);

        return view('reviewer.dashboard', compact('reviews', 'stats', 'pendingReviews'));
    }
}
