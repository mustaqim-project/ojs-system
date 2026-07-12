<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification
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
            ->subject('Payment Verified')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Your payment for the article titled "' . $this->article->title . '" has been successfully verified.')
            ->action('View Article', route('author.articles.index'))
            ->line('Your article will now proceed to the next stage of publication.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_verified',
            'message' => 'Payment verified for article: ' . $this->article->title,
            'url' => route('author.articles.index'),
            'icon' => 'bi-check-circle-fill',
            'color' => 'success'
        ];
    }
}
