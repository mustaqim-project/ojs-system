<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleAcceptedNotification extends Notification implements ShouldQueue
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
            ->subject("[{$this->article->tracking_code}] Selamat! Naskah Diterima (Letter of Acceptance): {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Kabar gembira! Kami dengan bangga menginformasikan bahwa naskah artikel ilmiah Anda yang berjudul:")
            ->line("**{$this->article->title}**")
            ->line("Dengan kode pelacakan **{$this->article->tracking_code}** telah secara resmi **DITERIMA (ACCEPTED FOR PUBLICATION)** pada jurnal **{$journalTitle}**.");

        if (!empty($this->editorNote)) {
            $mail->line("Catatan Dewan Editor:")
                 ->line("_{$this->editorNote}_");
        }

        $mail->line("Tahapan Selanjutnya:")
             ->line("1. Penyelesaian administrasi dan pembayaran kontribusi publikasi (APC) apabila berlaku.")
             ->line("2. Naskah akan memasuki tahap penyuntingan tata bahasa dan format (Copyediting & Layout Editing).")
             ->line("3. Anda akan diminta memeriksa draf akhir (Author Galley Proofreading) sebelum diterbitkan secara resmi.");

        return $mail
             ->action('Buka Dasbor Artikel', url('/author/articles/' . $this->article->id))
             ->line('Selamat atas penerimaan naskah Anda dan terima kasih telah mempercayakan publikasi karya riset Anda kepada kami.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';
        return "Selamat! Yth. {$notifiable->name},\n\n"
            . "Naskah artikel Anda \"{$this->article->title}\" resmi DITERIMA (ACCEPTED) untuk dipublikasikan di {$journalTitle}!\n\n"
            . "Lihat rincian penerimaan: " . url('/author/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Selamat! Naskah '{$this->article->title}' DITERIMA untuk publikasi",
            'url'           => url('/author/articles/' . $this->article->id),
        ];
    }
}
