<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\ReviewAssignment;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article         $article,
        public ReviewAssignment $assignment
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];

        if ($notifiable->phone) {
            $channels[] = FonnteChannel::class;
        }

        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Review Assignment")
            ->greeting("Hello {$notifiable->name},")
            ->line("You have been assigned as a reviewer for the article:")
            ->line("**{$this->article->title}**")
            ->line("Journal: {$this->article->journal->name}")
            ->line("Due Date: {$this->assignment->due_date->format('d M Y')}")
            ->action('Review Article', url('/reviewer/reviews/' . $this->assignment->id))
            ->line('Thank you for your contribution!');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Dear {$notifiable->name},\n\n"
            . "You have been assigned as a reviewer for:\n"
            . "{$this->article->title}\n"
            . "Journal: {$this->article->journal->name}\n"
            . "Due: {$this->assignment->due_date->format('d M Y')}\n\n"
            . "Please login to review: " . url('/reviewer/dashboard');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'assignment_id' => $this->assignment->id,
            'message' => "You have been assigned to review: {$this->article->title}",
            'url' => url('/reviewer/reviews/' . $this->assignment->id),
        ];
    }
}
