<?php

namespace App\Notifications;

use App\Models\ReviewAssignment;
use App\Notifications\Channels\FonnteChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewReminderNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public ReviewAssignment $assignment) {}

    public function via(object $notifiable): array
    {
        $channels = ['mail', 'database'];
        if ($notifiable->phone) $channels[] = FonnteChannel::class;
        return $channels;
    }

    public function toMail(object $notifiable): MailMessage
    {
        $article = $this->assignment->round->submission;
        return (new MailMessage)
            ->subject("[REMINDER] Review Due in 3 Days")
            ->greeting("Dear {$notifiable->name},")
            ->line("This is a reminder that your review for '{$article->title}' is due in 3 days.")
            ->line("Due Date: {$this->assignment->due_date->format('d M Y')}")
            ->action('Submit Review', url('/reviewer/reviews/' . $this->assignment->id))
            ->line('Thank you for your timely response.');
    }

    public function toWhatsApp(object $notifiable): string
    {
        $article = $this->assignment->round->submission;
        return "REMINDER: Your review for '{$article->title}' is due in 3 days ({$this->assignment->due_date->format('d M Y')}). Please submit soon: " . url('/reviewer/dashboard');
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'assignment_id' => $this->assignment->id,
            'message' => "Reminder: Review due in 3 days for assignment #{$this->assignment->id}",
            'url' => url('/reviewer/reviews/' . $this->assignment->id),
        ];
    }
}
