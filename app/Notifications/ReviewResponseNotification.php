<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\Review;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewResponseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public string  $responseType, // 'accepted' or 'declined'
        public string  $reviewerName,
        public ?string $reason = null
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
        $isAccepted = strtolower($this->responseType) === 'accepted';
        $statusText = $isAccepted ? 'MENERIMA (Accepted)' : 'MENOLAK (Declined)';

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Reviewer {$statusText}: {$this->article->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Reviewer **{$this->reviewerName}** telah **{$statusText}** tugas penelaahan untuk naskah:")
            ->line("**{$this->article->title}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Kode Tracking: **{$this->article->tracking_code}**");

        if ($isAccepted) {
            $mail->line("Reviewer sedang memulai proses penelaahan naskah ini.");
        } else {
            $mail->line("Reviewer tidak dapat menelaah naskah ini pada saat ini.");
            if (!empty($this->reason)) {
                $mail->line("Alasan Penolakan: \"{$this->reason}\"");
            }
            $mail->line("Disarankan untuk segera menugaskan reviewer alternatif agar proses editorial tetap tepat waktu.");
        }

        return $mail
            ->action('Buka Halaman Artikel Editor', url('/editor/articles/' . $this->article->id))
            ->line('Terima kasih atas perhatian Anda.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $isAccepted = strtolower($this->responseType) === 'accepted';
        $statusText = $isAccepted ? 'menerima' : 'menolak';
        return "Reviewer {$this->reviewerName} telah {$statusText} tugas telaah untuk '{$this->article->title}'. Kelola naskah: " . url('/editor/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        $isAccepted = strtolower($this->responseType) === 'accepted';
        return [
            'article_id'    => $this->article->id,
            'reviewer_name' => $this->reviewerName,
            'response_type' => $this->responseType,
            'message'       => "Reviewer {$this->reviewerName} " . ($isAccepted ? "menerima" : "menolak") . " tugas telaah untuk '{$this->article->title}'",
            'url'           => url('/editor/articles/' . $this->article->id),
        ];
    }
}
