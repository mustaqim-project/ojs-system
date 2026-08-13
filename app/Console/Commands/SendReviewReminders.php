<?php

namespace App\Console\Commands;

use App\Models\ReviewAssignment;
use App\Notifications\ReviewReminderNotification;
use Illuminate\Console\Command;

class SendReviewReminders extends Command
{
    protected $signature = 'reviews:send-reminders';
    protected $description = 'Send reminders to reviewers 3 days before due date';

    public function handle(): int
    {
        $reminders = ReviewAssignment::where('status', 'accepted')
            ->whereDate('due_date', now()->addDays(3)->toDateString())
            ->whereNull('reminded_at')
            ->with('reviewer', 'round.submission')
            ->get();

        $count = 0;
        foreach ($reminders as $assignment) {
            if ($assignment->reviewer) {
                $assignment->reviewer->notify(new ReviewReminderNotification($assignment));
                $assignment->update(['reminded_at' => now()]);
                $count++;
                $this->info("Reminder sent to reviewer #{$assignment->reviewer_id}");
            }
        }

        $this->info("Sent {$count} review reminders.");
        return Command::SUCCESS;
    }
}
