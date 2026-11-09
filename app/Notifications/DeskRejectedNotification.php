<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class DeskRejectedNotification extends Notification implements ShouldQueue
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
            ->subject("[{$this->article->tracking_code}] Keputusan Pemeriksaan Awal (Desk Reject): {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Terima kasih atas minat Anda untuk mempublikasikan karya ilmiah di **{$journalTitle}**.")
            ->line("Naskah Anda:")
            ->line("**{$this->article->title}**")
            ->line("Telah melalui tahap pemeriksaan awal (editorial screening). Dengan berat hati kami memberitahukan bahwa naskah Anda **tidak dapat dilanjutkan ke tahap penelaahan sejawat (peer review)**.");

        if (!empty($this->editorNote)) {
            $mail->line("Alasan / Catatan Editor:")
                 ->line("_{$this->editorNote}_");
        }

        return $mail
            ->line("Keputusan ini umumnya didasarkan pada ketidaksesuaian fokus dan ruang lingkup jurnal (aims & scope), tingkat kesamaan naskah (plagiasi), atau ketidaksesuaian panduan penulisan. Hal ini tidak mengurangi nilai karya riset Anda, dan kami mempersilakan Anda mengajukannya ke jurnal ilmiah lain yang lebih relevan.")
            ->action('Lihat Detail Naskah', url('/author/articles/' . $this->article->id))
            ->line('Terima kasih atas perhatian Anda.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Yth. {$notifiable->name}, naskah '{$this->article->title}' tidak lolos pemeriksaan awal (Desk Reject). Periksa catatan editor: " . url('/author/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Naskah '{$this->article->title}' tidak lolos pemeriksaan awal (Desk Reject)",
            'url'           => url('/author/articles/' . $this->article->id),
        ];
    }
}
