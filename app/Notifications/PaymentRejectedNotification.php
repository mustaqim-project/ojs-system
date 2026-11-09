<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class PaymentRejectedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public string  $adminNotes
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

        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Verifikasi Pembayaran Ditolak: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Bukti pembayaran yang Anda unggah untuk naskah artikel ilmiah:")
            ->line("**{$this->article->title}**")
            ->line("Pada jurnal **{$journalTitle}** telah diperiksa oleh tim administrasi dan keuangan.")
            ->line("Dengan berat hati kami sampaikan bahwa bukti pembayaran **belum dapat diverifikasi**.")
            ->line("Alasan / Catatan Verifikator:")
            ->line("_{$this->adminNotes}_")
            ->line("Mohon periksa kembali kesesuaian nominal, rekening tujuan, dan kejelasan bukti transfer, lalu unggah kembali berkas bukti pembayaran Anda.")
            ->action('Unggah Ulang Bukti Pembayaran', url('/author/articles/' . $this->article->id . '/payment'))
            ->line('Terima kasih atas perhatian dan kerja samanya.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        return "Yth. {$notifiable->name}, bukti pembayaran naskah '{$this->article->title}' belum dapat diverifikasi. Catatan: {$this->adminNotes}. Unggah ulang: " . url('/author/articles/' . $this->article->id . '/payment');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Bukti pembayaran naskah '{$this->article->title}' ditolak: {$this->adminNotes}",
            'url'           => url('/author/articles/' . $this->article->id . '/payment'),
        ];
    }
}
