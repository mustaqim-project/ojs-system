<?php

namespace App\Console\Commands;

use App\Models\ReviewAssignment;
use App\Notifications\ReviewEscalationNotification;
use Illuminate\Console\Command;

class SendReviewEscalations extends Command
{
    protected $signature = 'reviews:send-escalations';
    protected $description = 'Escalate overdue reviews to editors (2+ days past due)';

    public function handle(): int
    {
        $escalations = ReviewAssignment::where('status', 'accepted')
            ->whereDate('due_date', '<', now()->subDays(2)->toDateString())
            ->whereNull('escalated_at')
            ->with('reviewer', 'round.submission.assignedEditor')
            ->get();

        $count = 0;
        foreach ($escalations as $assignment) {
            $editor = $assignment->round->submission->assignedEditor;
            if ($editor) {
                $editor->notify(new ReviewEscalationNotification($assignment));
            }
            $assignment->update([
                'status'       => 'overdue',
                'escalated_at' => now(),
            ]);
            $count++;
            $this->info("Escalated assignment #{$assignment->id}");
        }

        $this->info("Escalated {$count} overdue reviews.");
        return Command::SUCCESS;
    }
}
