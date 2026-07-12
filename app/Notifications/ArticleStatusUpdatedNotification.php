<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleStatusUpdatedNotification extends Notification
{
    use Queueable;

    public $article;
    public $statusName;

    /**
     * Create a new notification instance.
     */
    public function __construct($article, $statusName)
    {
        $this->article = $article;
        $this->statusName = $statusName;
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
            ->subject('Article Status Updated: ' . $this->statusName)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('The status of your article titled "' . $this->article->title . '" has been updated to **' . $this->statusName . '**.')
            ->action('View Article', route('author.articles.index'))
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
            'type' => 'article_status_updated',
            'message' => 'Article status updated to ' . $this->statusName . ': ' . $this->article->title,
            'url' => route('author.articles.index'),
            'icon' => 'bi-info-circle',
            'color' => 'info'
        ];
    }
}
