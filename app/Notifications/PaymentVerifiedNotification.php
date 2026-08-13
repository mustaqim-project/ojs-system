<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentVerifiedNotification extends Notification implements ShouldQueue
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
            ->subject("[{$this->article->tracking_code}] Payment Verified")
            ->greeting("Hello {$notifiable->name},")
            ->line("Your payment for article '{$this->article->title}' has been verified.")
            ->line("Your article is now moving to the next stage.")
            ->action('View Article', url('/author/articles/' . $this->article->id))
            ->line('Thank you for your payment.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Payment verified for '{$this->article->title}'. Your article is now proceeding to review. Check dashboard: " . url('/author/dashboard');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'message' => "Payment verified for '{$this->article->title}'",
            'url' => url('/author/articles/' . $this->article->id),
        ];
    }
}
