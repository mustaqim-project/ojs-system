<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewAssignedNotification extends Notification
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
            ->subject('New Review Assignment')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('You have been assigned to review the article titled "' . $this->article->title . '".')
            ->action('View Review Task', route('reviewer.reviews.index'))
            ->line('Thank you for contributing your expertise!');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'review_assigned',
            'message' => 'New review assignment: ' . $this->article->title,
            'url' => route('reviewer.reviews.index'),
            'icon' => 'bi-clipboard-plus',
            'color' => 'warning'
        ];
    }
}
