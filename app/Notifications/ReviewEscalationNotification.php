<?php

namespace App\Notifications;

use App\Models\ReviewAssignment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewEscalationNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ReviewAssignment $assignment) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        $article = $this->assignment->round->submission;
        $reviewer = $this->assignment->reviewer;
        return (new MailMessage)
            ->subject("[ESCALATION] Review Overdue - {$article->title}")
            ->greeting("Dear {$notifiable->name},")
            ->line("The following review assignment is overdue:")
            ->line("Article: {$article->title}")
            ->line("Reviewer: {$reviewer->name} ({$reviewer->email})")
            ->line("Due Date: {$this->assignment->due_date->format('d M Y')}")
            ->line("Days Overdue: {$this->assignment->due_date->diffInDays(now())}")
            ->action('View Submission', url('/editor/articles/' . $article->id))
            ->line('Please consider reassigning or following up.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'article_id' => $this->assignment->round->submission_id,
            'message' => "Review overdue: {$this->assignment->reviewer->name} for assignment #{$this->assignment->id}",
            'url' => url('/editor/articles/' . $this->assignment->round->submission_id),
        ];
    }
}
