<?php

namespace App\Console\Commands;

use App\Models\Issue;
use Illuminate\Console\Command;

class PublishScheduledIssues extends Command
{
    protected $signature = 'issues:publish-scheduled';
    protected $description = 'Publish scheduled issues that have reached their publication date';

    public function handle(): int
    {
        $scheduledIssues = Issue::where('status', 'scheduled')
            ->where('published_date', '<=', now())
            ->with('articles')
            ->get();

        $count = 0;
        foreach ($scheduledIssues as $issue) {
            $issue->update(['status' => 'published']);

            // Publish all articles in this issue
            foreach ($issue->articles as $article) {
                if ($article->status !== 'published') {
                    $article->update([
                        'status'       => 'published',
                        'published_at' => now(),
                    ]);
                }
            }

            $count++;
            $this->info("Published issue: {$issue->title}");
        }

        $this->info("Published {$count} scheduled issues.");
        return Command::SUCCESS;
    }
}
