<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleSubmittedNotification extends Notification
{
    use Queueable;
    public $article;

    /**
     * Create a new notification instance.
     */
    public function __construct($article)
    {
        $this->article = $article;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        return (new MailMessage)
            ->subject('New Article Submitted')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('A new article titled "' . $this->article->title . '" has been submitted by ' . $this->article->author->name . '.')
            ->action('View Article', route('editor.articles.index'))
            ->line('Thank you for using our application!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'article_submitted',
            'message' => 'New article submitted: ' . $this->article->title,
            'url' => route('editor.articles.index'),
            'icon' => 'bi-file-earmark-plus',
            'color' => 'primary'
        ];
    }
}
