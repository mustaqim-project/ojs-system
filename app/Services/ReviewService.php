<?php

namespace App\Services;

use App\Models\Article;
use App\Models\Review;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ReviewService
{
    /**
     * Reviewer submit hasil review
     */
    public function submitReview(Review $review, array $data): Review
    {
        return DB::transaction(function () use ($review, $data) {
            $reviewFilePath = null;
            if (!empty($data['review_file'])) {
                $filename       = 'review-' . Str::uuid() . '.' . $data['review_file']->getClientOriginalExtension();
                $data['review_file']->move(public_path('uploads/reviews'), $filename);
                $reviewFilePath = 'uploads/reviews/' . $filename;
            }

            $review->update([
                'status'                => 'completed',
                'recommendation'        => $data['recommendation'],
                'comments_to_author'    => $data['comments_to_author'],
                'comments_to_editor'    => $data['comments_to_editor'] ?? null,
                'review_file'           => $reviewFilePath,
                'originality_score'     => $data['originality_score'] ?? null,
                'methodology_score'     => $data['methodology_score'] ?? null,
                'relevance_score'       => $data['relevance_score'] ?? null,
                'writing_score'         => $data['writing_score'] ?? null,
                'completed_at'          => now(),
            ]);

            // Cek apakah semua reviewer sudah selesai
            $this->checkAllReviewsCompleted($review->article);

            return $review->fresh();
        });
    }

    /**
     * Cek apakah semua review sudah selesai
     * (opsional: auto-set status ke ready for decision)
     */
    private function checkAllReviewsCompleted(Article $article): void
    {
        $totalReviews     = $article->reviews()->whereIn('status', ['accepted', 'in_progress', 'completed'])->count();
        $completedReviews = $article->reviews()->where('status', 'completed')->count();

        // Jika semua reviewer sudah submit, tidak ada action otomatis
        // Editor yang akan membuat keputusan
    }

    /**
     * Reviewer accept tugas review
     */
    public function acceptAssignment(Review $review): Review
    {
        $review->update(['status' => 'in_progress']);
        return $review->fresh();
    }

    /**
     * Reviewer decline tugas review
     */
    public function declineAssignment(Review $review, ?string $reason = null): Review
    {
        $review->update(['status' => 'declined']);
        return $review->fresh();
    }
}
