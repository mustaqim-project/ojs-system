<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubmissionAcknowledgmentNotification extends Notification implements ShouldQueue
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
        $submittedDate = $this->article->submitted_at
            ? $this->article->submitted_at->translatedFormat('d F Y H:i')
            : now()->translatedFormat('d F Y H:i');

        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Tanda Terima Pengiriman Naskah: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Terima kasih telah mengirimkan naskah artikel ilmiah Anda ke **{$journalTitle}**.")
            ->line("Berikut rincian naskah yang kami terima:")
            ->line("Judul Naskah: **{$this->article->title}**")
            ->line("Kode Pelacakan (Tracking Code): **{$this->article->tracking_code}**")
            ->line("Tanggal Pengiriman: **{$submittedDate}**")
            ->line("Naskah Anda saat ini sedang memasuki tahap **Pemeriksaan Awal (Initial Editorial Screening)** oleh dewan editor untuk memeriksa kesesuaian fokus & ruang lingkup (focus & scope) serta format penulisan.")
            ->line("Anda dapat memantau setiap tahapan proses naskah Anda secara langsung melalui tautan di bawah ini:")
            ->action('Pantau Status Naskah', url('/author/articles/' . $this->article->id))
            ->line('Jika Anda memiliki pertanyaan mengenai proses penelaahan, jangan ragu untuk menghubungi tim editorial kami.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';
        return "Yth. {$notifiable->name},\n\n"
            . "Naskah artikel Anda \"{$this->article->title}\" berhasil dikirimkan ke {$journalTitle}.\n"
            . "Kode Tracking: {$this->article->tracking_code}\n\n"
            . "Pantau progres naskah Anda di: " . url('/author/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Naskah '{$this->article->title}' berhasil disubmit [{$this->article->tracking_code}]",
            'url'           => url('/author/articles/' . $this->article->id),
        ];
    }
}
