<?php

namespace App\Services;

use App\Models\Article;
use App\Models\ReviewAssignment;
use App\Models\ReviewRound;
use App\Models\Setting;
use App\Models\User;
use App\Notifications\ReviewAssignedNotification;

class ReviewRoundService
{
    /**
     * Create a new review round for a submission.
     */
    public function createRound(Article $article, string $blindMode = 'single_blind'): ReviewRound
    {
        $lastRound = ReviewRound::where('submission_id', $article->id)->max('round_number') ?? 0;

        $round = ReviewRound::create([
            'submission_id' => $article->id,
            'round_number'  => $lastRound + 1,
            'blind_mode'    => $blindMode,
            'status'        => 'open',
            'opened_at'     => now(),
        ]);

        return $round;
    }

    /**
     * Assign a reviewer to a review round.
     *
     * @throws \Exception
     */
    public function assignReviewer(ReviewRound $round, int $reviewerId, int $assignedById): ReviewAssignment
    {
        $article = $round->submission;

        // COI: Cannot assign author as reviewer
        if ($article->author_id === $reviewerId) {
            throw new \Exception('Cannot assign the author as a reviewer (conflict of interest).');
        }

        // Check for duplicate assignment in this round
        $existing = ReviewAssignment::where('review_round_id', $round->id)
            ->where('reviewer_id', $reviewerId)
            ->whereNotIn('status', ['declined'])
            ->first();

        if ($existing) {
            throw new \Exception('Reviewer is already assigned to this round.');
        }

        $dueDays = (int) Setting::get('review_due_days', 14);

        $assignment = ReviewAssignment::create([
            'review_round_id' => $round->id,
            'reviewer_id'     => $reviewerId,
            'assigned_by'     => $assignedById,
            'status'          => 'invited',
            'due_date'        => now()->addDays($dueDays),
        ]);

        // Update submission stage
        $article->update(['current_stage' => 'under_review']);

        // Notify reviewer
        $reviewer = User::find($reviewerId);
        if ($reviewer) {
            $reviewer->notify(new ReviewAssignedNotification($article, $assignment));
        }

        AuditService::log('Review', 'reviewer_assigned', $assignment, null, $assignment->toArray());

        return $assignment;
    }

    /**
     * Close a review round.
     */
    public function closeRound(ReviewRound $round): ReviewRound
    {
        $round->update([
            'status'    => 'closed',
            'closed_at' => now(),
        ]);

        return $round->fresh();
    }

    /**
     * Get available reviewers with load balancing.
     */
    public function getAvailableReviewers(Article $article, ?int $excludeUserId = null): array
    {
        $query = User::byRole('reviewer')->active()
            ->withCount(['reviews as active_review_count' => function ($q) {
                $q->whereIn('status', ['invited', 'accepted']);
            }]);

        if ($excludeUserId) {
            $query->where('id', '!=', $excludeUserId);
        }

        return $query->orderBy('active_review_count')
            ->limit(20)
            ->get()
            ->toArray();
    }
}
