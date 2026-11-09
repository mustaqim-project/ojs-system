<?php

namespace App\Notifications;

use App\Models\Article;
use App\Models\Review;
use App\Models\ReviewAssignment;
use App\Notifications\Channels\FonnteChannel;
use Carbon\Carbon;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewAssignedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Article $article,
        public mixed   $assignment = null
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
        $dueDateStr = $this->getDueDateString();
        $reviewUrl = $this->getReviewUrl($notifiable);

        return (new MailMessage)
            ->subject("[{$this->article->tracking_code}] Undangan / Tugas Review Naskah: {$this->article->title}")
            ->greeting("Yth. {$notifiable->name},")
            ->line("Anda telah ditugaskan sebagai reviewer/penelaah untuk naskah artikel ilmiah berikut:")
            ->line("**{$this->article->title}**")
            ->line("Jurnal: **{$journalTitle}**")
            ->line("Batas Waktu Review (Due Date): **{$dueDateStr}**")
            ->line("Kami sangat menghargai kesediaan Anda untuk menelaah naskah ini demi menjaga kualitas dan integritas akademik publikasi.")
            ->action('Buka Halaman Review', $reviewUrl)
            ->line('Silakan login untuk menerima penugasan dan menyampaikan hasil telaah Anda. Terima kasih atas kontribusi Anda!');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $journalTitle = $this->article->journal->title ?? $this->article->journal->name ?? 'Jurnal Ilmiah';
        $dueDateStr = $this->getDueDateString();
        $reviewUrl = $this->getReviewUrl($notifiable);

        return "Yth. {$notifiable->name},\n\n"
            . "Anda telah ditugaskan sebagai reviewer untuk naskah:\n"
            . "\"{$this->article->title}\"\n"
            . "Jurnal: {$journalTitle}\n"
            . "Batas Waktu: {$dueDateStr}\n\n"
            . "Silakan akses tautan berikut untuk meninjau: {$reviewUrl}";
    }

    public function toDatabase(object $notifiable): array
    {
        $reviewUrl = $this->getReviewUrl($notifiable);
        return [
            'article_id'    => $this->article->id,
            'assignment_id' => $this->assignment?->id,
            'message'       => "Anda ditugaskan menelaah artikel: {$this->article->title}",
            'url'           => $reviewUrl,
        ];
    }

    private function getDueDateString(): string
    {
        if ($this->assignment && !empty($this->assignment->due_date)) {
            try {
                return Carbon::parse($this->assignment->due_date)->translatedFormat('d M Y');
            } catch (\Throwable $e) {}
        }
        return now()->addDays(14)->translatedFormat('d M Y');
    }

    private function getReviewUrl(object $notifiable): string
    {
        if ($this->assignment instanceof Review) {
            return url('/reviewer/reviews/' . $this->assignment->id);
        }

        if ($this->assignment instanceof ReviewAssignment) {
            return url('/reviewer/reviews/' . $this->assignment->id);
        }

        $review = $this->article->reviews()->where('reviewer_id', $notifiable->id ?? 0)->latest()->first();
        if ($review) {
            return url('/reviewer/reviews/' . $review->id);
        }

        return url('/reviewer/reviews');
    }
}
