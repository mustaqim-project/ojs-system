<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentUploadedNotification extends Notification
{
    use Queueable;

    public $article;
    public $authorName;

    /**
     * Create a new notification instance.
     */
    public function __construct($article, $authorName)
    {
        $this->article = $article;
        $this->authorName = $authorName;
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
            ->subject('Payment Proof Uploaded')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Author ' . $this->authorName . ' has uploaded a payment proof for the article titled "' . $this->article->title . '".')
            ->action('Verify Payment', route('admin.payments.index'))
            ->line('Please review and verify the payment.');
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'type' => 'payment_uploaded',
            'message' => 'Payment uploaded by ' . $this->authorName . ' for: ' . $this->article->title,
            'url' => route('admin.payments.index'),
            'icon' => 'bi-receipt',
            'color' => 'warning'
        ];
    }
}
