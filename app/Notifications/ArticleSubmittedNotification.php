<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleSubmittedNotification extends Notification implements ShouldQueue
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
        $authorName = $this->article->author->name ?? 'Author';

        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Naskah Baru Masuk (New Submission): {$this->article->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Sebuah naskah artikel ilmiah baru telah disubmit ke dalam sistem:")
            ->line("Judul Naskah: **{$this->article->title}**")
            ->line("Penulis: **{$authorName}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Kode Tracking: **{$this->article->tracking_code}**")
            ->line("Naskah ini memerlukan pemeriksaan awal (editorial screening) sebelum ditugaskan kepada editor bidang atau reviewer.")
            ->action('Periksa Naskah Submission', url('/editor/articles/' . $this->article->id))
            ->line('Silakan tinjau kelayakan naskah sesegera mungkin.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $authorName = $this->article->author->name ?? 'Author';
        return "Naskah baru masuk: '{$this->article->title}' oleh {$authorName} [{$this->article->tracking_code}]. Periksa di: " . url('/editor/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        $authorName = $this->article->author->name ?? 'Author';
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Naskah baru masuk dari {$authorName}: '{$this->article->title}'",
            'url'           => url('/editor/articles/' . $this->article->id),
        ];
    }
}
