<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticlePublishedNotification extends Notification implements ShouldQueue
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
        $issue = $this->article->issue;
        $issueText = $issue
            ? "Vol. {$issue->volume} No. {$issue->number} ({$issue->year})"
            : ($this->article->published_at ? $this->article->published_at->format('Y') : date('Y'));

        $publicUrl = url('/articles/' . $this->article->slug);

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Selamat! Naskah Anda Telah Resmi Dipublikasikan: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Kabar gembira! Kami dengan bangga memberitahukan bahwa naskah artikel ilmiah Anda telah resmi **DIPUBLIKASIKAN (PUBLISHED)**.")
            ->line("Rincian Publikasi:")
            ->line("Judul Artikel: **{$this->article->title}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Terbitan / Issue: **{$issueText}**")
            ->line("Kode Tracking: **{$this->article->tracking_code}**");

        if (!empty($this->article->doi)) {
            $mail->line("Digital Object Identifier (DOI): **https://doi.org/{$this->article->doi}**");
        }

        return $mail
            ->line("Artikel Anda kini telah tersedia secara terbuka (Open Access), dapat diakses, dibaca, dan dikutip oleh seluruh akademisi dan peneliti di seluruh dunia.")
            ->action('Baca Artikel yang Dipublikasikan', $publicUrl)
            ->line('Kami mengucapkan selamat atas pencapaian publikasi ilmiah ini dan mengundang Anda untuk menyebarluaskan tautan artikel ini kepada kolega serta jaringan riset Anda.')
            ->line('Terima kasih atas kontribusi ilmiah Anda pada jurnal kami.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';
        $publicUrl = url('/articles/' . $this->article->slug);
        return "Selamat! Yth. {$notifiable->name},\n\n"
            . "Naskah artikel Anda \"{$this->article->title}\" telah RESMI DIPUBLIKASIKAN di {$journalTitle}!\n\n"
            . "Baca dan unduh artikel: {$publicUrl}";
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Selamat! Artikel '{$this->article->title}' telah resmi dipublikasikan",
            'url'           => url('/articles/' . $this->article->slug),
        ];
    }
}
