<?php

namespace App\Listeners;

use App\Events\SubmissionStageChanged;
use App\Notifications\ArticlePublishedNotification;
use App\Notifications\DeskRejectedNotification;
use App\Notifications\PreReviewRevisionNotification;
use App\Notifications\ProductionStageNotification;
use Illuminate\Support\Facades\Log;

class SendSubmissionStageNotification
{
    /**
     * Handle the submission stage changed event.
     */
    public function handle(SubmissionStageChanged $event): void
    {
        $article = $event->article;
        $article->loadMissing(['author', 'journal', 'issue']);
        $author = $article->author;

        if (!$author) {
            return;
        }

        try {
            switch ($event->newStage) {
                case 'desk_rejected':
                    $author->notify(new DeskRejectedNotification($article, $article->editor_note));
                    break;

                case 'revision_required_before_review':
                    $author->notify(new PreReviewRevisionNotification($article, $article->editor_note));
                    break;

                case 'copy_editing':
                case 'layout_editing':
                case 'proofreading':
                case 'ready_to_publish':
                    $author->notify(new ProductionStageNotification($article, $event->newStage));
                    break;

                case 'published':
                    $author->notify(new ArticlePublishedNotification($article));
                    break;
            }
        } catch (\Throwable $e) {
            Log::error("Failed to send stage notification for article {$article->id} to stage {$event->newStage}: " . $e->getMessage());
        }
    }
}
