<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewSubmittedNotification extends Notification
{
    use Queueable;

    public $article;
    public $reviewerName;

    /**
     * Create a new notification instance.
     */
    public function __construct($article, $reviewerName)
    {
        $this->article = $article;
        $this->reviewerName = $reviewerName;
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
            ->subject('Review Submitted for Article: ' . $this->article->title)
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Reviewer ' . $this->reviewerName . ' has submitted their review for the article titled "' . $this->article->title . '".')
            ->action('View Reviews', route('editor.articles.index'))
            ->line('Please review their feedback to make an editorial decision.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_submitted',
            'message' => 'Review submitted by ' . $this->reviewerName . ' for: ' . $this->article->title,
            'url' => route('editor.articles.index'),
            'icon' => 'bi-clipboard-check',
            'color' => 'success'
        ];
    }
}
