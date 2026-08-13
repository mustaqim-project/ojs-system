<?php

namespace App\Listeners;

use App\Events\SubmissionStageChanged;
use App\Services\AuditService;

class LogSubmissionStageChanged
{
    public function handle(SubmissionStageChanged $event): void
    {
        AuditService::log(
            'Submission',
            'stage_changed',
            $event->article,
            ['current_stage' => $event->oldStage],
            ['current_stage' => $event->newStage]
        );
    }
}
