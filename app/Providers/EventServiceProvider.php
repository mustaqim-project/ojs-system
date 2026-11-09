<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\SubmissionStageChanged;
use App\Listeners\LogSubmissionStageChanged;
use App\Listeners\SendSubmissionStageNotification;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        SubmissionStageChanged::class => [
            LogSubmissionStageChanged::class,
            SendSubmissionStageNotification::class,
        ],
    ];

    public function boot(): void
    {
        //
    }
}
