<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PreReviewRevisionNotification extends Notification implements ShouldQueue
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
            ->subject("[{$this->article->tracking_code}] Revisi Awal Diperlukan Sebelum Review: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Naskah artikel Anda berjudul:")
            ->line("**{$this->article->title}**")
            ->line("Pada jurnal **{$journalTitle}** telah diperiksa pada tahap awal (initial screening).")
            ->line("Sebelum naskah dapat diteruskan ke tim penelaah (peer reviewers), dewan editor meminta Anda melakukan beberapa perbaikan awal (format penulisan, kelengkapan berkas, atau pengantar).");

        if (!empty($this->editorNote)) {
            $mail->line("Catatan / Panduan dari Editor:")
                 ->line("_{$this->editorNote}_");
        }

        return $mail
            ->action('Unggah Berkas Revisi Awal', url('/author/articles/' . $this->article->id . '/revision'))
            ->line('Silakan perbaiki dan unggah kembali berkas naskah Anda agar dapat segera diproses ke tahap review.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Yth. {$notifiable->name}, naskah '{$this->article->title}' memerlukan revisi format/awal sebelum proses review. Unggah revisi: " . url('/author/articles/' . $this->article->id . '/revision');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Revisi awal diperlukan sebelum proses review: '{$this->article->title}'",
            'url'           => url('/author/articles/' . $this->article->id . '/revision'),
        ];
    }
}
