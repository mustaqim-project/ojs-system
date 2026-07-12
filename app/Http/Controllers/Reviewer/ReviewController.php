<?php

namespace App\Http\Controllers\Reviewer;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreReviewRequest;
use App\Models\Review;
use App\Services\ReviewService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ReviewController extends Controller
{
    public function __construct(private ReviewService $reviewService) {}

    public function index(): View
    {
        $reviews = Review::where('reviewer_id', auth()->id())
            ->with(['article.journal', 'article.author'])
            ->latest()
            ->paginate(10);

        return view('reviewer.reviews.index', compact('reviews'));
    }

    public function show(Review $review): View
    {
        // Security: reviewer hanya bisa akses review miliknya
        $this->authorizeReview($review);
        $review->load(['article.journal', 'article.author', 'article.reviews']);

        return view('reviewer.reviews.show', compact('review'));
    }

    public function accept(Review $review): RedirectResponse
    {
        $this->authorizeReview($review);

        if ($review->status !== 'pending') {
            return back()->with('error', 'Tugas ini sudah diproses.');
        }

        $this->reviewService->acceptAssignment($review);
        return back()->with('success', 'Anda menerima tugas review ini.');
    }

    public function decline(Review $review): RedirectResponse
    {
        $this->authorizeReview($review);

        if ($review->status !== 'pending') {
            return back()->with('error', 'Tugas ini sudah diproses.');
        }

        $this->reviewService->declineAssignment($review);
        return back()->with('success', 'Anda menolak tugas review ini.');
    }

    public function submit(StoreReviewRequest $request, Review $review): RedirectResponse
    {
        $this->authorizeReview($review);

        if (!in_array($review->status, ['accepted', 'in_progress'])) {
            return back()->with('error', 'Review tidak dapat disubmit pada status ini.');
        }

        $this->reviewService->submitReview($review, $request->validated());

        // Notify Editors
        $editors = \App\Models\User::where('role', 'editor')->active()->get();
        \Illuminate\Support\Facades\Notification::send($editors, new \App\Notifications\ReviewSubmittedNotification($review->article, auth()->user()->name));

        return redirect()
            ->route('reviewer.reviews.show', $review)
            ->with('success', 'Review berhasil disubmit! Terima kasih.');
    }

    private function authorizeReview(Review $review): void
    {
        if ($review->reviewer_id !== auth()->id()) {
            abort(403, 'Anda tidak memiliki akses ke review ini.');
        }
    }
}
