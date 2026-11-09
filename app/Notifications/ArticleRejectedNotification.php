<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public ?string $editorNote = null
    ) {}

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

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Keputusan Editorial Naskah: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Terima kasih telah mengajukan naskah artikel ilmiah Anda yang berjudul:")
            ->line("**{$this->article->title}**")
            ->line("Ke jurnal **{$journalTitle}**.")
            ->line("Setelah melalui proses penelaahan sejawat (peer review) dan evaluasi komprehensif oleh dewan editor, dengan berat hati kami menginformasikan bahwa naskah Anda **belum dapat kami terima untuk publikasi** pada jurnal ini.");

        if (!empty($this->editorNote)) {
            $mail->line("Catatan / Masukan dari Tim Editorial:")
                 ->line("_{$this->editorNote}_");
        }

        return $mail
            ->line("Keputusan ini diambil dengan mempertimbangkan standar ruang lingkup, tingkat kebaruan, dan rekomendasi reviewer. Kami mendorong Anda untuk memanfaatkan masukan yang telah diberikan guna menyempurnakan naskah untuk pengajuan ke jurnal lainnya.")
            ->action('Lihat Detail Naskah', url('/author/articles/' . $this->article->id))
            ->line('Kami sangat menghargai kerja keras dan kontribusi ilmiah Anda.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';
        return "Yth. {$notifiable->name},\n\n"
            . "Pemberitahuan keputusan editorial untuk naskah \"{$this->article->title}\" pada {$journalTitle}.\n"
            . "Silakan periksa detailnya di: " . url('/author/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Keputusan editorial naskah '{$this->article->title}': Ditolak",
            'url'           => url('/author/articles/' . $this->article->id),
        ];
    }
}
