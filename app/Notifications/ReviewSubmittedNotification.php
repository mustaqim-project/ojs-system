<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public ?string $reviewerName = null,
        public ?string $recommendation = null
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
        $recLabel = $this->getRecommendationLabel();

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Hasil Telaah Masuk (Review Completed): {$this->article->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Seorang reviewer telah menyelesaikan telaah untuk naskah artikel ilmiah:")
            ->line("**{$this->article->title}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Kode Tracking: **{$this->article->tracking_code}**");

        if (!empty($this->reviewerName)) {
            $mail->line("Nama Reviewer: **{$this->reviewerName}**");
        }

        if (!empty($recLabel)) {
            $mail->line("Rekomendasi Reviewer: **{$recLabel}**");
        }

        return $mail
            ->line("Anda dapat memeriksa komentar dan evaluasi lengkap dari reviewer, serta membuat keputusan editorial (Editorial Decision) untuk naskah ini.")
            ->action('Lihat Hasil Review & Keputusan', url('/editor/articles/' . $this->article->id))
            ->line('Terima kasih atas kerja keras tim editorial.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $rec = $this->getRecommendationLabel();
        $recText = $rec ? " (Rekomendasi: {$rec})" : '';
        return "Review selesai untuk naskah '{$this->article->title}'{$recText}. Silakan periksa di dashboard: " . url('/editor/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'     => $this->article->id,
            'reviewer_name'  => $this->reviewerName,
            'recommendation' => $this->recommendation,
            'message'        => "Review selesai untuk '{$this->article->title}'" . ($this->reviewerName ? " oleh {$this->reviewerName}" : ''),
            'url'            => url('/editor/articles/' . $this->article->id),
        ];
    }

    private function getRecommendationLabel(): ?string
    {
        if (empty($this->recommendation)) return null;

        return match (strtolower($this->recommendation)) {
            'accept'                   => 'Accept (Diterima tanpa revisi)',
            'minor', 'minor_revision'  => 'Minor Revision (Revisi Minor)',
            'major', 'major_revision'  => 'Major Revision (Revisi Mayor)',
            'reject'                   => 'Reject (Ditolak)',
            default                    => ucfirst($this->recommendation),
        };
    }
}
