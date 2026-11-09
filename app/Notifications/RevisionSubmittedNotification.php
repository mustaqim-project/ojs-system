<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RevisionSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public ?string $authorNote = null
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
        $authorName = $this->article->author->name ?? 'Author';

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Naskah Revisi Telah Diunggah: {$this->article->title}")
            ->greeting("Halo {$notifiable->name},")
            ->line("Penulis naskah telah mengunggah berkas revisi untuk artikel:")
            ->line("**{$this->article->title}**")
            ->line("Penulis: **{$authorName}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Kode Tracking: **{$this->article->tracking_code}**");

        if (!empty($this->authorNote)) {
            $mail->line("Catatan dari Penulis (Author Note):")
                 ->line("_{$this->authorNote}_");
        }

        return $mail
            ->line("Naskah revisi kini siap untuk ditinjau kembali oleh tim editorial atau dikirimkan kembali ke reviewer terkait.")
            ->action('Periksa Naskah Revisi', url('/editor/articles/' . $this->article->id))
            ->line('Terima kasih atas dedikasi Anda dalam proses editorial.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Naskah revisi diunggah untuk '{$this->article->title}' oleh {$this->article->author->name}. Periksa di: " . url('/editor/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Naskah revisi diunggah oleh author untuk '{$this->article->title}'",
            'url'           => url('/editor/articles/' . $this->article->id),
        ];
    }
}
