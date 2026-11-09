<?php

namespace App\Notifications;

use App\Models\Article;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ArticleStatusUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public string  $status,
        public ?string $note = null
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
        $statusLabel = $this->getStatusLabel();

        $mail = (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Pembaruan Status Naskah: {$statusLabel}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Terdapat pembaruan status untuk naskah artikel ilmiah Anda:")
            ->line("**{$this->article->title}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Status Baru: **{$statusLabel}**");

        if (!empty($this->note)) {
            $mail->line("Catatan: _{$this->note}_");
        }

        return $mail
            ->action('Lihat Detail Naskah', url('/author/articles/' . $this->article->id))
            ->line('Terima kasih atas kerja samanya.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $statusLabel = $this->getStatusLabel();
        return "Yth. {$notifiable->name}, status naskah '{$this->article->title}' telah diperbarui menjadi: {$statusLabel}. Cek detail di: " . url('/author/articles/' . $this->article->id);
    }

    public function toDatabase(object $notifiable): array
    {
        $statusLabel = $this->getStatusLabel();
        return [
            'article_id'    => $this->article->id,
            'tracking_code' => $this->article->tracking_code,
            'status'        => $this->status,
            'message'       => "Status artikel '{$this->article->title}' diperbarui: {$statusLabel}",
            'url'           => url('/author/articles/' . $this->article->id),
        ];
    }

    private function getStatusLabel(): string
    {
        return match (strtolower($this->status)) {
            'submitted'          => 'Submitted (Telah Dikirimkan)',
            'screening'          => 'Screening (Pemeriksaan Awal)',
            'reviewer_assignment'=> 'Reviewer Assignment (Penugasan Reviewer)',
            'under_review'       => 'Under Review (Sedang Ditinjau Reviewer)',
            'revision_required'  => 'Revision Required (Perlu Revisi)',
            'accepted'           => 'Accepted (Diterima)',
            'rejected'           => 'Rejected (Ditolak)',
            'waiting_payment'    => 'Waiting Payment (Menunggu Pembayaran APC)',
            'waiting_verification'=> 'Waiting Verification (Verifikasi Pembayaran)',
            'paid'               => 'Paid (Pembayaran Terverifikasi)',
            'copy_editing'       => 'Copyediting (Penyuntingan Naskah)',
            'layout_editing'     => 'Layout Editing (Tata Letak)',
            'proofreading'       => 'Proofreading (Uji Baca Akhir)',
            'ready_to_publish'   => 'Ready to Publish (Siap Terbit)',
            'published'          => 'Published (Telah Terbit)',
            default              => ucfirst(str_replace('_', ' ', $this->status)),
        };
    }
}
