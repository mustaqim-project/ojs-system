<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class RevisionRequiredNotification extends Notification implements ShouldQueue
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
            ->subject("[{$this->article->tracking_code}] Permintaan Revisi Naskah: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Naskah artikel ilmiah Anda berjudul:")
            ->line("**{$this->article->title}**")
            ->line("Pada jurnal **{$journalTitle}** telah selesai ditelaah oleh tim reviewer.")
            ->line("Berdasarkan hasil penelaahan sejawat dan pertimbangan dewan editor, naskah Anda **memerlukan perbaikan/revisi (Revision Required)** sebelum dapat dipertimbangkan untuk penerimaan publikasi.");

        if (!empty($this->editorNote)) {
            $mail->line("Catatan / Instruksi dari Editor:")
                 ->line("_{$this->editorNote}_");
        }

        return $mail
            ->line("Petunjuk pengunggahan revisi:")
            ->line("1. Buka dasbor artikel Anda untuk melihat catatan rinci dari reviewer.")
            ->line("2. Tandai perubahan pada naskah revisi (menggunakan Track Changes atau teks berwarna/highlight).")
            ->line("3. Sertakan catatan tanggapan per poin masukan reviewer (Author Rebuttal / Response Sheet).")
            ->action('Unggah Naskah Revisi', url('/author/articles/' . $this->article->id . '/revision'))
            ->line('Kami menantikan naskah revisi Anda.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Yth. {$notifiable->name},\n\n"
            . "Naskah Anda \"{$this->article->title}\" memerlukan revisi.\n"
            . "Silakan periksa masukan reviewer dan unggah revisi di: " . url('/author/articles/' . $this->article->id . '/revision');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Revisi diperlukan untuk naskah '{$this->article->title}'",
            'url'           => url('/author/articles/' . $this->article->id . '/revision'),
        ];
    }
}
