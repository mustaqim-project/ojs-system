<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Article $article) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->phone) $channels[] = FonnteChannel::class;
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] New Article Submitted")
            ->greeting("Hello {$notifiable->name},")
            ->line("A new article has been submitted: **{$this->article->title}**")
            ->line("Author: {$this->article->author->name}")
            ->line("Journal: " . ($this->article->journal->title ?? $this->article->journal->name ?? '—'))
            ->action('Review Submission', url('/editor/articles/' . $this->article->id))
            ->line('Please review it as soon as possible.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "New article submitted: '{$this->article->title}' by {$this->article->author->name}. Please review: " . url('/editor/dashboard');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'message' => "New submission: {$this->article->title}",
            'url' => url('/editor/articles/' . $this->article->id),
        ];
    }
}
