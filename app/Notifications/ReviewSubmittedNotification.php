<?php

namespace App\Notifications;

use App\Models\Article;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Article $article) {}

    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Review Completed")
            ->greeting("Hello {$notifiable->name},")
            ->line("A review has been completed for: **{$this->article->title}**")
            ->action('View Review', url('/editor/articles/' . $this->article->id))
            ->line('You can now make an editorial decision.');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'message' => "Review completed for '{$this->article->title}'",
            'url' => url('/editor/articles/' . $this->article->id),
        ];
    }
}
