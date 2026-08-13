<?php

namespace App\Services;

use App\Models\Article;
use App\Events\SubmissionStageChanged;

class InvalidStateTransitionException extends \RuntimeException {}

class SubmissionStateMachine
{
    /**
     * All valid state transitions.
     * Key = current stage, Value = array of allowed next stages.
     */
    private array $transitions = [
        'draft'                        => ['submitted'],
        'submitted'                    => ['screening'],
        'screening'                    => ['desk_rejected', 'waiting_payment', 'reviewer_assignment', 'revision_required_before_review'],
        'waiting_payment'              => ['waiting_verification'],
        'waiting_verification'         => ['waiting_payment', 'reviewer_assignment'],
        'reviewer_assignment'          => ['under_review'],
        'under_review'                 => ['revision_required', 'rejected', 'accepted'],
        'revision_required'            => ['under_review'],
        'accepted'                     => ['copy_editing'],
        'copy_editing'                 => ['layout_editing'],
        'layout_editing'               => ['proofreading'],
        'proofreading'                 => ['ready_to_publish'],
        'ready_to_publish'             => ['published'],
        'desk_rejected'                => [],
        'rejected'                     => [],
        'published'                    => ['retracted'],
        'withdrawn'                    => [],
        'retracted'                    => [],
    ];

    /**
     * Check if a transition is allowed.
     */
    public function canTransition(string $currentStage, string $nextStage): bool
    {
        if (!isset($this->transitions[$currentStage])) {
            return false;
        }
        return in_array($nextStage, $this->transitions[$currentStage], true);
    }

    /**
     * Get all allowed next stages from the current stage.
     */
    public function getAllowedTransitions(string $currentStage): array
    {
        return $this->transitions[$currentStage] ?? [];
    }

    /**
     * Execute a state transition.
     *
     * @throws InvalidStateTransitionException
     */
    public function transition(Article $article, string $newStage): Article
    {
        $currentStage = $article->current_stage ?? $article->status;

        if (!$this->canTransition($currentStage, $newStage)) {
            throw new InvalidStateTransitionException(
                "Cannot transition from '{$currentStage}' to '{$newStage}'. " .
                    "Allowed: " . implode(', ', $this->getAllowedTransitions($currentStage))
            );
        }

        $oldStage = $currentStage;

        $article->update([
            'current_stage' => $newStage,
            'status'        => $newStage,
        ]);

        // Fire event for audit trail and notifications
        event(new SubmissionStageChanged($article, $oldStage, $newStage));

        return $article->fresh();
    }

    /**
     * Get all available stages for UI display.
     */
    public static function getAllStages(): array
    {
        return [
            'draft'                        => 'Draft',
            'submitted'                    => 'Submitted',
            'screening'                    => 'Initial Screening',
            'desk_rejected'                => 'Desk Rejected',
            'waiting_payment'              => 'Waiting Payment',
            'waiting_verification'         => 'Waiting Verification',
            'reviewer_assignment'          => 'Reviewer Assignment',
            'under_review'                 => 'Under Review',
            'revision_required'            => 'Revision Required',
            'revision_required_before_review' => 'Pre-Review Revision',
            'accepted'                     => 'Accepted',
            'rejected'                     => 'Rejected',
            'copy_editing'                 => 'Copy Editing',
            'layout_editing'               => 'Layout Editing',
            'proofreading'                 => 'Proofreading',
            'ready_to_publish'             => 'Ready to Publish',
            'published'                    => 'Published',
            'retracted'                    => 'Retracted',
            'withdrawn'                    => 'Withdrawn',
        ];
    }
}
