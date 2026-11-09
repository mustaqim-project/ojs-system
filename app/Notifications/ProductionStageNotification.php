<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProductionStageNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public string  $stage
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
        $stageLabel = $this->getStageLabel();

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Tahap Produksi: {$stageLabel} - {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Naskah artikel Anda:")
            ->line("**{$this->article->title}**")
            ->line("Pada jurnal **{$journalTitle}** saat ini telah memasuki tahap:")
            ->line("**{$stageLabel}**");

        if ($this->stage === 'copy_editing') {
            $mail->line("Naskah Anda sedang dalam proses penyuntingan teks, tata bahasa, dan standarisasi gaya selingkung jurnal oleh tim copyeditor.");
        } elseif ($this->stage === 'layout_editing') {
            $mail->line("Naskah Anda sedang dalam proses tata letak halaman (layout) dan pembuatan berkas PDF terbitan (galley) oleh tim layouter.");
        } elseif ($this->stage === 'proofreading') {
            $mail->line("Berkas draf akhir (galley proof) telah disiapkan. Mohon periksa susunan naskah, nama penulis, afiliasi, data tabel, dan gambar untuk memastikan tidak ada kekeliruan sebelum penerbitan resmi.");
        } elseif ($this->stage === 'ready_to_publish') {
            $mail->line("Seluruh proses produksi telah rampung dan naskah Anda siap untuk dijadwalkan terbit pada edisi/isu jurnal mendatang.");
        }

        return $mail
            ->action('Lihat Status Naskah', url('/author/articles/' . $this->article->id))
            ->line('Terima kasih atas kerja samanya dalam proses produksi artikel.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $stageLabel = $this->getStageLabel();
        return "Yth. {$notifiable->name}, naskah '{$this->article->title}' telah memasuki tahap: {$stageLabel}. Pantau di: " . url('/author/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        $stageLabel = $this->getStageLabel();
        return [
            'article_id'    => $this->article->id,
            'stage'         => $this->stage,
            'tracking_code' => $this->article->tracking_code,
            'message'       => "Naskah '{$this->article->title}' memasuki tahap {$stageLabel}",
            'url'           => url('/author/articles/' . $this->article->id),
        ];
    }

    private function getStageLabel(): string
    {
        return match ($this->stage) {
            'copy_editing'     => 'Copyediting (Penyuntingan Naskah)',
            'layout_editing'   => 'Layout Editing (Tata Letak & Galley)',
            'proofreading'     => 'Proofreading (Pemeriksaan Uji Baca Akhir)',
            'ready_to_publish' => 'Ready to Publish (Siap Terbit)',
            default            => ucwords(str_replace('_', ' ', $this->stage)),
        };
    }
}
