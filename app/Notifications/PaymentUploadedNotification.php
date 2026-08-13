<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentUploadedNotification extends Notification implements ShouldQueue
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
            ->subject("[{$this->article->tracking_code}] Payment Proof Uploaded")
            ->greeting("Hello {$notifiable->name},")
            ->line("Payment proof has been uploaded for article: **{$this->article->title}**")
            ->line("Please verify the payment as soon as possible.")
            ->action('Verify Payment', url('/admin/payments'))
            ->line('Thank you.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Payment proof uploaded for '{$this->article->title}'. Please verify: " . url('/admin/payments');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'message' => "Payment proof uploaded for '{$this->article->title}'",
            'url' => url('/admin/payments'),
        ];
    }
}
