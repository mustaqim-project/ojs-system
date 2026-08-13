<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public string  $status
    ) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->phone) $channels[] = FonnteChannel::class;
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Status Updated: {$this->status}")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your article '{$this->article->title}' status has been updated to: **{$this->status}**")
            ->action('View Article', url('/author/articles/' . $this->article->id))
            ->line('Thank you.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Article Update: '{$this->article->title}' status is now: {$this->status}. Check your dashboard: " . url('/author/dashboard');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'message' => "Article '{$this->article->title}' status: {$this->status}",
            'url' => url('/author/articles/' . $this->article->id),
        ];
    }
}
