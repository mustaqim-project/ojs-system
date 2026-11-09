<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewAcknowledgmentNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Article $article) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if (!empty($notifiable->phone)) {
            $channels[] = FonnteChannel::class;
        }
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';

        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Terima Kasih Atas Review Naskah: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Terima kasih banyak atas waktu, dedikasi, dan telaah mendalam yang telah Anda berikan untuk naskah:")
            ->line("**{$this->article->title}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Kode Tracking: **{$this->article->tracking_code}**")
            ->line("Evaluasi dan rekomendasi kritis yang Anda sampaikan sangat bernilai tinggi dalam menjaga standar akademik dan integritas ilmiah publikasi kami.")
            ->line("Tim editorial kami akan segera menindaklanjuti hasil review ini untuk proses keputusan naskah.")
            ->action('Lihat Riwayat Review', url('/reviewer/reviews'))
            ->line('Sekali lagi, terima kasih atas kontribusi berharga Anda bagi komunitas akademik.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';
        return "Yth. {$notifiable->name},\n\n"
            . "Terima kasih banyak atas telaah naskah \"{$this->article->title}\" pada {$journalTitle}. Kontribusi keahlian Anda sangat kami hargai!";
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id' => $this->article->id,
            'message'    => "Hasil telaah untuk '{$this->article->title}' telah kami terima. Terima kasih!",
            'url'        => url('/reviewer/reviews'),
        ];
    }
}
